<?php
require('lib/common.php');

//Alternative to editing users' profiles. - SquidEmpress
//Based off of banhammer.php from Blargboard by StapleButter.

$uid = $loguser['id'];

if (isset($_GET['id'])) {
	$temp = $_GET['id'];
	if (checknumeric($temp))
		$uid = $temp;
}

if (!has_perm('ban-users')) {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
}

//From editperms.php
$id = (int)$_GET['id'];

$tuser = $sql->fetchp("SELECT `group_id` FROM users WHERE id=?",array($id));
if ((is_root_gid($tuser[$u.'group_id']) || (!can_edit_user_assets($tuser[$u.'group_id']) && $id!=$loguser['id'])) && !has_perm('no-restrictions')) {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
}

if ($uid = $_GET['id']) {
	checknumeric($uid);
	$numid = $sql->fetchq("SELECT `id` FROM `users` WHERE `id`='$uid'");
	if (!$numid) error("Error", "Invalid user ID.");
}

$bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE `banned`=1");
$defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default`=1");

global $user;

$user = $sql->fetchq("SELECT * FROM users WHERE `id` = $uid");

//Concatenation like in ABXD
if (isset($_POST['banuser']) && $_POST['banuser'] == "Ban User") {
	$tempban = time() + ($_POST['tempbanned']);
	$tempban = "Banned until " . date("m-d-y h:i A",$tempban);
	if ($_POST['tempbanned'] > 0) {
		$banreason = $tempban;
		if ($_POST['title']) {
			$banreason .= ': '.htmlspecialchars($_POST['title']);
		}
	} else {
		$banreason = "Banned permanently";
		if ($_POST['title']) {
			$banreason .= ': ' . htmlspecialchars($_POST['title']);
		}
	}

	$sql->query("UPDATE users SET group_id='$bannedgroup[id]' WHERE id='$user[id]'");
	$sql->query("UPDATE users SET title='$banreason' WHERE id='$user[id]'");
	$sql->query("UPDATE users SET tempbanned='" . ($_POST['tempbanned'] > 0 ? ($_POST['tempbanned'] + time()) : 0) . "' WHERE id='$user[id]'");

	redirect("profile.php?id=$user[id]");
	die(pagefooter());
} elseif (isset($_POST['unbanuser']) && $_POST['unbanuser'] == "Unban User") {
	if ($user['group_id'] != $bannedgroup['id']) {
		error("Error", "This user is not a Banned User.<br> <a href=./>Back to main</a> ");
	}

	$sql->query("UPDATE users SET group_id='$defaultgroup[id]' WHERE id='$user[id]'");
	$sql->query("UPDATE users SET title='' WHERE id='$user[id]'");
	$sql->query("UPDATE users SET tempbanned='0' WHERE id='$user[id]'");

	redirect("profile.php?id=$user[id]");
	die(pagefooter());
}

if (isset($_GET['unban'])) {
	pageheader('Unban User');
} else {
	pageheader('Ban User');
}

if (isset($_GET['unban'])) {
	$pagebar = array(
		'breadcrumb' => array(array('href'=>'/.', 'title'=>'Main')),
		'title' => 'Unban User',
		'actions' => array(),
		'message' => (isset($errmsg) ? $errmsg : ''));
} else {
	$pagebar = array(
		'breadcrumb' => array(array('href'=>'/.', 'title'=>'Main')),
		'title' => 'Ban User',
		'actions' => array(),
		'message' => (isset($errmsg) ? $errmsg : ''));
}
RenderPageBar($pagebar);

if (isset($_GET['unban'])) {
	?><form action="banmanager.php?id=<?=$uid ?>" method="post" enctype="multipart/form-data"><table class="c1">
		<tr class="h"><td class="b">Unban User</td></tr>
		<tr class="n1"><td class="b n1 center"><input type="submit" class="submit" name="unbanuser" value="Unban User"></td></tr>
	</table><?php
} else {
	?><form action="banmanager.php?id=<?=$uid ?>" method="post" enctype="multipart/form-data">
	<table class="c1">
		<?=catheader('Ban User') ?>
		<tr>
			<td class="b n1 center">Reason:</td>
			<td class="b n2"><input type="text" name="title" class="right"></td>
		</tr><tr>
			<td class="b n1 center">Expires?</td>
			<td class="b n2"><?=fieldselect("tempbanned",0,array("600"=>"10 minutes",
				"3600"=>"1 hour","10800"=>"3 hours","86400"=>"1 day","172800"=>"2 days",
				"259200"=>"3 days","604800"=>"1 week","1209600"=>"2 weeks","2419200"=>"1 month",
				"4838400"=>"2 months","0"=>"never")) ?></td>
		</tr><tr class="n1">
			<td class="b"></td>
			<td class="b"><input type="submit" class="submit" name="banuser" value="Ban User"></td>
		</tr>
	</table><?php
}

pagefooter();