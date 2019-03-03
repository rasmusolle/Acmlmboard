<?php
require("lib/common.php");

needs_login(1);

$targetuserid = $loguser['id'];
$act = isset($_POST['action']) ? $_POST['action'] : '';

if (isset($_GET['id'])) {
	$temp = $_GET['id'];
	if (checknumeric($temp))
		$targetuserid = $temp;
}

if (!can_edit_user($targetuserid)) {
	$targetuserid = 0;
}

if ($targetuserid == 0) {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
}

if (has_perm('no-restrictions'))
	$blockroot = "";
else
	$blockroot = " AND `default` >= 0 ";

$allgroups = $sql->query("SELECT * FROM `group` WHERE `primary`=1 $blockroot ORDER BY sortorder ASC");
$listgroup = array();

while ($group = $sql->fetch($allgroups)) {
	$listgroup[$group['id']] = $group['title'];
}

$token = md5($pwdsalt2 . $loguser['pass'] . $pwdsalt);
if ($act == 'Edit profile') {
	if ($_POST['token'] !== $token) die('No.');

	if ($_POST['pass'] != '' && $_POST['pass'] == $_POST['pass2'] && $targetuserid == $loguser['id'])
		setcookie('pass', packlcookie(md5($pwdsalt2 . $_POST['pass'] . $pwdsalt)), 2147483647);
}

global $user;

$user = $sql->fetchq("SELECT * FROM users WHERE `id` = $targetuserid");

if (!$user) {
	?><table class="c1"><td class="b n1 center">
		This user doesn't exist!<br><a href="./">Back to main</a>
	</table><?php
	die(pagefooter());
}

if ($act == 'Edit profile') {
	$error = '';

	if ($_POST['pass'] && $_POST['pass2'] && $_POST['pass'] != $_POST['pass2'])
		$error = "- The passwords you entered don't match.<br />";

	$usepic = 'usepic';
	$fname = $_FILES['picture'];
	if ($fname['size'] > 0) {
		$ftypes = array("png","jpeg","jpg","gif");
		$img_data = getimagesize($fname['tmp_name']);
		$err = "";
		if ($img_data[0] > $avatardimx)
			$err .= "<br>Too wide.";
		if ($img_data[1] > $avatardimy)
			$err .= "<br>Too tall.";
		if ($fname['size'] > $avatarsize)
			$err .= "<br>Filesize limit of $avatarsize bytes exceeded.";
		if (!in_array(str_replace("image/","",$img_data['mime']),$ftypes))
			$err = "Invalid file type.";
		
		if ($err != "")
			$ava_out = $err;
		else {
			if (move_uploaded_file($fname['tmp_name'], "userpic/$user[id]")) {
				$ava_out = "OK!";
			} else {
				$ava_out = "<br>Error creating file.";
			}
		}

		if ($ava_out != "OK!") {
			$error .= $ava_out;
		} else
			$usepic = "usepic+1";
	}
	if (isset($_POST['picturedel']))
		$usepic = 0;

	//check for table breach
	if (tvalidate($_POST['head'] . $_POST['sign']) != 0) {
		$error .= "- Table tag count mismatch in post layout.<br />";
	}
	if (tvalidate($_POST['title']) != 0) {
		$error .= "- Table tag count mismatch in custom title.<br />";
	}

	if ($_POST['fontsize'] < 30) $_POST['fontsize'] = 30;
	if ($_POST['fontsize'] > 999) $_POST['fontsize'] = 999;
	if ($_POST['sex'] < 0) $_POST['sex'] = 0;
	if ($_POST['sex'] > 2) $_POST['sex'] = 2;

	$pass = $_POST['pass'];
	if (!strlen($_POST['pass2']))
		$pass = "";

	//Validate birthday values.
	if (!$_POST['birthM'] || !$_POST['birthD']) //Reject if any are missing.
		$birthday = -1;
	else {
		if (!is_numeric($_POST['birthM']) || !is_numeric($_POST['birthD'])) //Reject if not numeric.
			$birthday = -1;
	}
	if ($_POST['birthM'] > 12 || $_POST['birthD'] > 31) // fixes a small bug where if the fields are above a certain value, the profile fails to load
		$birthday = -1;
	$year = $_POST['birthY'];
	if (!$_POST['birthY'] || !is_numeric($_POST['birthY']))
		$year = -1;
	if ($birthday != -1 && $_POST['birthM'] != "" && $_POST['birthD'] != "")
		$birthday = str_pad($_POST['birthM'], 2, "0", STR_PAD_LEFT) . '-' . str_pad($_POST['birthD'], 2, "0", STR_PAD_LEFT) . '-' . $year;
	else
		$birthday = -1;

	$dateformat = $_POST['dateformat'];
	$timeformat = $_POST['timeformat'];

	if (has_perm("edit-users")) {
		$targetgroup = $_POST['group_id'];
		checknumeric($targetgroup);
		if (!isset($listgroup[$targetgroup]))
			$targetgroup = 0;

		if (!can_edit_group_assets($targetgroup) && $targetgroup != $loguser['group_id']) {
			$error .= "- You do not have the permissions to assign this group.<br />";
		}
		$targetname = $_POST['name'];

		if ($sql->resultq("SELECT COUNT(`name`) FROM `users` WHERE (`name` = '$targetname' OR `displayname` = '$targetname') AND `id` != $user[id]")) {
			$error .= "- Name already in use.<br />";
		}
	}
	if (checkcdisplayname($targetuserid)) {
		//Checks Displayname to name and other displaynames
		$targetdname = $_POST['displayname'];

		if (checkcdisplayname($targetuserid) && $targetdname != "") {
			if ($sql->resultq("SELECT COUNT(`name`) FROM `users` WHERE (`name` = '$targetdname' OR `displayname` = '$targetdname') AND `id` != $user[id]")) {
				$error .= "- Displayname already in use.<br />";
			}
		}
	}

	if (checkcusercolor($targetuserid)) {
		//Validate Custom username color is a 6 digit hex RGB color
		$custom_usercolor = $_POST['nick_color'];

		if ($custom_usercolor != "") {
			if (!preg_match('/^([A-Fa-f0-9]{6})$/', $custom_usercolor)) {
				$error .= "- Custom usercolor is not a valid RGB hex color.<br />";
			}
		}
	}

	if (!$error) {
		if (has_perm("edit-users")) {
			$banreason = '';
			if($_POST['title']) $banreason = "`title` = 'Banned permanently: {$_POST['title']}', ";
			else $banreason = "`title` = 'Banned permanently', ";

			$sql->query("UPDATE users SET "
				. ($targetgroup ? "`group_id` = $targetgroup, " : "")
				. (isset($_POST['permaban']) ? "`tempbanned` = '0', $banreason" : "")
				. "`name` = '$targetname'"
				. " WHERE `id`=$user[id]"
			);
		}
	}

	if (!$error) {
		$sql->query('UPDATE users SET '
			. ($pass ? 'pass="' . md5($pwdsalt2 . $pass . $pwdsalt) . '",' : '')
			. (checkcdisplayname($targetuserid) ? (setfield('displayname') . ',') : '')
			. (checkcusercolor($targetuserid) ? (setfield('nick_color') . ',') : '')
			. (checkcusercolor($targetuserid) ? (setfield('enablecolor') . ',') : '')
			. setfield('sex') . ','
			. setfield('ppp') . ','
			. setfield('tpp') . ','
			. setfield('signsep') . ','
			. setfield('longpages') . ','
			. setfield('rankset') . ','
			. (checkctitle($targetuserid) && !isset($_POST['permaban']) ? (setfield('title') . ',') : '')
			. setfield('realname') . ','
			. setfield('location') . ','
			. setfield('email') . ','
			. setfield('head') . ','
			. setfield('sign') . ','
			. setfield('bio') . ','
			. setfield('fontsize') . ','
			. setfield('theme') . ','
			. setfield('blocklayouts') . ','
			. setfield('emailhide') . ','
			. setfield('timezone') . ','
			. "birth='$birthday',"
			. "usepic=$usepic,"
			. "dateformat='$dateformat',"
			. "timeformat='$timeformat' "
			. "WHERE `id`=$user[id]"
		);

		redirect("profile.php?id=$user[id]", "Profile was edited successfully.");

		if ($config['log'] >= '1')
			$sql->query("INSERT INTO log VALUES(UNIX_TIMESTAMP(),'" . $_SERVER['REMOTE_ADDR'] . "','$loguser[id]','ACTION: " . addslashes("user edit " . $targetuserid) . "')");

		die(pagefooter());
	} else {
		noticemsg("Error", "Couldn't save the profile changes. The following errors occured:<br><br>" . $error);

		$act = '';
		foreach ($_POST as $k => $v)
			$user[$k] = $v;
		$user['birth'] = $birthday;
	}
}

pageheader('Edit profile');

if (empty($act)) {
	$listtimezones = array();
	foreach (timezone_identifiers_list() as $tz) {
		$listtimezones[$tz] = $tz;
	}

	$birthM = ''; $birthD = ''; $birthY = '';
	if ($user['birth'] != -1) {
		$birthday = explode('-', $user['birth']);
		$birthM = $birthday[0]; $birthD = $birthday[1]; $birthY = $birthday[2];
	}

	$passinput = "<input type=\"password\" name=pass size=13 maxlength=32> / Retype: <input type=\"password\" name=pass2 size=13 maxlength=32>";
	$birthinput = "
Month: <input type=\"text\" name=birthM size=2 maxlength=2 value=$birthM>
Day: <input type=\"text\" name=birthD size=2 maxlength=2 value=$birthD>
Year: <input type=\"text\" name=birthY size=4 maxlength=4 value=$birthY>";
	//http://jscolor.com/try.php
	$colorinput = "<script src=lib/js/jscolor.js></script><input type=\"text\" name=nick_color class=jscolor value=" . $user['nick_color'] . "><input type=checkbox name=enablecolor value=1 id=enablecolor " . ($user['enablecolor'] ? "checked" : "") . "><label for=enablecolor>Enable Color</label>";

	echo "<form action='editprofile.php?id=$targetuserid' method='post' enctype='multipart/form-data'><table class=\"c1\">" .
		catheader('Login information')
.	(has_perm("edit-users") ? fieldrow('Username', fieldinput(40, 255, 'name')) : fieldrow('Username', $user['name']))
.	(checkcdisplayname($targetuserid) ? fieldrow('Display name', fieldinput(40, 255, 'displayname')) : "" )
.	fieldrow('Password', $passinput);

	if (has_perm("edit-users"))
		echo
		catheader('Administrative bells and whistles')
.	fieldrow('Group', fieldselect('group_id', $user['group_id'], $listgroup))
.	(($user['tempbanned'] > 0) ? fieldrow('Ban Information', '<input type=checkbox name=permaban value=1 id=permaban><label for=permaban>Make ban permanent</label>') : "" );

	echo
		catheader('Appearance')
.	fieldrow('Rankset', fieldselect('rankset', $user['rankset'], ranklist()))
.	((checkctitle($targetuserid)) ? fieldrow('Title', fieldinput(40, 255, 'title')) : "")
.	fieldrow('Picture', '<input type=file name=picture size=40> <input type=checkbox name=picturedel value=1 id=picturedel><label for=picturedel>Erase</label>
		<br><span class=sfont>Must be PNG, JPG or GIF, within 80KB, within ' . $avatardimx . 'x' . $avatardimy . '.</span>')
.	(checkcusercolor($targetuserid) ? fieldrow('Custom username color', $colorinput) : "" ) 
.		catheader('User information')
.	fieldrow('Sex', fieldoption('sex', $user['sex'], array('Male', 'Female', 'N/A')))
.	fieldrow('Real name', fieldinput(40, 60, 'realname'))
.	fieldrow('Location', fieldinput(40, 60, 'location'))
.	fieldrow('Birthday', $birthinput)
.	fieldrow('Bio', fieldtext(5, 80, 'bio'))
.	fieldrow('Email address', fieldinput(40, 60, 'email'))
.	fieldrow('Hide Email', fieldoption('emailhide', $user['emailhide'], array('Show my email', 'Hide my email')))
.		catheader('Post layout')
.	fieldrow('Header', fieldtext(5, 80, 'head'))
.	fieldrow('Signature', fieldtext(5, 80, 'sign'))
.	fieldrow('Signature line', fieldoption('signsep', $user['signsep'], array('Display', 'Hide'))) 
.		catheader('Options')
.	fieldrow('Theme', fieldselect('theme', $user['theme'], themelist()))
.	fieldrow('Timezone', fieldselect('timezone', $user['timezone'], $listtimezones))
.	fieldrow('Posts per page', fieldinput(3, 3, 'ppp'))
.	fieldrow('Threads per page', fieldinput(3, 3, 'tpp'))
.	fieldrow('Long pagelists', fieldoption('longpages', $user['longpages'], array('Abbreviate as needed', 'Always display in entirety')))
.	fieldrow('Font size', fieldinput(3, 3, 'fontsize'))
.	fieldrow('Date format', fieldinput(15, 15, 'dateformat'))
.	fieldrow('Time format', fieldinput(15, 15, 'timeformat'))
.	fieldrow('Post layouts', fieldoption('blocklayouts', $user['blocklayouts'], array('Show everything in general', 'Block everything')));

	echo catheader('&nbsp;'); ?>
	<tr class="n1"><td class="b">&nbsp;</td><td class="b"><input type="submit" class="submit" name="action" value="Edit profile"></td>
	</table><input type="hidden" name=token value='<?=$token?>'></form><?php
}

pagefooter();
?>