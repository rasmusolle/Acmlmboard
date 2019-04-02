<?php
require('lib/common.php');

if (!has_perm('ban-users')) noticemsg("Error", "You have no permissions to do this!", true);

$id = (int)$_GET['id'];

$tuser = $sql->fetchp("SELECT group_id FROM users WHERE id = ?",[$id]);
if ((is_root_gid($tuser[$u.'group_id']) || (!can_edit_user_assets($tuser[$u.'group_id']) && $id!=$loguser['id'])) && !has_perm('no-restrictions')) {
	noticemsg("Error", "You have no permissions to do this!", true);
}

if ($uid = $_GET['id']) {
	$numid = $sql->fetchp("SELECT id FROM users WHERE id = ?",[$uid]);
	if (!$numid) noticemsg("Error", "Invalid user ID.", true);
}

$bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE banned = 1");
$defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default` = 1");

$user = $sql->fetchp("SELECT * FROM users WHERE id = ?",[$uid]);

if (isset($_POST['banuser']) && $_POST['banuser'] == "Ban User") {
	if ($_POST['tempbanned'] > 0) {
		$banreason = "Banned until ".date("m-d-y h:i A",time() + ($_POST['tempbanned']));
	} else {
		$banreason = "Banned permanently";
	}
	if ($_POST['title']) {
		$banreason .= ': '.htmlspecialchars($_POST['title']);
	}

	$sql->prepare("UPDATE users SET group_id = ?, title = ?, tempbanned = ? WHERE id = ?",
		[$bannedgroup['id'], $banreason, ($_POST['tempbanned'] > 0 ? ($_POST['tempbanned'] + time()) : 0), $user['id']]);

	redirect("profile.php?id=$user[id]");
} elseif (isset($_POST['unbanuser']) && $_POST['unbanuser'] == "Unban User") {
	if ($user['group_id'] != $bannedgroup['id']) noticemsg("Error", "This user is not a banned user.", true);

	$sql->prepare("UPDATE users SET group_id = ?, title = '', tempbanned = 0 WHERE id = ?", [$defaultgroup['id'],$user['id']]);

	redirect("profile.php?id=$user[id]");
}

if (isset($_GET['unban'])) {
	pageheader('Unban User');
} else {
	pageheader('Ban User');
}

$pagebar = [
	'breadcrumb' => [['href' => './', 'title' => 'Main'], ['href' => "profile.php?id=$uid", 'title' => ($user['displayname'] ? $user['displayname'] : $user['name'])]]
];

if (isset($_GET['unban'])) {
	$pagebar['title'] = 'Unban User';
} else {
	$pagebar['title'] = 'Ban User';
}
RenderPageBar($pagebar);

if (isset($_GET['unban'])) {
	?><br><form action="banmanager.php?id=<?=$uid ?>" method="post" enctype="multipart/form-data"><table class="c1">
		<tr class="h"><td class="b">Unban User</td></tr>
		<tr class="n1"><td class="b n1 center"><input type="submit" class="submit" name="unbanuser" value="Unban User"></td></tr>
	</table></form><br><?php
} else {
	?><br><form action="banmanager.php?id=<?=$uid ?>" method="post" enctype="multipart/form-data">
	<table class="c1">
		<?=catheader('Ban User') ?>
		<tr>
			<td class="b n1 center">Reason:</td>
			<td class="b n2"><input type="text" name="title" class="right"></td>
		</tr><tr>
			<td class="b n1 center">Expires?</td>
			<td class="b n2"><?=fieldselect("tempbanned",0,["600"=>"10 minutes",
				"3600"=>"1 hour","10800"=>"3 hours","86400"=>"1 day","172800"=>"2 days",
				"259200"=>"3 days","604800"=>"1 week","1209600"=>"2 weeks","2419200"=>"1 month",
				"4838400"=>"2 months","0"=>"never"]) ?></td>
		</tr><tr class="n1">
			<td class="b"></td>
			<td class="b"><input type="submit" class="submit" name="banuser" value="Ban User"></td>
		</tr>
	</table></form><br><?php
}

RenderPageBar($pagebar);

pagefooter();