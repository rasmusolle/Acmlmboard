<?php

//[KAWA] Blocklayouts
function LoadBlocklayouts() {
	global $blocklayouts, $loguser, $log, $sql;
	if (isset($blocklayouts) || !$log)
		return;

	$blocklayouts = array();
	$rBlocks = $sql->query("select * from blockedlayouts where blockee = " . $loguser['id']);
	while ($block = $sql->fetch($rBlocks))
		$blocklayouts[$block['user']] = 1;
}

function threadpost($post, $pthread = '') {
	global $dateformat, $loguser, $sql, $blocklayouts, $config, $signsep;

	$post['head'] = '';
	$post['head'] = str_replace("<!--", "&lt;!--", $post['head']);
	$post['uhead'] = str_replace("<!--", "&lt;!--", $post['uhead']);

	if (isset($post['sign']))
		$post['text'] = $post['head'] . $post['text'] . '____________________' . $post['sign'];
	else
		$post['text'] = $post['head'] . $post['text'];

	$post['ranktext'] = getrank($post['urankset'], $post['uposts']);
	$post['utitle'] = $post['ranktext']
			. ((strlen($post['ranktext']) >= 1) ? "<br>" : "")
			. $post['utitle'];

	//[KAWA] Blocklayouts. Supports user/user ($blocklayouts), per-post ($post[nolayout]) and user/world (token).
	LoadBlockLayouts(); //load the blocklayout data - this is just once per page.
	$isBlocked = $post['nolayout'] || $loguser['blocklayouts'];
	if ($isBlocked)
		$post['usign'] = $post['uhead'] = "";

	if (isset($post['deleted']) && $post['deleted']) {
		$postlinks = "";
		if (can_edit_forum_posts(getforumbythread($post['thread']))) {
			$postlinks.="<a href=\"thread.php?pid=$post[id]&amp;pin=$post[id]&rev=$post[revision]#$post[id]\">Peek</a> | ";
			$postlinks.="<a href=\"editpost.php?pid=" . urlencode(packsafenumeric($post['id'])) . "&amp;act=undelete\">Undelete</a>";
		}

		if ($post['id'])
			$postlinks .= ($postlinks ? ' | ' : '') . "ID: $post[id]";

		$ulink = userlink($post, 'u');
		$text = <<<HTML
<table class="c1"><tr>
	<td class="b n1" style="border-right:0;width:180px">$ulink</td>
	<td class="b n1" style="border-left:0">
		<table width="100%">
			<td class="nb sfont">(post deleted)</td>
			<td class="nb sfont right">$postlinks</td>
		</table>
	</td>
</tr></table>
HTML;
		return $text;
	}

	$postheaderrow = '';
	$threadlink = '';
	$postlinks = '';
	$revisionstr = '';

	// Lazy hacks :3
	if (!isset($post['id'])) $post['id'] = 0;

	if ($pthread)
		$threadlink = ", in <a href=\"thread.php?id=$pthread[id]\">" . htmlval($pthread['title']) . "</a>";

	if (isset($post['id']) && $post['id'])
		$postlinks = "<a href=\"thread.php?pid=$post[id]#$post[id]\">Link</a>";  // headlinks for posts

	if (isset($post['revision']) && $post['revision'] >= 2)
		$revisionstr = " (rev. {$post['revision']} of " . date($dateformat, $post['ptdate']) . " by " . userlink_by_id($post['ptuser']) . ")";

	// I have no way to tell if it's closed (or otherwise impostable (hah)) so I can't hide it in those circumstances...
	if (isset($post['isannounce'])) {
		$postheaderrow = "<tr class=\"h\"><td class=\"b\" colspan=2>" . $post['ttitle'] . "</td></tr>";
	} else if (isset($post['thread']) && $loguser['id'] != 0) {
		$postlinks .= ($postlinks ? ' | ' : '') . "<a href=\"newreply.php?id=$post[thread]&amp;pid=$post[id]\">Reply</a>";
	}

	// "Edit" link for admins or post owners, but not banned users
	if (isset($post['thread']) && can_edit_post($post) && $post['id'])
		$postlinks.=($postlinks ? ' | ' : '') . "<a href=\"editpost.php?pid=$post[id]\">Edit</a>";

	if (isset($post['thread']) && $post['id'] && can_delete_forum_posts(getforumbythread($post['thread'])))
		$postlinks.=($postlinks ? ' | ' : '') . "<a href=\"editpost.php?pid=" . urlencode(packsafenumeric($post['id'])) . "&amp;act=delete\">Delete</a>";

	if ($post['id'])
		$postlinks.=" | ID: $post[id]";

	if (has_perm('view-post-ips'))
		$postlinks.=($postlinks ? ' | ' : '') . "IP: $post[ip]";

	if (isset($post['maxrevision']) && isset($post['thread']) && has_perm('view-post-history') && $post['maxrevision'] > 1) {
		$revisionstr.=" | Go to revision: ";
		for ($i = 1; $i <= $post['maxrevision'];  ++$i)
			$revisionstr.="<a href=\"thread.php?pid=$post[id]&amp;pin=$post[id]&amp;rev=$i#$post[id]\">$i</a> ";
	}

	$tbar1 = (!$isBlocked) ? "topbar" . $post['uid'] . "_1" : "";
	$tbar2 = (!$isBlocked) ? "topbar" . $post['uid'] . "_2" : "";
	$sbar = (!$isBlocked) ? "sidebar" . $post['uid'] : "";
	$mbar = (!$isBlocked) ? "mainbar" . $post['uid'] : "";
	$ulink = userlink($post, 'u');
	$pdate = date($dateformat, $post['date']);
	$text = <<<HTML
<table class="c1" id="{$post['id']}">
	$postheaderrow
	<tr>
		<td class="b n1 $tbar1" style="border-bottom:0; border-right:0; min-width: 180px;" height=17>$ulink</td>
		<td class="b n1 $tbar2" style="border-left:0" width=100%>
			<table width=100%>
				<tr><td class="nb sfont">Posted on $pdate $threadlink $revisionstr</td><td class="nb sfont right">$postlinks</td></tr>
			</table>
		</td>
	</tr><tr valign=top>
		<td class='b n1 sfont $sbar' style="border-top:0;">
HTML;

	$lastpost = ($post['ulastpost'] ? timeunits(time() - $post['ulastpost']) : 'none');
	$picture = ($post['uusepic'] ? "<br><img src=\"userpic/{$post['uid']}\">" : '');

	if ($post['usign']) {
		$signsep = $post['usignsep'] ? '' : '____________________<br>';

		if (!$post['uhead'])
			$post['usign'] = '<br><br><small>' . $signsep . $post['usign'] . '</small>';
		else
			$post['usign'] = '<br><br>' . $signsep . $post['usign'];
	}

	$text .= postfilter($post['utitle']);
	$text .= "$picture
<br>Posts: " . ($post['num'] ? "$post[num]/" : '') . "$post[uposts]
<br>
<br>Since: " . date('Y-m-d', $post['uregdate']) . "
<br>
<br>Last post: $lastpost
<br>Last view: " . timeunits(time() - $post['ulastview']);
			$text .= "</td>
<td class=\"b n2 $mbar\" id=\"post_" . $post['id'] . "\">" . postfilter(amptags($post, $post['uhead']) . $post['text'] . amptags($post, $post['usign'])) . "</td>
</table>";

	return $text;
}
