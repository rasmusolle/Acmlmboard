<?php
require('lib/common.php');

needs_login(1);

$_POST['action'] = (isset($_POST['action']) ? $_POST['action'] : null);

if ($act = $_POST['action']) {
	$tid = $_POST['tid'];
	$userid = $loguser['id'];
	$user = $loguser;
} else {
	$user = $loguser;
	$tid = $_GET['id'];
}
checknumeric($tid);

$act = (isset($act) ? $act : null);

$thread = $sql->fetchq('SELECT t.*, f.title ftitle, f.private fprivate, f.readonly freadonly '
		. 'FROM threads t '
		. 'LEFT JOIN forums f ON f.id=t.forum '
		. "WHERE t.id=$tid AND t.forum IN " . forums_with_view_perm());

$threadlink = "<a href=thread.php?id=$tid>Back to thread</a>";
$err = '';
if (!$thread) {
	error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
} else if (!can_create_forum_post(['id' => $thread['forum'], 'private' => $thread['fprivate'], 'readonly' => $thread['readonly']])) {
	$err = "You have no permissions to create posts in this forum!<br>$forumlink";
} elseif ($thread['closed'] && !has_perm('override-closed')) {
	$err = "You can't post in closed threads!<br>$threadlink";
}
if ($act == 'Submit') {
	$lastpost = $sql->fetchq("SELECT `id`,`user`,`date` FROM `posts` WHERE `thread`=$thread[id] ORDER BY `id` DESC LIMIT 1");
	if ($lastpost['user'] == $userid && $lastpost['date'] >= (time() - 86400) && !has_perm('consecutive-posts'))
		$err = "You can't double post until it's been at least one day!<br>$threadlink";
	if ($lastpost['user'] == $userid && $lastpost['date'] >= (time() - $config['secafterpost']) && !has_perm('consecutive-posts'))
		$err = "You must wait $config[secafterpost] seconds before posting consecutively.<br>$threadlink";
	if (strlen(trim($_POST['message'])) == 0)
		$err = "Your post is empty! Enter a message and try again.<br>$threadlink";
	if ($user['regdate'] > (time() - $config['secafterpost']))
		$err = "You must wait {$config['secafterpost']} seconds before posting on a freshly registered account.<br>$threadlink";
}

$topbot = [
	'breadcrumb' => [
		['href' => './', 'title' => 'Main'], ['href' => "forum.php?id={$thread['forum']}", 'title' => $thread['ftitle']],
		['href' => "thread.php?id={$thread['id']}", 'title' => htmlval($thread['title'])]
	],
	'title' => "New reply"
];

$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
if ($pid) {
	checknumeric($pid);  //nice way of adding security, really. int_val doesn't really do it (floats and whatnot), so heh
	$post = $sql->fetchq("SELECT IF(u.displayname='',u.name,u.displayname) name, p.user, pt.text, f.id fid, f.private fprivate, p.thread "
			. "FROM posts p "
			. "LEFT JOIN poststext pt ON p.id=pt.id "
			. "LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) "
			. "LEFT JOIN users u ON p.user=u.id "
			. "LEFT JOIN threads t ON t.id=p.thread "
			. "LEFT JOIN forums f ON f.id=t.forum "
			. "WHERE p.id=$pid AND ISNULL(pt2.id)");

	//does the user have reading access to the quoted post?
	if (!can_view_forum(['id' => $post['fid'], 'private' => $post['fprivate']])) {
		$post['name'] = 'your overlord';
		$post['text'] = "";
	}

	$quotetext = "[quote=\"$post[name]\" id=\"$pid\"]" . str_replace("&", "&amp", $post['text']) . "[/quote]";
}

if ($err) {
	pageheader('New reply', $thread['forum']);
	$topbot['title'] .= ' - Error';
	RenderPageBar($topbot);
	echo '<br>';
	noticemsg("Error", $err);
} elseif ($act == 'Preview' || !$act) {
	if ($act == 'Preview') {
		$_POST['message'] = stripslashes($_POST['message']);
	}

	$post['date'] = time();
	$post['ip'] = $userip;
	$post['num'] = ++$user['posts'];
	if ($act == 'Preview')
		$post['text'] = $_POST['message'];
	else
		$post['text'] = $quotetext;
	if ($log && !$act)
		$pass = md5($pwdsalt2 . $loguser['pass'] . $pwdsalt);
	foreach ($user as $field => $val)
		$post['u' . $field] = $val;
	$post['ulastpost'] = time();

	if ($act == 'Preview') {
		pageheader('New reply', $thread['forum']);
		$topbot['title'] .= ' - Preview';
		RenderPageBar($topbot);
		echo "<br><table class=\"c1\"><tr class=\"h\"><td class=\"b h\" colspan=2>Post preview</table>".threadpost($post);
	} else {
		pageheader('New reply', $thread['forum']);
		RenderPageBar($topbot);
	}
	?><br>
	<form action="newreply.php" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan="2">Reply</td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Format:</td>
				<td class="b n2"><?=posttoolbar() ?></td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Post:</td>
				<td class="b n2">
					<textarea name=message id='message' rows=20 cols=80><?=htmlval($post['text']) ?></textarea>
				</td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b">
					<input type="hidden" name=tid value=<?=$tid ?>>
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
				</td>
			</tr>
		</table>
	</form><?php
}elseif ($act == 'Submit') {
	$user = $sql->fetchq("SELECT * FROM users WHERE id=$userid");
	$user['posts']++;

	$sql->query("UPDATE users SET posts=posts+1,lastpost=" . time() . " WHERE id=$userid");
	$sql->prepare("INSERT INTO posts (user,thread,date,ip,num) VALUES (?,?,?,?,?)",
		[$userid,$tid,time(),$userip,$user['posts']]);
	$pid = $sql->insertid();
	$sql->prepare("INSERT INTO poststext (id,text) VALUES (?,?)",
		[$pid,$_POST['message']]);
	$sql->query("UPDATE threads SET replies=replies+1,lastdate=" . time() . ",lastuser=$userid,lastid=$pid$modext WHERE id=$tid");
	$sql->query("UPDATE forums SET posts=posts+1,lastdate=" . time() . ",lastuser=$userid,lastid=$pid WHERE id=$thread[forum]");

	//2007-02-21 //blackhole89 - nuke entries of this thread in the "threadsread" table
	$sql->query("DELETE FROM threadsread WHERE tid='$thread[id]' AND NOT (uid='$userid')");

	redirect("thread.php?pid=$pid#$pid");
}

echo '<br>';
RenderPageBar($topbot);

pagefooter();