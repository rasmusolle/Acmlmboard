<?php
require('lib/common.php');
require('lib/threadpost.php');
loadsmilies();

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

if ($act != 'Submit') {
	$posts = $sql->query("SELECT " . userfields('u', 'u') . ",u.posts AS uposts, p.*, pt1.text, t.forum tforum "
			. 'FROM posts p '
			. 'LEFT JOIN threads t ON t.id=p.thread '
			. 'LEFT JOIN poststext pt1 ON p.id=pt1.id '
			. 'LEFT JOIN poststext pt2 ON pt2.id=pt1.id AND pt2.revision=(pt1.revision+1) '
			. 'LEFT JOIN users u ON p.user=u.id '
			. "WHERE p.thread=$tid "
			. "  AND ISNULL(pt2.id) "
			. 'ORDER BY p.id DESC '
			. "LIMIT $loguser[ppp]");
}

$thread = $sql->fetchq('SELECT t.*, f.title ftitle, f.private fprivate, f.readonly freadonly '
		. 'FROM threads t '
		. 'LEFT JOIN forums f ON f.id=t.forum '
		. "WHERE t.id=$tid AND t.forum IN " . forums_with_view_perm());

if ($act != "Submit") {
	echo "<script language=\"javascript\" type=\"text/javascript\" src=\"tools.js\"></script>";
}
$toolbar = posttoolbar();

$threadlink = "<a href=thread.php?id=$tid>Back to thread</a>";
$err = '';
if (!$thread) {
	error("Error", "Thread does not exist. <br> <a href=./>Back to main</a>");
} else if (!can_create_forum_post(array('id' => $thread['forum'], 'private' => $thread['fprivate'], 'readonly' => $thread['readonly']))) {
	$err = "You have no permissions to create posts in this forum!<br>$forumlink";
} elseif ($thread['closed'] && !has_perm('override-closed')) {
	$err = "You can't post in closed threads!<br>$threadlink";
}
if ($act == 'Submit') {
	$lastpost = $sql->fetchq("SELECT `id`,`user`,`date` FROM `posts` WHERE `thread`=$thread[id] ORDER BY `id` DESC LIMIT 1");
	$message = $sql->escape($_POST['message']);
	if ($lastpost['user'] == $userid && $lastpost['date'] >= (ctime() - 86400) && !has_perm('consecutive-posts'))
		$err = "You can't double post until it's been at least one day!<br>$threadlink";
	if ($lastpost['user'] == $userid && $lastpost['date'] >= (ctime() - $config['secafterpost']) && has_perm('consecutive-posts'))
		$err = "You must wait $config[secafterpost] seconds before posting consecutively.<br>$threadlink";
	if (strlen(trim($message)) == 0)
		$err = "Your post is empty! Enter a message and try again.<br>$threadlink";
	if ($user['regdate'] > (ctime() - $config['secafterpost']))
		$err = "You must wait {$config['secafterpost']} seconds before posting on a freshly registered account.<br>$threadlink";
}

$top = '<a href=./>Main</a> '
		. "- <a href=\"forum.php?id={$thread['forum']}\">{$thread['ftitle']}</a> "
		. "- <a href=\"thread.php?id={$thread['id']}\">" . htmlval($thread['title']) . '</a> '
		. '- New reply';

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
	if (!can_view_forum(array('id' => $post['fid'], 'private' => $post['fprivate']))) {
		$post['name'] = 'your overlord';
		$post['text'] = "";
	}

	$quotetext = "[quote=\"$post[name]\" id=\"$pid\"]" . str_replace("&", "&amp", $post['text']) . "[/quote]";
}

if ($err) {
	pageheader('New reply', $thread['forum']);
	echo "$top - Error";
	noticemsg("Error", $err);
} elseif ($act == 'Preview' || !$act) {
	if ($act == 'Preview') {
		$_POST['message'] = stripslashes($_POST['message']);
	}

	$post['date'] = ctime();
	$post['ip'] = $userip;
	$post['num'] = ++$user['posts'];
	if ($act == 'Preview')
		$post['text'] = $_POST['message'];
	else
		$post['text'] = $quotetext;
	if ($log && !$act)
		$pass = md5($pwdsalt2 . $loguser['pass'] . $pwdsalt);
	$post['nolayout'] = (isset($_POST['nolayout']) ? $_POST['nolayout'] : null);
	foreach ($user as $field => $val)
		$post['u' . $field] = $val;
	$post['ulastpost'] = ctime();

	if ($act == 'Preview') {
		pageheader('New reply', $thread['forum']);
		echo "$top - Preview
" . "<br>
" . "<table class=\"c1\">
" . "  <tr class=\"h\">
" . "    <td class=\"b h\" colspan=2>Post preview
" . "</table>
" . threadpost($post, 0) . "
" . "<br>
";
	} else {
		pageheader('New reply', $thread['forum']);
		echo "$top
" . "<br><br>
";
	}
	?>
	<form action="newreply.php" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan="2">Reply</td>
			</tr>
			<tr>
				<td class="b n1" align="center" width=120>Format:</td>
				<td class="b n2"><table><tr><?php echo $toolbar; ?></tr></table></td>
			</tr>
			<tr>
				<td class="b n1" align="center" width=120>Post:</td>
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
					<input type="checkbox" name=nolayout id=nolayout value=1 <?php echo ($post['nolayout'] ? "checked" : ""); ?>><label for=nolayout>Disable post layout</label>
				</td>
			</tr>
		</table><?php
}elseif ($act == 'Submit') {
	checknumeric($_POST['nolayout']);

	$user = $sql->fetchq("SELECT * FROM users WHERE id=$userid");
	$user['posts']++;

	$sql->query("UPDATE users SET posts=posts+1,lastpost=" . ctime() . " WHERE id=$userid");
	$sql->query("INSERT INTO posts (user,thread,date,ip,num,nolayout) "
			. "VALUES ($userid,$tid," . ctime() . ",'$userip',$user[posts],$_POST[nolayout])");
	$pid = $sql->insertid();
	$sql->query("INSERT INTO poststext (id,text) VALUES ($pid,'$message')");
	$sql->query("UPDATE threads SET replies=replies+1,lastdate=" . ctime() . ",lastuser=$userid,lastid=$pid$modext WHERE id=$tid");
	$sql->query("UPDATE forums SET posts=posts+1,lastdate=" . ctime() . ",lastuser=$userid,lastid=$pid WHERE id=$thread[forum]");

	//2007-02-21 //blackhole89 - nuke entries of this thread in the "threadsread" table
	$sql->query("DELETE FROM threadsread WHERE tid='$thread[id]' AND NOT (uid='$userid')");

	redirect("thread.php?pid=$pid#$pid", $c);
}

if ($act != 'Submit' && !$err && can_view_forum($thread)) {
	?><br>
<table class="c1"><tr class="h"><td class="b h" colspan=2>Thread preview</td></tr></table>
<?php
	while ($post = $sql->fetch($posts)) {
		echo threadpost($post, 1);
	}

	if ($thread['replies'] >= $loguser['ppp']) {
		?><br><table class="c1"><tr><td class="b n1">The full thread can be viewed <a href=thread.php?id=<?=$tid ?>>here</a>.</td></table><?php
	}
}

pagefooter();
?>