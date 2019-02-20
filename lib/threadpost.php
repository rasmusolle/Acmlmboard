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

function loadsmilies() {
	global $sql,$smilies;
	$i = 0;
	$s = $sql->query("SELECT * FROM smilies");
	while($smilies[$i++] = $sql->fetch($s));
		$smilies['num'] = $i;
}

function threadpost($post, $type, $pthread = '') {
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

	//[KAWA] TODO: replace with token effect, or preferably just a profile switch
	/*
	  //opaque goggles
	  if ($x_hacks['opaques']) {
	  $post['usign'] = $post['uhead'] = "";
	  }
	 */
	//if($post[nolayout]) {
	//[KAWA] Blocklayouts. Supports user/user ($blocklayouts), per-post ($post[nolayout]) and user/world (token).
	LoadBlockLayouts(); //load the blocklayout data - this is just once per page.
	$isBlocked = $post['nolayout'] || $loguser['blocklayouts'];
	if ($isBlocked)
		$post['usign'] = $post['uhead'] = "";
	//}
	
	if (isset($post['deleted']) && $post['deleted']) {
		$postlinks = "";
		if (can_edit_forum_posts(getforumbythread($post['thread']))) {
			$postlinks.="<a href=\"thread.php?pid=$post[id]&amp;pin=$post[id]&rev=$post[revision]#$post[id]\">Peek</a> | ";
			$postlinks.="<a href=\"editpost.php?pid=" . urlencode(packsafenumeric($post[id])) . "&amp;act=undelete\">Undelete</a>";
		}

		if ($post['id'])
			$postlinks.=($postlinks ? ' | ' : '') . "ID: $post[id]";

		$text = "<table class=\"c1\">
" . "  <tr>
" . "    <td class=\"b n1\" style=border-bottom:0;border-right:0;width:180px height=17>
" . "      " . userlink($post, 'u') . "</td>
" . "    <td class=\"b n1\" style=border-left:0>
" . "      <table width=100%>
" . "        <td class=\"nb sfont\">(post deleted)</td>
" . "        <td class=\"nb sfont\" align=\"right\">$postlinks</td>
" . "      </table>
" . "</table>";
		return $text;
	}

	switch ($type) {
		case 0:
		case 1:
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

				
//2007-03-08 blackhole89
			if (isset($post['revision']) && $post['revision'] >= 2)
				$revisionstr = " (rev. {$post['revision']} of " . cdate($dateformat, $post['ptdate']) . " by " . userlink_by_id($post['ptuser']) . ")";

			// I have no way to tell if it's closed (or otherwise impostable (hah)) so I can't hide it in those circumstances...
			if (isset($post['isannounce'])) {
				$postheaderrow = "<tr class=\"h\">
               <td class=\"b\" colspan=2>" . $post['ttitle'] . "</td>
             </tr>
            ";
			} else if (isset($post['thread']) && $loguser['id'] != 0) {
				$postlinks.=($postlinks ? ' | ' : '') . "<a href=\"newreply.php?id=$post[thread]&amp;pid=$post[id]\">Reply</a>";
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

			if (isset($post['thread']) && can_view_forum_post_history(getforumbythread($post['thread'])) && $post['maxrevision'] > 1) {
				$revisionstr.=" | Go to revision: ";
				for ($i = 1; $i <= $post['maxrevision'];  ++$i)
					$revisionstr.="<a href=\"thread.php?pid=$post[id]&amp;pin=$post[id]&amp;rev=$i#$post[id]\">$i</a> ";
			}

			// if quote enabled then if $postlink2 then postlink2 .= | [quote]
			// 2/22/2007 xkeeper - guess which moron forgot to close the </a>
			//[KAWA] Fun fact: <a name> is deprecated in favor of using IDs.
			//       That's right, you can use <anything id="foo"> in place of <a name="foo">!
			$tbar1 = ($type == 0 && !$isBlocked) ? "topbar" . $post['uid'] . "_1" : "";
			$tbar2 = ($type == 0 && !$isBlocked) ? "topbar" . $post['uid'] . "_2" : "";
			$sbar = ($type == 0 && !$isBlocked) ? "sidebar" . $post['uid'] : "";
			$mbar = ($type == 0 && !$isBlocked) ? "mainbar" . $post['uid'] : "";
			$text = "<table class=\"c1\" id=" . $post['id'] . ">
" . "  $postheaderrow 
" . "  <tr>
" . "    <td class=\"b n1 $tbar1\" style=\"border-bottom:0; border-right:0; min-width: 180px;\" height=17>
" . "      " . userlink($post, 'u') .
					/* " ".gettokenstring($post[uid])."</td> //[KAWA] Removed in favor of profile field
					  ". */ "    </td>
" . "    <td class=\"b n1 $tbar2\" style=\"border-left:0\" width=100%>
" . "      <table width=100%>
" . "       <tr>
" . "        <td class=\"nb sfont\">Posted on " . cdate($dateformat, $post['date']) . "$threadlink$revisionstr</td>
" . "        <td class=\"nb sfont\" align=\"right\">$postlinks</td>
" . "      </table>
" . "  <tr valign=top>
" . "    <td class='b n1 sfont $sbar' style=\"border-top:0;\">
";
			if ($type == 0) {
				$location = ($post['ulocation'] ? '<br>From: ' . postfilter($post['ulocation']) : '');
				$lastpost = ($post['ulastpost'] ? timeunits(ctime() - $post['ulastpost']) : 'none');

				$picture = ($post['uusepic'] ? "<img src=\"userpic/{$post['uid']}\">" : '');

				if ($post['usign']) {
					$signsep = $post['usignsep'] ? '' : '____________________<br>';

					if (!$post['uhead'])
						$post['usign'] = '<br><br><small>' . $signsep . $post['usign'] . '</small>';
					else
						$post['usign'] = '<br><br>' . $signsep . $post['usign'];
				}

				//2/26/2007 xkeeper - making "posts: [[xxx/]]yyy" conditional instead of constant
				$grouplink = grouplink($post['usex'], $post['ugroup_id']);
				$text.=
						$grouplink . "
" . "      " . ((strlen($grouplink)) ? "<br>" : "") . "
" . "      " . postfilter($post['utitle']);
				/* Normal Rendering */
				$text.= "      <br>$picture
" . "      <br>Posts: " . ($post['num'] ? "$post[num]/" : '') . "$post[uposts]
" . "      <br>
" . "      <br>Since: " . cdate('m-d-y', $post['uregdate']) . "
" . "      $location
" . "      <br>
" . "      <br>Last post: $lastpost
" . "      <br>Last view: " . timeunits(ctime() - $post['ulastview']);
			}else {
				$text.="
" . "      Posts: $post[num]/$post[uposts]
";
			}
			$text.=
					"    </td>
" . "    <td class=\"b n2 $mbar\" id=\"post_" . $post['id'] . "\">" . postfilter(amptags($post, $post['uhead']) . $post['text'] . amptags($post, $post['usign'])) . "</td>
" . "</table>
";
	}
	return $text;
}

?>