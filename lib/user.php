<?php

$rankcache = array();
$userbirthdays = array();

function dobirthdays() { //Function for calling after we get the timezone for the user set [Gywall]
	global $sql, $userbirthdays;
	// [Mega-Mario] Check for birthdays globally.
	// Makes stuff like checking for rainbow usernames a lot easier.
	$rbirthdays = $sql->query("SELECT `id` FROM `users` WHERE `birth` LIKE '" . date('m-d') . "%'");
	while ($bd = $sql->fetch($rbirthdays))
		$userbirthdays[$bd['id']] = true;
	return;
}

function checkuser($name, $pass) {
	global $sql;
	$id = $sql->resultq("SELECT id FROM users WHERE (name='$name' OR displayname='$name') AND pass='$pass'");
	if (!$id)
		$id = 0;
	return $id;
}

function checkuid($userid, $pass) {
	global $sql;
	checknumeric($userid);
	$user = $sql->fetchq("SELECT * FROM users WHERE id=$userid AND pass='" . addslashes($pass) . "'");
	return $user;
}

function checkctitle($uid) {
	global $sql, $loguser;

	$defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default`=1");

	if (!$loguser['id'])
		return false;

	if (has_perm_revoked('edit-own-title'))
		return false;

	if ($uid == $loguser['id'] && has_perm('edit-own-title')) {
		if ($loguser['group_id'] != $defaultgroup) // resultq returns the actual field... not sure why this was comparing against an array.
			return true;

		if ($loguser['posts'] >= 100)
			return true;

		if ($loguser['posts'] > 50 && $loguser['regdate'] < (time() - 3600 * 24 * 60))
			return true;

		return false;
	}

	if (has_perm('edit-titles'))
		return true;

	if (has_perm_with_bindvalue('edit-user-title', $uid))
		return true;

	return false;
}

function checkcusercolor($uid) {
	global $loguser, $config;

	if (!$config["perusercolor"])
		return false;

	if (!$loguser['id'])
		return false;
	if (has_perm_revoked('has-customusercolor'))
		return false;
	if ($uid == $loguser['id'] && has_perm('has-customusercolor'))
		return true;

	if (has_perm('edit-customusercolors'))
		return true;
	if (has_perm_with_bindvalue('edit-user-customnickcolor', $uid))
		return true;

	return false;
}

function checkcdisplayname($uid) {
	global $sql, $loguser, $config;

	$defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default` = 1");

	if (!$config['displayname'])
		return false;

	if (!$loguser['id'])
		return false;
	if (has_perm_revoked('has-displayname'))
		return false;

	if ($uid == $loguser['id'] && has_perm('has-displayname')) {
		if ($loguser['group_id'] != $defaultgroup)
			return true;

		//Allow a custom displayname after a specific postcount/time.
		if ($loguser['posts'] >= 100)
			return true;

		if ($loguser['posts'] > 50 && $loguser['regdate'] < (time() - 3600 * 24 * 60))
			return true;

		return false;
	}

	if (has_perm('edit-displaynames'))
		return true;
	if (has_perm_with_bindvalue('edit-user-displayname', $uid))
		return true;

	return false;
}

function getrank($set, $posts) {
	global $ranks, $rankset_data, $rankset_names;

	if ($set == 0) return '';

	$i = 1;
	foreach ($rankset_data[$rankset_names[$set]] as $rankset) {
		$neededposts = $rankset['p'];
		if (isset($rankset_data[$rankset_names[$set]][$i]['p']))
			$nextneededposts = $rankset_data['Mario'][$i]['p'];
		else
			$nextneededposts = 2147483647;

		if (($posts >= $neededposts) && ($posts < $nextneededposts)) {
			return $rankset['str'];
		}
		$i++;
	}
	return "";
}

function randnickcolor() {
	/* OLD HACKISH CODE FOR APRIL 5 */
	$stime = gettimeofday();
	$h = (($stime['usec'] / 5) % 600);
	if ($h < 100) {
		$r = 255;
		$g = 155 + $h;
		$b = 155;
	} elseif ($h < 200) {
		$r = 255 - $h + 100;
		$g = 255;
		$b = 155;
	} elseif ($h < 300) {
		$r = 155;
		$g = 255;
		$b = 155 + $h - 200;
	} elseif ($h < 400) {
		$r = 155;
		$g = 255 - $h + 300;
		$b = 255;
	} elseif ($h < 500) {
		$r = 155 + $h - 400;
		$g = 155;
		$b = 255;
	} else {
		$r = 255;
		$g = 155;
		$b = 255 - $h + 500;
	}
	$rndcolor = substr(dechex($r * 65536 + $g * 256 + $b), -6);
	$namecolor = "color=$rndcolor";
	return $rndcolor;
}

function userfields($tbl = '', $pf = '') {
	$fields = array('id', 'name', 'displayname', 'sex', 'group_id', 'nick_color', 'enablecolor');

	$ret = '';
	foreach ($fields as $f) {
		if ($ret)
			$ret .= ',';
		if ($tbl)
			$ret .= '`' . $tbl . '`.';
		$ret .= '`' . $f . '`';
		if ($pf)
			$ret .= ' AS `' . $pf . $f . '`';
	}

	return $ret;
}

function userlink_by_id($uid) {
	global $sql;
	$u = $sql->fetchp("SELECT " . userfields() . " FROM users WHERE id=?", array($uid));
	return userlink($u);
}

function userlink($user, $u = '') {
	global $loguser;
	if (!$user[$u . 'name'])
		$user[$u . 'name'] = '&nbsp;';

	return '<a href="profile.php?id=' . $user[$u . 'id'] . '">'
			. userdisp($user, $u)
			. '</a>';
}

function userdisp($user, $u = '') {
	global $sql, $config, $usergroups, $userbirthdays, $usercnc;

//Enable per theme nick colors & light theme nick shadows
	$nccss = '';

	if ($config['nickcolorcss'])
		$nccss = "class='nc" . $user[$u . 'group_id'] . "'";
//Over-ride for custom colours [Gywall]
	if ($config['perusercolor'] && $user[$u . 'nick_color'] && $user[$u . 'enablecolor']) {
		$nc = $user[$u . 'nick_color'];
		$nccss = "";
	} else {
		$group = $usergroups[$user[$u . 'group_id']];
		$nc = $group['nc'];
	}
	//Random Nick Color on Birthday
	if (isset($userbirthdays[$user[$u . 'id']]))
		$nc = randnickcolor();

	$n = $user[$u . 'name'];
	if ($config['displayname'] && $user[$u . 'displayname'])
		$n = $user[$u . 'displayname'];

	$userdisname = "<span $nccss style='color:#$nc;'>"
			. str_replace(" ", "&nbsp;", htmlval($n))
			. '</span>';

	return $userdisname;
}

?>