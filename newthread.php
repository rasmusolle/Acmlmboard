<?php
require('lib/common.php');

needs_login(1);

if (isset($_REQUEST['announce'])) { $announce = $_REQUEST['announce']; }
checknumeric($announce);

if (!isset($_POST['action'])) { $_POST['action'] = ''; }
if ($act = $_POST['action']) {
	$fid = $_POST['fid'];
	$userid = $loguser['id'];
	$user = $loguser;
} else {
	$user = $loguser;
	$fid = $_GET['id'];
}
checknumeric($fid);

if ($announce) {
	$type = "announcement";
	$typecap = "Announcement";
} else {
	$type = "thread";
	$typecap = "Thread";
}

if ($announce && $fid == 0)
	$forum = array(
		'id' => 0,
		'readonly' => 1
	);
else
	$forum = $sql->fetchq("SELECT * FROM forums WHERE id=$fid AND id IN " . forums_with_view_perm());

$forumlink = "<a href=forum.php?id=$fid>Back to forum</a>";

if (!$forum)
	error("Error", "Forum does not exist. <br> <a href=./>Back to main</a>");
else if ($announce && !has_perm('create-forum-announcements'))
	$err = "You have no permissions to create announcements in this forum!<br>$forumLink";
else if (!can_create_forum_thread($forum))
	$err = "You have no permissions to create threads in this forum!<br>$forumlink";
else if ($user['lastpost'] > time() - 30 && $act == 'Submit' && !has_perm('ignore-thread-time-limit'))
	$err = "Don't post threads so fast, wait a little longer.<br>$forumlink";
else if ($user['lastpost'] > time() - $config['secafterpost'] && $act == 'Submit' && has_perm('ignore-thread-time-limit'))
	$err = "You must wait ".$config['secafterpost']." seconds before posting a thread.<br>$forumlink";

// 2007-02-19 //blackhole89 - table breach protection
if ($act == 'Submit') {
	if (($tdepth = tvalidate($_POST['message'])) != 0)
		$err = "This post would disrupt the board's table layout! The calculated table depth is $tdepth.<br>$forumlink";
	if (strlen(trim(str_replace(" ", "", $_POST['title']))) < 4)
		$err = "You need to enter a longer $type title.<br>$forumlink";
}

$top = "<a href=./>Main</a> - <a href=forum.php?id=$fid>$forum[title]</a> - New $type";

if (isset($err)) {
	pageheader("New $type", $forum['id']);
	echo "$top - Error";
	noticemsg("Error", $err);
} elseif (!$act) {
	pageheader("New $type", $forum['id']);
	echo $top;
	?>
	<br><br>
	<form action="newthread.php" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan="2"><?=$typecap ?></td>
			</tr>
			<tr>
				<td class="b n1 center"><?=$typecap ?> title:</td>
				<td class="b n2"><input type="text" name=title size=100 maxlength=100></td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Format:</td>
				<td class="b n2"><?=posttoolbar() ?></td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Post:</td>
				<td class="b n2">
					<textarea name=message id='message' rows=20 cols=80></textarea>
				</td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b">
					<input type="hidden" name="fid" value="<?=$fid ?>">
					<input type="hidden" name="announce" value="<?=$announce ?>">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
				</td>
			</tr>
		</table>
	</form>
	<script src="lib/js/tools.js"></script>
	<?php
} elseif ($act == 'Preview') {
	$_POST['title'] = stripslashes($_POST['title']);
	$_POST['message'] = stripslashes($_POST['message']);

	$post['date'] = time();
	$post['ip'] = $userip;
	$post['num'] = ++ $user['posts'];
	$post['text'] = $_POST['message'];
	foreach ($user as $field => $val)
		$post['u' . $field] = $val;
	$post['ulastpost'] = time();

	pageheader("New $type", $forum['id']);
	echo "$top - Preview";
	?><br>
	<table class="c1"><tr class="h"><td class="b h" colspan=2>Post preview</td></tr>
	<?=threadpost($post) ?>
	<br>
	<form action="newthread.php" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan=2><?=$typecap ?></td>
			</tr>
			<tr>
				<td class="b n1 center"><?=$typecap ?> title:</td>
				<td class="b n2"><input type="text" name=title size=100 maxlength=100 value="<?=htmlval($_POST['title']) ?>"></td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Format:</td>
				<td class="b n2"><?=posttoolbar() ?></td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Post:</td>
				<td class="b n2">
					<textarea name=message id='message' rows=20 cols=80><?=htmlval($_POST['message']) ?></textarea>
				</td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b">
					<input type="hidden" name=fid value=<?=$fid ?>>
					<input type="hidden" name="announce" value="<?=$announce ?>">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
				</td>
			</tr>
		</table>
	</form><?php
} elseif ($act == 'Submit') {
	$modclose = "0";
	$modstick = "0";

	$user = $sql->fetchq("SELECT * FROM users WHERE id=$userid");
	$user['posts']++;

	if ($announce) {
		$modclose = $announce;
	}

	$sql->query("UPDATE users SET posts=posts+1,threads=threads+1,lastpost=" . time() . " WHERE id=$userid");
	$sql->prepare("INSERT INTO threads (title,forum,user,lastdate,lastuser,announce,closed,sticky) VALUES (?,?,?,?,?,?,?,?)",
		array($_POST['title'],$fid,$userid,time(),$userid,$announce,$modclose,$modstick));
	$tid = $sql->insertid();
	$sql->prepare("INSERT INTO posts (user,thread,date,ip,num,announce) VALUES (?,?,?,?,?,?)",
		array($userid,$tid,time(),$userip,$user['posts'],$announce));
	$pid = $sql->insertid();
	$sql->prepare("INSERT INTO poststext (id,text) VALUES (?,?)",
		array($pid,$_POST['message']));
	if (!$announce) {
		$sql->query("UPDATE forums SET threads=threads+1,posts=posts+1,lastdate=" . time() . ",lastuser=$userid,lastid=$pid " . "WHERE id=$fid");
	}
	$sql->query("UPDATE threads SET lastid=$pid WHERE id=$tid");

	if ($announce) {
		$viewlink = "thread.php?announce";
		$shortlink = "a=" . $forum['id'];
	} else {
		$viewlink = "thread.php?id=$tid";
		$shortlink = "t=$tid";
	}

	redirect($viewlink);
}

pagefooter();