<?php

/* thread.php ****************************************
  Changelog
  0224  Sukasa          Removed hack.
  0223  Sukasa          added small threadid==4650 hack for banner (will remove)
  it's near the end of the document, specifically just below $modlink=...
  0222  blackhole89     added support for mark forum read from here
  0221  blackhole89     updating the threadsread table when a logged on user
  uses this
  0220  blackhole89     readded check for forum minpower; this appears
  to have been lost in the process of merging before
 * ************************************************** */

require('lib/common.php');
require('lib/threadpost.php');
$rdmsg = "";
if (!empty($_COOKIE['pstbon'])) {
	header("Set-Cookie: pstbon=" . $_COOKIE['pstbon'] . "; Max-Age=1; Version=1");
	$rdmsg = "<script language=\"javascript\">
	function dismiss()
	{
		document.getElementById(\"postmes\").style['display'] = \"none\";
	}
</script>
	<div id=\"postmes\" onclick=\"dismiss()\" title=\"Click to dismiss.\"><br>
" . "<table class=\"c1\" width=\"100%\" id=\"edit\"><tr class=\"h\"><td class=\"b h\">";
	if ($_COOKIE['pstbon'] >= 1) {
		$rdmsg.="Post Successful<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">Post successful.</td></tr></table></div><br>";
	} else {
		$rdmsg.="Edit Successful<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">Post was edited successfully.</td></tr></table></div>";
	}
}

function timelink($time) {
	global $timeval;
	if ($timeval == $time)
		return " " . timeunits2($time) . " ";
	else
		return " <a href=thread.php?time=$time>" . timeunits2($time) . '</a> ';
}

$page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
if ($page < 0 || $page > 1000000000000000) {
	error("Error", "Invalid page number");
}

$fieldlist = '';
$ufields = array('posts', 'regdate', 'lastpost', 'lastview', 'location', 'rankset', 'title', 'usepic', 'head', 'sign', 'signsep');
foreach ($ufields as $field) {
	$fieldlist.="u.$field u$field,";
}

$ppp = isset($_REQUEST['ppp']) ? (int)$_REQUEST['ppp'] : $loguser['ppp'];
if ($ppp < 0 || $ppp > 1000000000000000) {
	error("Error", "Invalid posts per page number");
}

if (isset($_REQUEST['id'])) {
	$tid = (int)$_REQUEST['id'];
	$viewmode = "thread";
} elseif (isset($_GET['user'])) {
	$uid = (int)$_GET['user'];
	$viewmode = "user";
} elseif (isset($_GET['time'])) {
	$timeval = (int)$_GET['time'];
	$viewmode = "time";
} elseif (isset($_GET['announce'])) {
	$announcefid = (int)$_GET['announce'];
	$viewmode = "announce";
}
// "link" support (i.e., thread.php?pid=999whatever)
elseif (isset($_GET['pid'])) {
	$pid = (int)$_GET['pid'];
	$numpid = $sql->fetchq("SELECT t.id tid FROM posts p LEFT JOIN threads t ON p.thread=t.id WHERE p.id=$pid");
	if (!$numpid) {
		error("Error", "Thread post does not exist. <br> <a href=./>Back to main</a>");
	}
	$isannounce = $sql->resultq("SELECT announce FROM posts WHERE id=$pid");
	if ($isannounce) {
		$pinf = $sql->fetchq("SELECT t.forum fid, t.id tid FROM posts p LEFT JOIN threads t ON p.thread=t.id WHERE p.id=$pid");
		$announcefid = $pinf['fid'];
		$atid = $pinf['tid'];

		$page = floor($sql->resultq("SELECT COUNT(*) FROM threads WHERE announce=1 AND forum=$announcefid AND id>$atid") / $ppp) + 1;
		$viewmode = "announce";
	} else {
		$tid = $sql->resultq("SELECT thread FROM posts WHERE id=$pid");
		$page = floor($sql->resultq("SELECT COUNT(*) FROM posts WHERE thread=$tid AND id<$pid") / $ppp) + 1;
		$viewmode = "thread";
	}
} else {
	error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
}

if ($viewmode == "thread")
	$threadcreator = $sql->resultq("SELECT user FROM threads WHERE id=$tid");
else
	$threadcreator = 0;

$action = '';
$userbar = '';
//$timeval = 0;

$post_c = isset($_POST['c']) ? $_POST['c'] : '';
$act = isset($_POST['action']) ? $_POST['action'] : '';

//Sukasa 2009-14-09: Laid some of the groundwork to allow users to rename their own threads
if (isset($tid) && $log && $post_c == md5($pwdsalt2 . $loguser['pass'] . $pwdsalt) && (can_edit_forum_threads(getforumbythread($tid)) ||
		($loguser['id'] == $threadcreator && $act == "rename" && has_perm('rename-own-thread')))) {

	if ($act == 'stick') {
		$action = ',sticky=1';
	} elseif ($act == 'unstick') {
		$action = ',sticky=0';
	} elseif ($act == 'close') {
		$action = ',closed=1';
	} elseif ($act == 'open') {
		$action = ',closed=0';
	} elseif ($act == 'trash') {
		editthread($tid, '', $trashid, 1);
	} elseif ($act == 'rename') {
		if(!empty($_POST['title'])) {
			$newtitle=stripslashes($_POST['title']);
			$action=",title='".$sql->escape($newtitle)."'";
		}
	} elseif ($act == 'move') {
		editthread($tid, '', $_POST['arg']);
	} else {
		error("Error", "Unknown action.");
	}

	if ($config['log'] >= '2')
		$sql->query("INSERT INTO log VALUES(UNIX_TIMESTAMP(),'" . $_SERVER['REMOTE_ADDR'] . "','$loguser[id]','ACTION: " . addslashes($act . " " . $tid . " " . $_POST['arg']) . "')");
}

checknumeric($_GET['pin']);
checknumeric($_GET['rev']);
//determine string for revision pinning
if ($_GET['pin'] && $_GET['rev'] && has_perm('view-post-history')) {
	$pinstr = "AND (pt2.id<>$_GET[pin] OR pt2.revision<>($_GET[rev]+1)) ";
} else
	$pinstr = "";

if ($viewmode == "thread") {
	if (!$tid)
		$tid = 0;
	$sql->query("UPDATE threads "
			. "SET views=views+1 $action "
			. "WHERE id=$tid");

	$thread = $sql->fetchq("SELECT t.*, NOT ISNULL(p.id) ispoll, p.question, p.multivote, p.changeable, f.title ftitle, t.forum fid" . ($log ? ', r.time frtime' : '') . ' '
			. "FROM threads t LEFT JOIN forums f ON f.id=t.forum "
			. ($log ? "LEFT JOIN forumsread r ON (r.fid=f.id AND r.uid=$loguser[id]) " : '')
			. "LEFT JOIN polls p ON p.id=t.id "
			. "WHERE t.id=$tid AND t.forum IN " . forums_with_view_perm());

	if (!isset($thread['id'])) {
		error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
	}

	if ($thread['ispoll']) {
		if (isset($_GET['act']) && $_GET['act'] == "vote" && $log) {
			$vote = unpacksafenumeric($_GET['vote']);
			if ($vote > -1) {
				if ($thread['multivote']) {
					if ($thread['changeable']) {
						//changeable multivotes toggle
						$res = $sql->query("DELETE FROM pollvotes WHERE user='$loguser[id]' AND id='$vote'");
						if (!$sql->affectedrows())
							$sql->query("REPLACE INTO pollvotes VALUES($vote,$loguser[id])");
					} else
						$sql->query("REPLACE INTO pollvotes VALUES($vote,$loguser[id])");
				} else if ($thread['changeable']) {
					$sql->query("DELETE v FROM pollvotes v LEFT JOIN polloptions o ON o.id=v.id WHERE v.user=$loguser[id] AND o.poll=$tid");
					$sql->query("INSERT INTO pollvotes VALUES($vote,$loguser[id])");
				} else {
					$res = $sql->resultq("SELECT COUNT(*) FROM pollvotes v LEFT JOIN polloptions o ON o.id=v.id WHERE v.user='$loguser[id]' AND o.poll=$tid");
					if (!$res)
						$sql->query("INSERT INTO pollvotes VALUES($vote,$loguser[id])");
				}

				$redir = 'Location: thread.php?';
				if ($pid)
					$redir .= "pid={$pid}#{$pid}";
				else {
					$redir .= 'id=' . $tid;
					if (isset($_REQUEST['page']))
						$redir .= '&page=' . $_REQUEST['page'];
				}
				die(header($redir));
			}
		}
	}

	//append thread's title to page title
	pageheader($thread['title'], $thread['fid']);

	//mark thread as read // 2007-02-21 blackhole89
	if ($log && $thread['lastdate'] > $thread['frtime'])
		$sql->query("REPLACE INTO threadsread VALUES ($loguser[id],$thread[id]," . ctime() . ")");

	//check for having to mark the forum as read too
	if ($log) {
		$readstate = $sql->fetchq("SELECT ((NOT ISNULL(r.time)) OR t.lastdate<'$thread[frtime]') n "
				. "FROM threads t "
				. "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) "
				. "WHERE t.forum=$thread[fid] "
				. "GROUP BY ((NOT ISNULL(r.time)) OR t.lastdate<'$thread[frtime]') ORDER BY n ASC");
		//if $readstate[n] is 1, MySQL did not create a group for threads where ((NOT ISNULL(r.time)) OR t.lastdate<'$thread[frtime]') is 0;
		//thus, all threads in the forum are read. Mark it as such.
		if ($readstate['n'] == 1)
			$sql->query("REPLACE INTO forumsread VALUES ($loguser[id],$thread[fid]," . ctime() . ')');
	}

	//select top revision // 2007-03-08 blackhole89
	$posts = $sql->query("SELECT " . userfields('u', 'u') . ", " . $fieldlist . " p.*, pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.forum tforum "
			. "FROM posts p "
			. "LEFT JOIN threads t ON t.id=p.thread "
			. "LEFT JOIN poststext pt ON p.id=pt.id "
			. "LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr " //SQL barrel roll
			. "LEFT JOIN users u ON p.user=u.id "
			. "WHERE p.thread=$tid AND ISNULL(pt2.id) "
			. "GROUP BY p.id "
			. "ORDER BY p.id "
			. "LIMIT " . (($page - 1) * $ppp) . "," . $ppp);
}elseif ($viewmode == "user") {
	$user = $sql->fetchq("SELECT * "
			. "FROM users "
			. "WHERE id=$uid ");
	//title
	pageheader("Posts by " . ($user['displayname'] ? $user['displayname'] : $user['name']));
	$posts = $sql->query("SELECT " . userfields('u', 'u') . ",$fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, f.private fprivate, t.title ttitle, t.forum tforum "
			. "FROM posts p "
			. "LEFT JOIN poststext pt ON p.id=pt.id "
			. "LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr "
			. "LEFT JOIN users u ON p.user=u.id "
			. "LEFT JOIN threads t ON p.thread=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON c.id=f.cat "
			. "WHERE p.user=$uid AND ISNULL(pt2.id) "
			. "ORDER BY p.id "
			. "LIMIT " . (($page - 1) * $ppp) . "," . $ppp);

	$thread[replies] = $sql->resultq("SELECT count(*) "
			. "FROM posts p "
			. "LEFT JOIN threads t ON p.thread=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON c.id=f.cat "
			. "WHERE p.user=$uid ");
} elseif ($viewmode == "announce") {
	pageheader('Announcements');

	$posts = $sql->query("SELECT " . userfields('u', 'u') . ",$fieldlist p.*, pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, t.title ttitle, t.forum tforum, p.announce isannounce "
			. "FROM posts p "
			. "LEFT JOIN poststext pt ON p.id=pt.id "
			. "LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr " //SQL barrel roll
			. "LEFT JOIN users u ON p.user=u.id "
			. "LEFT JOIN threads t ON p.thread=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON c.id=f.cat "
			. "WHERE p.announce=1 AND t.announce=1 AND ISNULL(pt2.id) GROUP BY pt.id "
			. "ORDER BY p.id DESC "
			. "LIMIT " . (($page - 1) * $ppp) . "," . $ppp);

	$thread['replies'] = $sql->resultq("SELECT count(*) "
					. "FROM posts p "
					. "LEFT JOIN threads t ON p.thread=t.id "
					. "LEFT JOIN forums f ON f.id=t.forum "
					. "LEFT JOIN categories c ON c.id=f.cat "
					. "WHERE p.announce=1 AND t.announce=1  "
			) - 1;
} elseif ($viewmode == "time") {
	$mintime = ctime() - $timeval;

	pageheader('Latest posts');

	$posts = $sql->query("SELECT " . userfields('u', 'u') . ",$fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, f.private fprivate, t.title ttitle, t.forum tforum "
			. "FROM posts p "
			. "LEFT JOIN poststext pt ON p.id=pt.id "
			. "LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr "
			. "LEFT JOIN users u ON p.user=u.id "
			. "LEFT JOIN threads t ON p.thread=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON c.id=f.cat "
			. "WHERE p.date>$mintime AND ISNULL(pt2.id) "
			. "ORDER BY p.date DESC "
			. "LIMIT " . (($page - 1) * $ppp) . "," . $ppp);

	$thread['replies'] = $sql->resultq("SELECT count(*) "
			. "FROM posts p "
			. "LEFT JOIN threads t ON p.thread=t.id "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "LEFT JOIN categories c ON c.id=f.cat "
			. "WHERE p.date>$mintime "
	);
} else
	pageheader();

if (isset($_GET['time'])) {
	$mintime = ctime() - $timeval;

	$posts = $sql->query("SELECT " . userfields('u', 'u') . ",$fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, f.id fid, f.private fprivate, t.title ttitle, t.forum tforum "
		. "FROM posts p "
		. "LEFT JOIN poststext pt ON p.id=pt.id "
		. "LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) $pinstr "
		. "LEFT JOIN users u ON p.user=u.id "
		. "LEFT JOIN threads t ON p.thread=t.id "
		. "LEFT JOIN forums f ON f.id=t.forum "
		. "LEFT JOIN categories c ON c.id=f.cat "
		. "WHERE p.date>$mintime AND ISNULL(pt2.id) "
		. "ORDER BY p.date DESC "
		. "LIMIT " . (($page - 1) * $ppp) . "," . $ppp);

	$thread['replies'] = $sql->resultq("SELECT count(*) "
		. "FROM posts p "
		. "LEFT JOIN threads t ON p.thread=t.id "
		. "LEFT JOIN forums f ON f.id=t.forum "
		. "LEFT JOIN categories c ON c.id=f.cat "
		. "WHERE p.date>$mintime ");
}

if ($thread['replies'] < $ppp) {
	$pagelist = '';
	$pagebr = '';
} else {
	$pagelist = '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">Pages:';
	for ($p = 1; $p <= 1 + floor($thread['replies'] / $ppp); $p++)
		if ($p == $page)
			$pagelist.=" $p";
		elseif ($viewmode == "thread")
			$pagelist.=" <a href=thread.php?id=$tid&page=$p>$p</a>";
		elseif ($viewmode == "user")
			$pagelist.=" <a href=thread.php?user=$uid&page=$p>$p</a>";
		elseif ($viewmode == "time")
			$pagelist.=" <a href=thread.php?time=$timeval&page=$p>$p</a>";
		elseif ($viewmode == "announce")
			$pagelist.=" <a href=thread.php?announce&page=$p>$p</a>";
	$pagebr = '<br>';
	$pagelist.='</div>';
}

if ($viewmode == "thread") {

	$faccess = $sql->fetch($sql->query("SELECT id,private,readonly FROM forums WHERE id=" . (int) $thread['forum']));
	if (can_create_forum_post($faccess)) {
		if (has_perm('override-closed') && $thread['closed'])
			$newreply = "<b><i>Thread closed</i></b> | <a href=\"newreply.php?id=$tid\" class=\"newreply\">New reply</a>";
		elseif ($thread['closed'])
			$newreply = "Thread closed";
		else
			$newreply = "<a href=\"newreply.php?id=$tid\" class=\"newreply\">New reply</a>";
	}
	$poll = '';
	if ($thread['ispoll']) {
		$poll = "<br><table class=\"c1\">
" . "  <tr class=\"n1\">
" . "    <td class=\"b n1\" colspan=3>" . htmlval($thread['question']) . "
";
		$opts = $sql->query("SELECT o.*,(COUNT(*) & (NOT ISNULL(v.user))*1023) c,((NOT ISNULL(w.user))*1) s FROM polloptions o LEFT JOIN pollvotes v ON v.id=o.id LEFT JOIN pollvotes w ON w.user='$loguser[id]' AND w.id=o.id WHERE poll=$tid GROUP BY o.id");
		$total = $sql->resultq("SELECT COUNT(DISTINCT v.user) FROM polloptions o, pollvotes v WHERE o.poll=$tid AND v.id=o.id");
		$mytotal = $log ? $sql->resultq("SELECT COUNT(*) FROM polloptions o, pollvotes v WHERE o.poll=$tid AND v.id=o.id AND v.user='$loguser[id]'") : 0;
		while ($opt = $sql->fetch($opts)) {
			$h = $opt['s'] ? "*" : "";
			$cond = $log && (($thread['multivote'] && !$opt['s']) || $thread['changeable'] || !$mytotal);
			$percentage = ($total != 0 ? min(100, ($opt['c'] / $total) * 100) : 100);
			$color = str_pad(dechex($opt['r']), 2, "0", STR_PAD_LEFT) . str_pad(dechex($opt['g']), 2, "0", STR_PAD_LEFT) . str_pad(dechex($opt['b']), 2, "0", STR_PAD_LEFT);
			$poll .= "<tr class=\"n2\"><td class=\"b n2\" width=200>"
			.($cond ? ("<a href=thread.php?id=$tid&act=vote&vote=".urlencode(packsafenumeric($opt['id'])).">") : "")
			.htmlval($opt['option']).($cond ? "</a>" : "")." $h
			<td class=\"b n3\"><div style=\"width:" . sprintf("%.2f%%", $percentage) . ";background-color:#$color;\"><span style=\"padding-right:10em;\"></span></div></td><td class=\"b n3\" width=60>".sprintf("%.2f%%", $percentage)."</td>";
		}
		$poll.=
				"  <tr class=\"n2\"><td class=\"b sfont\" colspan=3>Multiple voting is " . ($thread['multivote'] ? "" : "not") . " allowed. Changing your vote is " . ($thread['changeable'] ? "" : "not") . " allowed. $total " . ($total == 1 ? "user has" : "users have") . " voted so far.
" . "</table>
";
	}

	$topbot = "<table width=100%><tr>
" . "  <td class=\"nb\"><a href=./>Main</a> - <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> - " . htmlval($thread['title']) . "</td>
" . "  <td class=\"nb\" align=\"right\">
" . "  $newreply
" . "  </td>
" . "</table>
";
}elseif ($viewmode == "user") {
	$topbot = "<table width=100%>
" . "  <td class=\"nb\"><a href=./>Main</a> - Posts by " . userlink($user, "") . "</td>
" . "</table>
";
} elseif ($viewmode == "announce") {
	if (has_perm('create-forum-announcements')) {
		$newreply = "<a href=newthread.php?announce=1>New announcement</a>";
	} else {
		$newreply = "";
	}

	$topbot = "<table width=100%><tr>
" . "  <td class=\"nb\"><a href=./>Main</a> - Announcements</td>
" . "  <td class=\"nb\" align=\"right\">
" . "    $newreply
" . "  </td>
" . "</table>
";
} elseif ($viewmode == "time") {
	$topbot = "<table width=100%>
" . "  <td class=\"nb\"><a href=./>Main</a> - Latest posts</td>
" . "</table>
";
	$timeval = $_GET['time'];
} else {
	noticemsg("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
	pagefooter();
	die();
}


$modlinks = '<br>';
if (isset($tid) &&
		(can_edit_forum_threads($thread['forum']) ||
		($loguser['id'] == $thread['user'] && !$thread['closed'] && has_perm('rename-own-thread')))) {
	$link = "<a href=javascript:submitmod";
	if (can_edit_forum_threads($thread['forum'])) {
		if ($thread['sticky']) {
			$stick = "$link('unstick')>Unstick</a>";
			$stick2 = "$link(\'unstick\')>Unstick</a>";
		} else {
			$stick = "$link('stick')>Stick</a>";
			$stick2 = "$link(\'stick\')>Stick</a>";
		}

		if ($thread['closed']) {
			$close = "| $link('open')>Open</a>";
			$close2 = "| $link(\'open\')>Open</a>";
		} else {
			$close = "| $link('close')>Close</a>";
			$close2 = "| $link(\'close\')>Close</a>";
		}

		if ($thread['forum'] != $trashid) {
			$trash = "| <a href=javascript:submitmod('trash') onclick=\"trashConfirm(event)\">Trash</a> |";
			$trash2 = "| <a href=javascript:submitmod(\'trash\') onclick=\"trashConfirm(event)\">Trash</a> |";
		} else {
			$trash = '| ';
			$trash2 = '| ';
		}

		$edit = "<a href=javascript:showrbox()>Rename</a> | <a href=javascript:showmove()>Move</a>";

		//KAWA: Made it a dropdown list. The change isn't alone in this file, but it's clear where it starts and ends if you want to put this on 2.1+delta.
		$r = $sql->query("SELECT c.title ctitle,c.private cprivate,f.id,f.title,f.cat,f.private FROM forums f LEFT JOIN categories c ON c.id=f.cat ORDER BY c.ord,c.id,f.ord,f.id");
		$fmovelinks = "<select id=\"forumselect\">";
		$c = -1;
		while ($d = $sql->fetch($r)) {
			if (!can_view_forum($d))
				continue;

			if ($d['cat'] != $c) {
				if ($c != -1)
					$fmovelinks .= '</optgroup>';
				$c = $d['cat'];
				$fmovelinks .= '<optgroup label="' . $d['ctitle'] . '">';
			}
			$fmovelinks.="<option value=\"" . $d['id'] . "\"" . ($d['id'] == $thread['forum'] ? " selected=\"selected\"" : "") . ">" . $d['title'] . "</option>";
		}
		$fmovelinks.="</optgroup></select>";
		$fmovelinks = addslashes($fmovelinks);
		$fmovelinks.="<input type=\"submit\" class=\"submit\" id=\"move\" value=\"Submit\" name=\"movethread\" onclick=\"submitmove(movetid());\">";
		$fmovelinks.="<input type=\"button\" class=\"submit\" value=\"Cancel\" onclick=\"hidethreadedit(); return false;\">";

		$opt = "Moderating";
	} else {
		$fmovelinks = "";
		$close = $stick = $trash = "";
		$edit = "<a href=javascript:showrbox()>Rename</a>";
		$opt = "Thread";
	}

	$renamefield = "<input type=\"text\" name=\"title\" id=\"title\" size=60 maxlength=255 value=\"".htmlspecialchars($thread['title'])."\">";
	$renamefield .= "<input type=\"submit\" class=\"submit\" name=\"submit\" value=\"Rename\" onclick=\"submitmod('rename');\">";
	$renamefield .= "<input type=\"button\" class=\"submit\" value=\"Cancel\" onclick=\"hidethreadedit(); return false;\">";
	$renamefield = addcslashes($renamefield, "'"); //because of javascript, single quotes will gum up the works

	echo "<script language=\"javascript\">
function trashConfirm(e) {
    if(confirm(\"Are you sure you want to trash this thread?\"));
    else {
  e.preventDefault();
 }
}
</script>";

	$modlinks = "<form action=\"thread.php\" method=\"post\" name=\"mod\" id=\"mod\">
" . "  <table class=\"c2\"><tr class=\"n2\">
" . "  <td class=\"b n3\">
" . "    <span id=\"moptions\">
" . "    $opt options:
" . "    $stick
" . "    $close
" . "    $trash
" . "    $edit
" . "    </span>
" . "    <span id=\"mappend\">
" . "    </span>
" . "    <span id=\"canceledit\">
" . "    </span>
" . "    <script type=\"text/javascript\">
" . "      function submitmod(act){
" . "        document.getElementById('action').value=act;
" . "        document.getElementById('mod').submit();
" . "      }
" . "      function submitrename(name){
" . "        document.mod.arg.value=name;
" . "        submitmod('rename')
" . "      }
" . "      function submitmove(fid){
" . "        document.mod.arg.value=fid;
" . "        submitmod('move')
" . "      }
" . "      function showrbox(){
" . "        document.getElementById('moptions').innerHTML='Rename thread:';
" . "        document.getElementById('mappend').innerHTML='$renamefield';
" . "        document.getElementById('mappend').style.display = '';
" . "      }
" . "      function showmove(){
" . "        document.getElementById('moptions').innerHTML='Move to: ';
" . "        document.getElementById('mappend').innerHTML='$fmovelinks';
" . "        document.getElementById('mappend').style.display = '';
" . "      }
" . "      function submit_on_return(event,act){
" . "        a=event.keyCode?event.keyCode:event.which?event.which:event.charCode;
" . "        document.mod.action.value=act;
" . "        document.mod.arg.value=document.mod.tmp.value;
" . "        if(a==13) document.mod.submit();
" . "      }
" . "      function hidethreadedit() {
" . "        document.getElementById('moptions').innerHTML = '$opt options: $stick2 $close2 $trash2 $edit';
" . "        document.getElementById('mappend').innerHTML = '<input type=hidden name=tmp style=\'width:80%!important;border-width:0px!important;padding:0px!important\' onkeypress=\"submit_on_return(event,\'rename\')\" value=\"" . addcslashes(htmlentities($thread['title'], ENT_COMPAT | ENT_HTML401, 'UTF-8'), "'") . "\" maxlength=100>';
" . "        document.getElementById('canceledit').style.display = 'none';
" . "     }
" . "     function movetid() {
" . "        var x = document.getElementById('forumselect').selectedIndex;
" . "        document.getElementById('move').innerHTML = document.getElementsByTagName('option')[x].value;
" . "        return document.getElementsByTagName('option')[x].value;
" . "     }
" . "     function renametitle() {
" . "        var x = document.getElementById('title').value;
" . "        document.getElementById('rename').innerHTML = document.getElementsByTagName('input')[x].value;
" . "        return document.getElementsByTagName('input')[x].value;
" . "     }
" . "    </script>
" . "    <input type=hidden id=\"arg\" name=\"arg\" value=\"\" />
" . "    <input type=hidden id=\"id\" name=\"id\" value=\"$tid\" />
" . "    <input type=hidden id=\"action\" name=\"action\" value=\"\" />
" . "    <input type=hidden id=\"c\" name=\"c\" value=" . md5($pwdsalt2 . $loguser['pass'] . $pwdsalt) . " />
" . "  </td>
" . "</table>
" . "</form>
";
}

echo "$topbot$userbar";

if (isset($timeval)) {
	echo "<div style=\"margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block\">
          <a href=forum.php?time=$timeval>By Threads</a> | By Posts</a></div><br>";
	echo '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">' .
			timelink(900) . '|' . timelink(3600) . '|' . timelink(86400) . '|' . timelink(604800)
			. "</div>";
}

echo "$modlinks
" . "$pagelist
" . (isset($poll) ? $poll : '');
while ($post = $sql->fetch($posts)) {
	if (!isset($_GET['time'])) {
	if (isset($post['fid'])) {
		if (!can_view_forum(array('id' => $post['fid'], 'private' => $post['fprivate'])))
			continue;
	}
	}
	if (isset($uid) || isset($timeval)) {
		$pthread['id'] = $post['tid'];
		$pthread['title'] = $post['ttitle'];
	}
	if ($post['id'] != $_GET['pin']) {
		$post['maxrevision'] = $post['revision']; // not pinned, hence the max. revision equals the revision we selected
	} else {
		$post['maxrevision'] = $sql->resultq("SELECT MAX(revision) FROM poststext WHERE id=$_GET[pin]");
	}
	if (isset($thread['forum']) && can_edit_forum_posts($thread['forum']) && $post['id'] == $_GET['pin'])
		$post['deleted'] = false;

	if (isset($_REQUEST['pid'])) {
		if (isset($_COOKIE['pstbon']) && $post['id'] == $_REQUEST['pid'] && $_COOKIE['pstbon'] == "-1") {
			echo $rdmsg;
		}
	}
	echo "<br>
" . threadpost($post);
}


echo "$pagelist$pagebr
" . "<br>";

if (isset($thread['id']) && can_create_forum_post($faccess) && !$thread['closed']) {
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
	$toolbar = posttoolbar();

	if (isset($_COOKIE['pstbon']) && $_COOKIE['pstbon'] >= 1) {
		echo $rdmsg;
	}
	echo "
" . "
" . "<table class=\"c1\">
" . " <form action=newreply.php method=post>
" . "  <tr class=\"h\">
" . "    <td class=\"b h\" colspan=2>Warp Whistle Reply</a></td>
";
	echo "  <input type=\"hidden\" name=name value=\"" . htmlval($loguser['name']) . "\">
" . "  <input type=\"hidden\" name=passenc value=\"" . md5($pwdsalt2 . $loguser['pass'] . $pwdsalt) . "\">
";
	echo "  <tr>
";
		echo "    <td class=\"b n1\" align=\"center\" width=120>Format:</td>
" . "    <td class=\"b n2\"><table><tr class='toolbar'>$toolbar</table>
";

	// TODO: WHERE IS QUOTE TEXT??
	if(!isset($quotetext)) $quotetext = '';

	echo "  <tr>
" . "    <td class=\"b n1\" align=\"center\" width=120>Reply:</td>
" . "    <td class=\"b n2\"><textarea wrap=\"virtual\" name=message id='message' rows=8 cols=80>$quotetext</textarea></td>
" . "  <tr class=\"n1\">
" . "    <td class=\"b\">&nbsp;</td>
" . "    <td class=\"b\">
" . "      <input type=\"hidden\" name=tid value=$tid>
" . "      <input type=\"submit\" class=\"submit\" name=action value=Submit>
" . "      <input type=\"submit\" class=\"submit\" name=action value=Preview>
" . "      <input type=\"checkbox\" name=nolayout id=nolayout value=1 ><label for=nolayout>Disable post layout</label>
";
	if (can_edit_forum_threads($thread['forum']))
		echo "     <input type=\"checkbox\" name=close id=close value=1 ><label for=close>Close thread</label>
                " . (!$thread['sticky'] ? "<input type=\"checkbox\" name=stick id=stick value=1><label for=stick>Stick thread</label>" : "") . "
                " . ($thread['sticky'] ? "<input type=\"checkbox\" name=unstick id=unstick value=1><label for=unstick>Unstick thread</label>" : "") . "
";
	echo "    </td>
" . " </form>
" . "</table><br>
";
}

echo "$userbar$topbot";

pagefooter();
?>
