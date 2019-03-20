<?php

function userlink_by_name($name) {
	global $sql, $config;
	$u = $sql->fetchp("SELECT " . userfields() . " FROM users WHERE UPPER(name)=UPPER(?) OR UPPER(displayname)=UPPER(?)", [$name, $name]);
	if ($u)
		return userlink($u, null);
	else
		return 0;
}

function get_userlink($matches) {
	return userlink_by_id($matches[1]);
}

function get_username_link($matches) {
	$x = str_replace('"', '', $matches[1]);
	$nl = userlink_by_name($x);
	if ($nl)
		return $nl;
	else
		return $matches[0];
}

function get_forumlink($matches) {
	$fl = forumlink_by_id($matches[1]);
	if ($fl)
		return $fl;
	else
		return $matches[0];
}

function get_threadlink($matches) {
	$tl = threadlink_by_id($matches[1]);
	if ($tl)
		return $tl;
	else
		return $matches[0];
}

function securityfilter($msg) {
	$tags = 'script|iframe|embed|object|textarea|noscript|meta|xmp|plaintext|base';
	$msg = preg_replace("'<(/?)({$tags})'si", "&lt;$1$2", $msg);

	$msg = preg_replace('@(on)(\w+\s*)=@si', '$1$2&#x3D;', $msg);

	$msg = preg_replace("'-moz-binding'si", ' -mo<z>z-binding', $msg);
	$msg = str_ireplace("expression", "ex<z>pression", $msg);
	$msg = preg_replace("'filter:'si", 'filter&#58;>', $msg);
	$msg = preg_replace("'javascript:'si", 'javascript&#58;>', $msg);
	$msg = preg_replace("'transform:'si", 'transform&#58;>', $msg);

	return $msg;
}

function makecode($match) {
	$code = htmlspecialchars($match[1]);
	$list = ["\r\n", "[", ":", ")", "_", "@", "-"];
	$list2 = ["<br>", "&#91;", "&#58;", "&#41;", "&#95;", "&#64;", "&#45;"];
	return "<table style=\"width: 90%; min-width: 90%;\"><tr><td class=\"b n3\" style=\"background-color:#444;border:1px solid #444;white-space: PRE;\"><code class=\"prettyprint\">" . str_replace($list, $list2, $code) . "</code></table>";
}

function makeirc($match) {
	$code = htmlspecialchars($match[1]);
	$list = ["\r\n", "[", ":", ")", "_", "@", "-"];
	$list2 = ["<br>", "&#91;", "&#58;", "&#41;", "&#95;", "&#64;", "&#45;"];
	return "<table style=\"width: 90%; min-width: 90%;\"><tr><td class=\"b n3\"><code>" . str_replace($list, $list2, $code) . "</code></table>";
}

function filterstyle($match) {
	$style = $match[2];

	// remove newlines.
	// this will prevent them being replaced with <br> tags and breaking the CSS
	$style = str_replace("\n", '', $style);

	return $match[1] . $style . $match[3];
}

function postfilter($msg) {
	global $smilies, $config, $sql, $swfid;

	//[blackhole89] - [code] tag
	$msg = preg_replace_callback("'\[code\](.*?)\[/code\]'si", 'makecode', $msg);

	//[irc] variant of [code]
	$msg = preg_replace_callback("'\[irc\](.*?)\[/irc\]'si", 'makeirc', $msg);

	$msg = preg_replace_callback("@(<style.*?>)(.*?)(</style.*?>)@si", 'filterstyle', $msg);

	$msg = securityfilter($msg);

	$msg = str_replace("\n", '<br>', $msg);

	for ($i = 0; $i < sizeof($smilies); $i++)
		$msg = str_replace($smilies[$i]['text'], '<img src=' . $smilies[$i]['url'] . ' align=absmiddle alt="' . $smilies[$i]['text'] . '" title="' . $smilies[$i]['text'] . '">', $msg);

	//Relocated here due to conflicts with specific smilies.
	$msg = preg_replace("@(</?(?:table|caption|col|colgroup|thead|tbody|tfoot|tr|th|td|ul|ol|li|div|p|style|link).*?>)\r?\n@si", '$1', $msg);

	$msg = preg_replace("'\[(b|i|u|s)\]'si", '<\\1>', $msg);
	$msg = preg_replace("'\[/(b|i|u|s)\]'si", '</\\1>', $msg);
	$msg = str_replace('[spoiler]', '<span class="spoiler1" onclick=""><span class="spoiler2">', $msg);
	$msg = str_replace('[/spoiler]', '</span></span>', $msg);
	$msg = preg_replace("'\[url\](.*?)\[/url\]'si", '<a href=\\1>\\1</a>', $msg);
	$msg = preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si", '<a href=\\1>\\2</a>', $msg);
	$msg = preg_replace("'\[img\](.*?)\[/img\]'si", '<img src=\\1>', $msg);
	$msg = str_replace('[quote]', '<blockquote><hr>', $msg);
	$msg = str_replace('[/quote]', '<hr></blockquote>', $msg);
	$msg = preg_replace("'\[color=([a-f0-9]{6})\](.*?)\[/color\]'si", '<span style="color: #\\1">\\2</span>', $msg);

	$msg = preg_replace_callback('\'@\"((([^"]+))|([A-Za-z0-9_\-%]+))\"\'si', "get_username_link", $msg);
	//$msg=preg_replace_callback('\'@(("([^"]+)"))\'si',"get_username_link",$msg);
	//$msg=preg_replace_callback('\'@(("([^"]+)")|([A-Za-z0-9_\-%]+))\'si',"get_username_link",$msg); //For Reference. Original no quote @username

	$msg = preg_replace_callback("'\[user=([0-9]+)\]'si", "get_userlink", $msg);
	$msg = preg_replace_callback("'\[forum=([0-9]+)\]'si", "get_forumlink", $msg);
	$msg = preg_replace_callback("'\[thread=([0-9]+)\]'si", "get_threadlink", $msg);
	$msg = preg_replace_callback("'\[username=([[A-Za-z0-9 _\-%]+)\]'si", "get_username_link", $msg);

	$msg = preg_replace("'\[url=(.*?)\](.*?)\[/url\]'si", '<a href=\\1>\\2</a>', $msg);

	$msg = preg_replace("'\[reply=\"(.*?)\" id=\"(.*?)\"\]'si", '<blockquote><span class="quotedby"><small><i><a href=showprivate.php?id=\\2>Sent by \\1</a></i></small></span><hr>', $msg);
	$msg = preg_replace("'\[quote=\"(.*?)\" id=\"(.*?)\"\]'si", '<blockquote><span class="quotedby"><small><i><a href=thread.php?pid=\\2#\\2>Posted by \\1</a></i></small></span><hr>', $msg);
	$msg = preg_replace("'\[quote=(.*?)\]'si", '<blockquote><span class="quotedby"><i>Posted by \\1</i></span><hr>', $msg);
	$msg = preg_replace("'>>([0-9]+)'si", '>><a href=thread.php?pid=\\1#\\1>\\1</a>', $msg);

	//[KAWA] Youtube tag.
	$msg = preg_replace("'\[youtube\]([\-0-9_a-zA-Z]*?)\[/youtube\]'si", '<iframe width="420" height="315" src="http://www.youtube.com/embed/\\1" frameborder="0" allowfullscreen></iframe>', $msg);

	return $msg;
}

function amptags($post, $s) {
	global $sql;
	if (!$post['num'])
		$post['num'] = $post['uposts'];
	$s = str_replace("&postnum&", $post['num'], $s);
	$s = str_replace("&numdays&", floor((time() - $post['uregdate']) / 86400), $s);
	$s = str_replace("&postcount&", $post['uposts'], $s);
	$s = str_replace("&rank&", $post['ranktext'], $s);
	$s = str_replace("&rankname&", preg_replace("'<(.*?)>'si", "", $post['ranktext']), $s);
	$s = str_replace("&postrank&", $sql->result($sql->query("SELECT count(*) FROM users WHERE posts>" . $post['uposts']), 0, 0), $s); //Added by request of Acmlm
	// e modifier is no longer supported... using preg_replace_callback to stop the complaining.
	$replace_callback = function($match) use ($post) {
		return max($match[1] - $post['num'], 0);
	};
	$s = preg_replace_callback('@&(\d+)&@si', $replace_callback, $s);
	return $s;
}

//2007-02-19 //blackhole89 - table depth validation
function tvalidate($str) {
	$l = strlen($str);
	$isquot = 0;
	$istag = 0;
	$isneg = 0;
	$iscomment = 0;
	$params = 0;
	$iscode = 0;
	$t_depth = 0;

	for ($i = 0; $i < $l;  ++$i) {
		if ($iscode) {
			if (!strcasecmp(substr($str, $i, 7), '[/code]'))
				$iscode = 0;
			else
				continue;
		}
		if (!strcasecmp(substr($str, $i, 6), '[code]'))
			$iscode = 1;
		if (($str[$i] == '\"' || $str[$i] == '\'') && $str[$i - 1] != '\\')
			$isquot = !$isquot;
		if ($str[$i] == '<' && !$isquot) {
			$istag = 1;
			$isneg = 0;
			$params = 0;
		} elseif ($str[$i] == '>' && !$isquot)
			$istag = 0;
		if ($str[$i] == '/' && !$isquot && $istag)
			$isneg = 1;
		if (!strcmp(substr($str, $i, 4), "<!--"))
			$iscomment = 1;
		if (!strcmp(substr($str, $i, 3), "-->"))
			$iscomment = 0;
		if ($istag && !$params && !$iscomment && !strcasecmp(substr($str, $i, 5), 'table'))
			$t_depth+=($isneg == 1 ? -1 : 1);
		if ($t_depth < 0)
			return -1;  //disrupture
		if ($istag && !$params && !$iscomment && $t_depth == 0 && !strcasecmp(substr($str, $i, 2), 'td'))
			return -1;  //td on top level
		if ($istag && !$params && !$iscomment && $t_depth == 0 && !strcasecmp(substr($str, $i, 2), 'tr'))
			return -1;  //tr on top level
		if ($istag && $str[$i] != ' ' && $str[$i] != '/' && $str[$i] != '<')
			$params = 1;
	}
	return $t_depth;
}

function htmlval($text) {
	$text = str_replace('&', '&amp;', $text);
	$text = str_replace('<', '&lt;', $text);
	$text = str_replace('"', '&quot;', $text);
	$text = str_replace('>', '&gt;', $text);
	return $text;
}

function forcewrap($text) {
	$l = 0;
	$text2 = '';
	for ($i = 0; $i < strlen($text); $i++) {
		$text2.=$text[$i];
		if ($text[$i] == ' ')
			$l = 0;
		else {
			$l++;
			if (!($l % 30))
				$text2.=' ';
		}
	}
	return $text2;
}

function posttoolbutton($e, $name, $title, $leadin, $leadout, $names = "") {
	if ($names == "")
		$names = $name;
	return "<td class=\"b n3\" id='tbk$names' style='width:16px;text-align:center'><a href=\"javascript:buttonProc('$e','tbk$names','$leadin','$leadout')\"><span style='font-size:9pt'><input type=\"button\" class=\"submit\" title='$title' value='$name' tabindex=\"-1\"></span></a></td>";
}

function posttoolbar() {
	return '<table><tr>'
			. posttoolbutton("message", "B", "Bold", "[b]", "[/b]")
			. posttoolbutton("message", "I", "Italic", "[i]", "[/i]")
			. posttoolbutton("message", "U", "Underline", "[u]", "[/u]")
			. posttoolbutton("message", "S", "Strikethrough", "[s]", "[/s]")
			. "<td class=\"nb n2\">&nbsp;</td>"
			. posttoolbutton("message", "_", "IRC", "[irc]", "[/irc]")
			. posttoolbutton("message", "/", "URL", "[url]", "[/url]")
			. posttoolbutton("message", "!", "Spoiler", "[spoiler]", "[/spoiler]", "sp")
			. posttoolbutton("message", "&#133;", "Quote", "[quote]", "[/quote]", "qt")
			. posttoolbutton("message", ";", "Code", "[code]", "[/code]", "cd")
			. "<td class=\"nb n2\">&nbsp;</td>"
			. posttoolbutton("message", "[]", "IMG", "[img]", "[/img]")
			. posttoolbutton("message", "YT", "YouTube", "[youtube]", "[/youtube]", "yt")
			. '</tr></table>';
}

//[KAWA] Blocklayouts
function LoadBlocklayouts() {
	global $blocklayouts, $loguser, $log, $sql;
	if (isset($blocklayouts) || !$log)
		return;

	$blocklayouts = [];
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

	//[KAWA] Blocklayouts. Supports user/user ($blocklayouts) and user/world (token).
	LoadBlockLayouts(); //load the blocklayout data - this is just once per page.
	$isBlocked = $loguser['blocklayouts'];
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
