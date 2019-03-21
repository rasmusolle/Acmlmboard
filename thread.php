<?php
require('lib/common.php');

$page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
if ($page < 0 || $page > 1000000000000000) {
	error("Error", "Invalid page number");
}

$fieldlist = '';
$ufields = ['posts', 'regdate', 'lastpost', 'lastview', 'location', 'rankset', 'title', 'usepic', 'head', 'sign', 'signsep'];
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
	$time = (int)$_GET['time'];
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

	$thread = $sql->fetchq("SELECT t.*, f.title ftitle, t.forum fid" . ($log ? ', r.time frtime' : '') . ' '
			. "FROM threads t LEFT JOIN forums f ON f.id=t.forum "
			. ($log ? "LEFT JOIN forumsread r ON (r.fid=f.id AND r.uid=$loguser[id]) " : '')
			. "WHERE t.id=$tid AND t.forum IN " . forums_with_view_perm());

	if (!isset($thread['id'])) {
		error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
	}

	//append thread's title to page title
	pageheader($thread['title'], $thread['fid']);

	//mark thread as read // 2007-02-21 blackhole89
	if ($log && $thread['lastdate'] > $thread['frtime'])
		$sql->query("REPLACE INTO threadsread VALUES ($loguser[id],$thread[id]," . time() . ")");

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
			$sql->query("REPLACE INTO forumsread VALUES ($loguser[id],$thread[fid]," . time() . ')');
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
	$user = $sql->fetchq("SELECT * FROM users WHERE id = $uid");

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

	$thread['replies'] = $sql->resultq("SELECT count(*) "
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
	$mintime = time() - $time;

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
			$pagelist.=" <a href=thread.php?time=$time&page=$p>$p</a>";
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

	$topbot = "<table width=100%><tr>
		<td class=\"nb\"><a href=./>Main</a> - <a href=forum.php?id=$thread[forum]>$thread[ftitle]</a> - " . htmlval($thread['title']) . "</td>
		<td class=\"nb right\">$newreply</td></tr></table>";
}elseif ($viewmode == "user") {
	$topbot = "<table width=100%><td class=\"nb\"><a href=./>Main</a> - Posts by ".userlink($user, "")."</td></table>";
} elseif ($viewmode == "announce") {
	if (has_perm('create-forum-announcements'))
		$newreply = "<a href=newthread.php?announce=1>New announcement</a>";
	else
		$newreply = "";

	$topbot = "<table width=100%><tr><td class=\"nb\"><a href=./>Main</a> - Announcements</td><td class=\"nb right\">$newreply</td></tr></table>";
} elseif ($viewmode == "time") {
	$topbot = "";
	$time = $_GET['time'];
} else {
	noticemsg("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
	pagefooter();
	die();
}

$modlinks = '';
if (isset($tid) && (can_edit_forum_threads($thread['forum']) || ($loguser['id'] == $thread['user'] && !$thread['closed'] && has_perm('rename-own-thread')))) {
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

	echo "<script>
function trashConfirm(e) {
	if (confirm(\"Are you sure you want to trash this thread?\"));
	else {
		e.preventDefault();
	}
}
</script>";

	$modlinks = "<form action=\"thread.php\" method=\"post\" name=\"mod\" id=\"mod\">
<table class=\"c1\"><tr class=\"n2\">
	<td class=\"b n3\">
		<span id=\"moptions\">$opt options: $stick $close $trash $edit </span>
		<span id=\"mappend\"></span>
		<span id=\"canceledit\"></span>
		<script>
function submitmod(act){
	document.getElementById('action').value=act;
	document.getElementById('mod').submit();
}
function submitrename(name){
	document.mod.arg.value=name;
	submitmod('rename')
}
function submitmove(fid){
	document.mod.arg.value=fid;
	submitmod('move')
}
function showrbox(){
	document.getElementById('moptions').innerHTML='Rename thread:';
	document.getElementById('mappend').innerHTML='$renamefield';
	document.getElementById('mappend').style.display = '';
}
function showmove(){
	document.getElementById('moptions').innerHTML='Move to: ';
	document.getElementById('mappend').innerHTML='$fmovelinks';
	document.getElementById('mappend').style.display = '';
}
function submit_on_return(event,act){
	a=event.keyCode?event.keyCode:event.which?event.which:event.charCode;
	document.mod.action.value=act;
	document.mod.arg.value=document.mod.tmp.value;
	if(a==13) document.mod.submit();
}
function hidethreadedit() {
	document.getElementById('moptions').innerHTML = '$opt options: $stick2 $close2 $trash2 $edit';
	document.getElementById('mappend').innerHTML = '<input type=hidden name=tmp style=\'width:80%!important;border-width:0px!important;padding:0px!important\' onkeypress=\"submit_on_return(event,\'rename\')\" value=\"" . addcslashes(htmlentities($thread['title'], ENT_COMPAT | ENT_HTML401, 'UTF-8'), "'") . "\" maxlength=100>';
	document.getElementById('canceledit').style.display = 'none';
}
function movetid() {
	var x = document.getElementById('forumselect').selectedIndex;
	document.getElementById('move').innerHTML = document.getElementsByTagName('option')[x].value;
	return document.getElementsByTagName('option')[x].value;
}
function renametitle() {
	var x = document.getElementById('title').value;
	document.getElementById('rename').innerHTML = document.getElementsByTagName('input')[x].value;
	return document.getElementsByTagName('input')[x].value;
}
		</script>
		<input type=hidden id=\"arg\" name=\"arg\" value=\"\" />
		<input type=hidden id=\"id\" name=\"id\" value=\"$tid\" />
		<input type=hidden id=\"action\" name=\"action\" value=\"\" />
		<input type=hidden id=\"c\" name=\"c\" value=" . md5($pwdsalt2 . $loguser['pass'] . $pwdsalt) . " />
	</td>
</table>
</form>";
}

echo $topbot;

if (isset($time)) {
	?><table class="c1" style="width:auto">
		<tr class="h"><td class="b">Latest Posts</td></tr>
		<tr><td class="b n1 center">
			<a href="forum.php?time=<?=$time ?>">By Threads</a> | By Posts</a><br><br>
			<?=timelink(900,'thread').' | '.timelink(3600,'thread').' | '.timelink(86400,'thread').' | '.timelink(604800,'thread') ?>
		</td></tr>
	</table><?php
}

echo "$modlinks $pagelist";

if ($sql->numrows($posts) < 1) echo '<br>';
if_empty_query($posts, "No posts were found.", 0, true);

while ($post = $sql->fetch($posts)) {
	if (!isset($_GET['time'])) {
	if (isset($post['fid'])) {
		if (!can_view_forum(['id' => $post['fid'], 'private' => $post['fprivate']]))
			continue;
	}
	}
	if (isset($uid) || isset($time)) {
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

	echo "<br>".threadpost($post);
}

echo "$pagelist$pagebr" . (!isset($time) ? '<br>' : '');

if (isset($thread['id']) && can_create_forum_post($faccess) && !$thread['closed']) {
	echo "<script src=\"lib/js/tools.js\"></script>";
	?><table class="c1">
<form action="newreply.php" method="post">
	<tr class="h"><td class="b h" colspan=2>Warp Whistle Reply</a></td>
	<tr>
		<td class="b n1 center" width=120>Format:</td>
		<td class="b n2"><?=posttoolbar() ?></td>
	</tr><tr>
		<td class="b n1 center" width=120>Reply:</td>
		<td class="b n2"><textarea wrap="virtual" name="message" id="message" rows=8 cols=80></textarea></td>
	</tr><tr class="n1">
		<td class="b"></td>
		<td class="b">
			<input type="hidden" name="tid" value="<?=$tid ?>">
			<input type="submit" class="submit" name="action" value="Submit">
			<input type="submit" class="submit" name="action" value="Preview">
		</td>
	</tr>
</form></table><br>
<?php
}

echo $topbot;

pagefooter();