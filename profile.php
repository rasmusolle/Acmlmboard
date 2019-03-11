<?php
require("lib/common.php");

$uid = isset($_GET['id']) ? (int)$_GET['id'] : -1;
if($uid < 0) {
	error("Error", "You must specify a user ID!");
}

$user = $sql->fetchq("SELECT * FROM `users` WHERE `id` = '$uid'");
if(!$user) {
	error("Error", "This user does not exist!");
}

$group = $sql->fetchp("SELECT * FROM `group` WHERE id=?", array($user['group_id']));

pageheader("Profile for " . ($user['displayname'] ? $user['displayname'] : $user['name']));

$days = (time() - $user['regdate']) / 86400;
$pfound = $sql->resultq("SELECT count(*) FROM `posts` WHERE `user`='$uid'");
$pavg = sprintf("%1.02f", $user['posts'] / $days);
$tfound = $sql->resultq("SELECT count(*) FROM `threads` WHERE `user`='$uid'");
$tavg = sprintf('%1.02f', $user['threads'] / $days);

$thread = $sql->fetchq("SELECT `p`.`id`, `t`.`title` `ttitle`, `f`.`title` `ftitle`, `t`.`forum`, `f`.`private`
						FROM `forums` `f`
						LEFT JOIN `threads` `t` ON `t`.`forum`=`f`.`id`
						LEFT JOIN `posts` `p` ON `p`.`thread`=`t`.`id`
						WHERE `p`.`date`='$user[lastpost]' AND p.user='$uid' AND `f`.`id` IN " . forums_with_view_perm());

$threadhack = $sql->fetchq("SELECT `p`.`id`, `t`.`title` `ttitle`, `t`.`forum`, `t`.`announce`
							FROM `threads` `t`
							LEFT JOIN `posts` `p` ON `p`.`thread`=`t`.`id`
							WHERE `p`.`date`='$user[lastpost]' AND p.user='$uid' AND `t`.`forum`='0'");

if ($pfound && $thread) {
	$lastpostlink = "<br>in <a href=\"thread.php?pid=$thread[id]#$thread[id]\">" . forcewrap(htmlval($thread['ttitle'])) . "</a>
		(<a href=\"forum.php?id=$thread[forum]\">" . htmlval($thread['ftitle']) . "</a>)";
} else if ($pfound && $threadhack['announce'] && $threadhack['forum'] == 0) {
	$lastpostlink = "<br>in <a href=\"thread.php?pid=$threadhack[id]#$threadhack[id]\">" . forcewrap(htmlval($threadhack['ttitle'])) . "</a>
		(<a href=\"thread.php?announce=0\">Announcements</a>)";
} else if ($user['posts'] == 0) {
	$lastpostlink = "";
} else {
	$lastpostlink = "<br>in <i>(restricted forum)</i>";
}

$themes = themelist();
foreach ($themes as $k => $v) {
	if ((string)$k == $user['theme']) {
		$themename = $v;
		break;
	}
}

if ($user['birth'] != -1) {
	//Crudely done code.
	//You're Goddamn right. :P - SquidEmpress
	$monthnames = array(1 => 'January', 'February', 'March', 'April',
		'May', 'June', 'July', 'August',
		'September', 'October', 'November', 'December');
	$bdec = explode("-", $user['birth']);
	$bstr = $bdec[2] . "-" . $bdec[0] . "-" . $bdec[1];
	$mn = intval($bdec[0]);
	if ($bdec['2'] <= 0 && $bdec['2'] > -2)
		$birthday = $monthnames[$mn] . " " . $bdec[1];
	else
		$birthday = date("l, F j, Y", strtotime($bstr));

	$age = '<!-- This feature requires PHP 5.3.0 or higher -->';
	if (class_exists('DateTime') && method_exists('DateTime', 'diff')) {
		$bd1 = new DateTime($bstr);
		$bd2 = new DateTime(date("Y-m-d"));
		if ($bd2 < $bd1 && !$bdec['2'] <= 0)
			$age = '(not born yet)';
		else if ($bdec['2'] <= 0 && $bdec['2'] > -2)
			$age = '';
		else {
			$bd3 = $bd1->diff($bd2);
			$age = "(" . intval($bd3->format("%Y")) . " years old)";
		}
	}
} else {
	$birthday = "";
	$age = "";
}

//This code was done by Gywall
if ($user['email'] && !$user['emailhide']) {
	$email = EmailObscurer($user['email']);
} else {
	$email = "";
}

if ($user['url'][0] == "!") {
	$user['url'] = substr($user['url'], 1);
	$user['ssl'] = 1;
}

$post['date'] = time();
$post['ip'] = $user['ip'];
$post['num'] = 0; //$user[posts];  #2/26/2007 xkeeper - threadpost can hide "1/" now

$post['id'] = -1;
$post['nolayout'] = 0;
$post['thread'] = -1;

$post['text'] = $config['samplepost'];

foreach ($user as $field => $val) {
	$post['u' . $field] = $val;
}

//More indepth test to not show the link if you can't edit your own perms
$editpermissions = "";
if (has_perm('edit-permissions')) {
	if (!has_perm('edit-own-permissions') && $loguser['id'] == $uid)
		$editpermissions = "";
	else
		$editpermissions = "| <a href=\"editperms.php?uid=" . $user['id'] . "\">Edit user permissions</a>";
}

$secondarygroups = "";
if (has_perm('assign-secondary-groups')) {
	$secondarygroups = "| <a href=\"assignsecondary.php?uid=" . $user['id'] . "\">Manage secondary groups</a>";
}

$bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE `banned`=1");

$banuser = "";
if (has_perm('edit-permissions')) {
	if (!has_perm('ban-users'))
		$banuser = "";
	elseif ($user['group_id'] != $bannedgroup)
		$banuser = "| <a href=\"banmanager.php?id=" . $user['id'] . "\">Ban user</a>";
	elseif ($user['group_id'] = $bannedgroup)
		$banuser = "| <a href=\"banmanager.php?unban&id=" . $user['id'] . "\">Unban user</a>";
}

//[KAWA] Blocklayout ported from ABXD
$qblock = "SELECT * FROM `blockedlayouts` WHERE `user`='$uid' AND `blockee`='$loguser[id]'";
$rblock = $sql->query($qblock);
$isblocked = $sql->numrows($rblock);
$blocklayoutlink = '';

if ($log) {
	if (isset($_GET['block'])) {
		$block = (int) $_GET['block'];

		if ($block && !$isblocked) {
			$qblock = "INSERT INTO `blockedlayouts` (`user`, `blockee`) values ('$uid', '$loguser[id]')";
			$rblock = $sql->query($qblock);
			$blockmessage = "Layout blocked.";
			$isblocked = true;
		} elseif (!$block && $isblocked) {
			$qblock = "DELETE FROM `blockedlayouts` WHERE `user`='$uid' AND `blockee`='$loguser[id]' LIMIT 1";
			$rblock = $sql->query($qblock);
			$blockMessage = "Layout unblocked.";
			$isblocked = false;
		}

		if ($blockmessage) {
			echo '<table class="c1"><td class="b n1 center">'.$blockmessage.'</table><br>';
		}
	}

	if ($isblocked)
		$blocklayoutlink = "| <a href=\"profile.php?id=$uid&amp;block=0\">Unblock layout</a>";
	else
		$blocklayoutlink = "| <a href=\"profile.php?id=$uid&amp;block=1\">Block layout</a>";
}

//timezone calculations
$now = new DateTime("now");
$usertz = new DateTimeZone($user['timezone']);
$userdate = new DateTime("now", $usertz);
$userct = date_format($userdate, $dateformat);
$logtz = new DateTimeZone($loguser['timezone']);
$usertzoff = $usertz->getOffset($now);
$logtzoff = $logtz->getOffset($now);

//User color override - Should be moved to a function.
$u = ''; // what was this originally?
$group = $usergroups[$user[$u . 'group_id']];
$realnc = $group['nc'];

//Toggles class define for spans where appropriate
$usercnickcolor = '';
$userdisplayname = false;
$showrealnick = false;

//If user has a a displayname, a custom username color, or both, we need to show the realname field.
if ($config['perusercolor'] && $user['enablecolor'])
	$usercnickcolor = $user['nick_color'];

if ($config['displayname'] && $user['displayname'])
	$userdisplayname = true;

if ($userdisplayname || $usercnickcolor) {
	$showrealnick = true;
}

$sex = array('Male', 'Female', 'N/A');

print "<a href=\"./\">Main</a> - Profile for " . userdisp($user) . "<br><br>";
?>
<table class="c1">
	<tr class="h">
		<td class="b h" colspan="2">General information</td>
	<?=($showrealnick ? "<tr><td class=\"b n1\" width=\"110\"><b>Real handle</b></td><td class=\"b n2\"><span style='color:#" . $realnc . ";'><b>" . htmlval($user['name']) . "</b></span>" : "") ?>
	</tr><tr>
		<td class="b n1" width="120"><b>Group</b></td>
		<td class="b n2"><?=$group['title'] ?></td>
	</tr><tr>
		<td class="b n1"><b>Total posts</b></td>
		<td class="b n2"><?=$user['posts'] ?> (<?=$pfound ?> found, <?=$pavg ?> per day)</td>
	</tr><tr>
		<td class="b n1"><b>Total threads</b></td>
		<td class="b n2"><?=$user['threads'] ?> (<?=$tfound ?> found, <?=$tavg ?> per day)</td>
	</tr><tr>
		<td class="b n1"><b>Registered on</b></td>
		<td class="b n2"><?=date($dateformat, $user['regdate']) ?> (<?=timeunits($days * 86400) ?> ago)</td>
	</tr><tr>
		<td class="b n1"><b>Last post</b></td>
		<td class="b n2">
			<?=($user['lastpost'] ? date($dateformat, $user['lastpost']) . " (" . timeunits(time() - $user['lastpost']) . " ago)" : "None") . $lastpostlink ?>
		</td>
	</tr><tr>
		<td class="b n1"><b>Last view</b></td>
		<td class="b n2">
			<?=date($dateformat, $user['lastview']) ?> (<?=timeunits(time() - $user['lastview']) ?> ago)
			<?=($user['url'] ? "<br>at <a href='" . htmlval($user['url']) . "'>" . htmlval($user['url']) . "</a>" : '') ?>
			<?=($user['ip'] && has_perm("view-post-ips") ? "<br>from IP: $user[ip]" : '') ?>
		</td>
	</tr>
</table>
<br>
<table class="c1">
	<tr class="h">
		<td class="b h" colspan="2">User information</td>
	</tr><tr>
		<td class="b n1" width="120"><b>Real name</b></td>
		<td class="b n2"><?=($user['realname'] ? htmlval($user['realname']) : "") ?></td>
	</tr><tr>
		<td class="b n1"><b>Sex</b></td>
		<td class="b n2"><?=$sex[$user['sex']] ?></td>
	</tr><tr>
		<td class="b n1"><b>Location</b></td>
		<td class="b n2"><?=($user['location'] ? htmlval($user['location']) : "") ?></td>
	</tr><tr>
		<td class="b n1"><b>Birthday</b></td>
		<td class="b n2"><?=$birthday . ' ' . $age ?></td>
	</tr><tr>
		<td class="b n1"><b>Bio</b></td>
		<td class="b n2"><?=($user['bio'] ? postfilter($user['bio']) : "") ?></td>
	</tr><tr>
		<td class="b n1" width="110"><b>Email address</b></td>
		<td class="b n2"><?=$email ?></td>
	</tr>
</table>
<br>
<table class="c1">
	<tr class="h">
		<td class="b h" colspan="2">User settings</td>
	</tr><tr>
		<td class="b n1" width="120"><b>Theme</b></td>
		<td class="b n2"><?=htmlval($themename) ?></td>
	</tr><tr>
		<td class="b n1"><b>Time offset</b></td>
		<td class="b n2">
			<?=sprintf("%d:%02d", ($usertzoff - $logtzoff) / 3600, abs(($usertzoff - $logtzoff) / 60) % 60) ?> from you (Current time: <?=$userct ?>)
		</td>
	</tr><tr>
		<td class="b n1"><b>Items per page</b></td>
		<td class="b n2"><?=$user['ppp'] ?> posts, <?=$user['tpp'] ?> threads</td>
	</tr>
</table>
<br>
<table class="c1"><tr class="h"><td class="b h">Sample post</td><tr></table>
<?=threadpost($post)?>
<br>
<table class="c1">
	<tr class="h"><td class="b n3">
		<a href="forum.php?user=<?=$user['id'] ?>">View threads</a>
		| <a href="thread.php?user=<?=$user['id'] ?>">Show posts</a>
		<?=$blocklayoutlink ?>
		<?=(has_perm('create-pms') ? '| <a href="sendprivate.php?uid=' . $user['id'] . '">Send private message</a>' : "") ?>
		<?=(has_perm('view-user-pms') ? '| <a href="private.php?id=' . $user['id'] . '">View private messages</a>' : "") ?>
		<?=(has_perm('edit-users') ? '| <a href="editprofile.php?id=' . $user['id'] . '">Edit user</a>' : "") ?>
		<?=$banuser . " " . $editpermissions . " " . $secondarygroups ?>
	</td></tr>
</table>
<?php pagefooter() ?>