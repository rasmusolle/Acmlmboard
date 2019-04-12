<?php
require('lib/common.php');

needs_login(1);

$topbot = [
	'breadcrumb' => [['href' => './', 'title' => 'Main'], ['href' => "private.php", 'title' => 'Private messages']],
	'title' => 'Send'
];

if (!has_perm('create-pms')) noticemsg("Error", "You have no permissions to do this!", true);

if (!isset($_POST['action'])) {
	$userto = '';
	if (isset($_GET['pid']) && $pid = $_GET['pid']) {
		$post = $sql->fetchp("SELECT IF(u.displayname = '',u.name,u.displayname) name, p.title, p.text "
			."FROM pmsgs p LEFT JOIN users u ON p.userfrom = u.id "
			."WHERE p.id = ?" . (!has_perm('view-user-pms') ? " AND (p.userfrom=".$loguser['id']." OR p.userto=".$loguser['id'].")" : ''), [$pid]);
		if ($post) {
			$quotetext = '[reply="'.$post['name'].'" id="'.$pid.'"]'.$post['text'].'[/quote]' . PHP_EOL;
			$title = 'Re:' . $post['title'];
			$userto = $post['name'];
		}
	}

	if (isset($_GET['uid']) && $uid = $_GET['uid']) {
		$userto = $sql->resultp("SELECT IF(displayname = '',name,displayname) name FROM users WHERE id = ?", [$uid]);
	} elseif (!isset($userto)) {
		$userto = $_POST['userto'];
	}

	pageheader('Send private message');
	RenderPageBar($topbot);
	?><br>
	<form action="sendprivate.php" method="post">
		<table class="c1">
			<tr class="h"><td class="b h" colspan="2">Send message</td></tr>
			<tr>
				<td class="b n1 center" width="120">Send to:</td>
				<td class="b n2"><input type="text" name="userto" size="25" maxlength=25 value="<?=htmlval($userto) ?>"></td>
			</tr><tr>
				<td class="b n1 center">Title:</td>
				<td class="b n2"><input type="text" name="title" size="80" maxlength="255" value="<?=htmlval((isset($title) ? $title : '')) ?>"></td>
			</tr><tr>
				<td class="b n1 center" width="120">Format:</td>
				<td class="b n2"><?=posttoolbar() ?></td>
			</tr><tr>
				<td class="b n1 center"></td>
				<td class="b n2"><textarea name="message" id="message" rows="20" cols="80"><?=htmlval((isset($quotetext) ? $quotetext : '')) ?></textarea></td>
			</tr><tr>
				<td class="b n1"></td>
				<td class="b n1">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
				</td>
			</tr>
		</table>
	</form>
	<?php
} elseif ($_POST['action'] == 'Preview') {
	$_POST['title'] = stripslashes($_POST['title']);
	$_POST['message'] = stripslashes($_POST['message']);

	$post['date'] = time();
	$post['ip'] = $userip;
	$post['num'] = 0;
	$post['text'] = $_POST['message'];
	foreach ($loguser as $field => $val)
		$post['u' . $field] = $val;
	$post['ulastpost'] = time();

	pageheader('Send private message');
	$topbot['title'] .= ' - Preview';
	RenderPageBar($topbot);
	?><br>
	<table class="c1"><tr class="h"><td class="b h" colspan=2>Message preview</td></tr></table>
	<?=threadpost($post) ?>
	<br>
	<form action="sendprivate.php" method="post">
		<table class="c1">
			<tr class="h"><td class="b h" colspan="2">Send message</td></tr>
			<tr>
				<td class="b n1 center" width="120">Send to:</td>
				<td class="b n2"><input type="text" name="userto" size=25 maxlength=25 value="<?=htmlval((isset($_POST['userto']) ? $_POST['userto'] : '')) ?>"></td>
			</tr><tr>
				<td class="b n1 center">Title:</td>
				<td class="b n2"><input type="text" name="title" size="80" maxlength="255" value="<?=htmlval($_POST['title']) ?>"></td>
			</tr><tr>
				<td class="b n1 center" width="120">Format:</td>
				<td class="b n2"><?=posttoolbar() ?></td>
			</tr><tr>
				<td class="b n1 center">Post:</td>
				<td class="b n2"><textarea name="message" id="message" rows="20" cols="80"><?=htmlval($_POST['message']) ?></textarea></td>
			</tr><tr>
				<td class="b n1"></td>
				<td class="b n1">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
				</td>
			</tr>
		</table>
	</form>
	<?php
} elseif ($_POST['action'] == 'Submit') {
	$userto = $sql->resultp("SELECT id FROM users WHERE name LIKE ? OR displayname LIKE ?", [$_POST['userto'], $_POST['userto']]);

	if ($userto && $_POST['message']) {
		$recentpms = $sql->prepare("SELECT date FROM pmsgs WHERE date >= (UNIX_TIMESTAMP()-30) AND userfrom = ?", [$loguser['id']]);
		$secafterpm = $sql->prepare("SELECT date FROM pmsgs WHERE date >= (UNIX_TIMESTAMP() - $config[secafterpost]) AND userfrom = ?", [$loguser['id']]);
		if (($sql->numrows($recentpms) > 0) && (!has_perm('consecutive-posts'))) {
			$msg = "You can't send more than one PM within 30 seconds!";
		} else if (($sql->numrows($secafterpm) > 0) && (has_perm('consecutive-posts'))) {
			$msg = "You can't send more than one PM within ".$config['secafterpost']." seconds!";
		} else {
			$sql->prepare("INSERT INTO pmsgs (date,ip,userto,userfrom,title,text) VALUES (?,?,?,?,?,?)",
				[time(),$userip,$userto,$loguser['id'],$_POST['title'],$_POST['message']]);

			redirect("private.php");
		}
	} elseif (!$userto) {
		$msg = "That user doesn't exist!<br>Go back or <a href=sendprivate.php>try again</a>";
	} elseif (!$_POST['message']) {
		$msg = "You can't send a blank message!<br>Go back or <a href=sendprivate.php>try again</a>";
	}

	pageheader('Send private message');
	$topbot['title'] .= ' - Error';
	RenderPageBar($topbot);
	echo '<br>';
	noticemsg("Error", $msg);
}

echo '<br>';
RenderPageBar($topbot);

pagefooter();