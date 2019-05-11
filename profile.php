<?php
require("lib/common.php");

$uid = isset($_GET['id']) ? (int)$_GET['id'] : -1;
if ($uid < 0) noticemsg("Error", "You must specify a user ID!", true);

$user = $sql->fetchp("SELECT * FROM users WHERE id = ?", [$uid]);
if (!$user) noticemsg("Error", "This user does not exist!", true);

$group = $sql->fetchp("SELECT * FROM groups WHERE id = ?", [$user['group_id']]);

pageheader("Profile for " . ($user['displayname'] ? $user['displayname'] : $user['name']));

$days = (time() - $user['regdate']) / 86400;
$pfound = $sql->resultp("SELECT count(*) FROM posts WHERE user = ?", [$uid]);
$pavg = sprintf("%1.02f", $user['posts'] / $days);
$tfound = $sql->resultp("SELECT count(*) FROM threads WHERE user = ?", [$uid]);
$tavg = sprintf('%1.02f', $user['threads'] / $days);

$thread = $sql->fetchp("SELECT p.id, t.title ttitle, f.title ftitle, t.forum, f.private FROM forums f
	LEFT JOIN threads t ON t.forum = f.id LEFT JOIN posts p ON p.thread = t.id
	WHERE p.date = ? AND p.user = ? AND f.id IN " . forums_with_view_perm(), [$user['lastpost'], $uid]);

if ($pfound && $thread) {
	$lastpostlink = "<br>in <a href=\"thread.php?pid=$thread[id]#$thread[id]\">".htmlval($thread['ttitle'])."</a>
		(<a href=\"forum.php?id=$thread[forum]\">" . htmlval($thread['ftitle']) . "</a>)";
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
	//You're Goddamn right. :P
	$monthnames = [1 => 'January', 'February', 'March', 'April',
		'May', 'June', 'July', 'August',
		'September', 'October', 'November', 'December'];
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

if ($user['email'] && !$user['emailhide']) {
	$email = str_replace(".", "<b> (dot) </b>", str_replace("@", "<b> (at) </b>", $user['email']));
} else {
	$email = "";
}

if ($user['url'][0] == "!") {
	$user['url'] = substr($user['url'], 1);
	$user['ssl'] = 1;
}

$post['date'] = time();
$post['ip'] = $user['ip'];
$post['num'] = 0;

$post['id'] = 0; $post['thread'] = 0;

$post['text'] = <<<HTML
[b]This[/b] is a [i]sample message.[/i] It shows how [u]your posts[/u] will look on the board.
[quote=Anonymous][spoiler]Hello![/spoiler][/quote]
[code]if (true) {
	print "The world isn't broken.";
} else {
	print "Something is very wrong.";
}[/code]
[irc]This is like code tags but without formatting.
<Anonymous> I said something![/irc]
[url=]Test Link. Ooh![/url]
HTML;

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

$banuser = "";
if (has_perm('edit-permissions')) {
	if (!has_perm('ban-users'))
		$banuser = "";
	elseif ($user['group_id'] != $bannedgroup)
		$banuser = "| <a href=\"banmanager.php?id=" . $user['id'] . "\">Ban user</a>";
	elseif ($user['group_id'] = $bannedgroup)
		$banuser = "| <a href=\"banmanager.php?unban&id=" . $user['id'] . "\">Unban user</a>";
}

$rblock = $sql->prepare("SELECT * FROM blockedlayouts WHERE user = ? AND blockee = ?", [$uid, $loguser['id']]);
$isblocked = $sql->numrows($rblock);
$blocklayoutlink = '';

if ($log) {
	if (isset($_GET['block'])) {
		$block = (int) $_GET['block'];

		if ($block && !$isblocked) {
			$rblock = $sql->prepare("INSERT INTO blockedlayouts (user, blockee) values (?,?)", [$uid, $loguser['id']]);
			$isblocked = true;
		} elseif (!$block && $isblocked) {
			$rblock = $sql->prepare("DELETE FROM blockedlayouts WHERE user = ? AND blockee = ?", [$uid, $loguser['id']]);
			$isblocked = false;
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

//If user has a a displayname, a custom username color, or both, we need to show the real name field.
if ($user['enablecolor']) $usercnickcolor = $user['nick_color'];
if ($user['displayname']) $userdisplayname = true;

if ($userdisplayname || $usercnickcolor) {
	$showrealnick = true;
}

$gender = ['Male', 'Female', 'N/A'];

$profilefields = [
	"General information" => [
		['title' => 'Real handle', 'value' => "<span style='color:#".$realnc.";'><b>".htmlval($user['name'])."</b></span>"],
		['title' => 'Group', 'value' => $group['title']],
		['title' => 'Total posts', 'value' => $user['posts']." ($pfound found, $pavg per day)"],
		['title' => 'Total threads', 'value' => $user['threads']." ($tfound found, $tavg per day)"],
		['title' => 'Registered on', 'value' => date($dateformat, $user['regdate']).' ('.timeunits($days * 86400).' ago)'],
		['title' => 'Last post', 'value'=>($user['lastpost'] ? date($dateformat, $user['lastpost'])." (".timeunits(time()-$user['lastpost'])." ago)" : "None").$lastpostlink],
		['title' => 'Last view', 
			'value' => date($dateformat, $user['lastview']).' ('.timeunits(time() - $user['lastview']).' ago)'.
			($user['url'] ? '<br>at <a href="'.htmlval($user['url']).'">'.htmlval($user['url']).'</a>' : '').
			($user['ip'] && has_perm("view-post-ips") ? '<br>from IP: '.$user['ip'] : '')]
	],
	"User information" => [
		['title' => 'Gender', 'value' => $gender[$user['gender']]],
		['title' => 'Location', 'value' => ($user['location'] ? htmlval($user['location']) : "")],
		['title' => 'Birthday', 'value' => "$birthday $age"],
		['title' => 'Bio', 'value' => ($user['bio'] ? postfilter($user['bio']) : "")],
		['title' => 'Email', 'value' => $email]
	],
	"User settings" => [
		['title' => 'Theme', 'value' => htmlval($themename)],
		['title' => 'Time offset', 'value' => sprintf("%d:%02d", ($usertzoff - $logtzoff) / 3600, abs(($usertzoff - $logtzoff) / 60) % 60)." from you (Current time: $userct)"],
		['title' => 'Items per page', 'value' => $user['ppp']." posts, ".$user['tpp']." threads"]
	]
];

$topbot = [
	'breadcrumb' => [['href' => './', 'title' => 'Main']],
	'title' => ($user['displayname'] ? $user['displayname'] : $user['name'])
];

RenderPageBar($topbot);

foreach ($profilefields as $k => $v) {
	echo '<br><table class="c1"><tr class="h"><td class="b h" colspan="2">'.$k.'</td></tr>';
	foreach ($v as $pf) {
		if ($pf['title'] == 'Real handle' && !$showrealnick) continue;
		echo '<tr><td class="b n1" width="130"><b>'.$pf['title'].'</b></td><td class="b n2">'.$pf['value'].'</td>';
	}
	echo '</table>';
}

?><br>
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
		<?=$banuser . " " . $editpermissions ?>
	</td></tr>
</table><br>
<?php
RenderPageBar($topbot);
pagefooter();