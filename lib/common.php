<?php
require("lib/function.php");

header("Content-type: text/html; charset=utf-8");

//[Scrydan] Added these three variables to make editing quicker.
$boardprog = "Acmlm, Emuz, <a href='credits.php'>et al</a>.";
$abversion = "2.5.3MOD";

$userip = $_SERVER['REMOTE_ADDR'];
$userfwd = addslashes(getenv('HTTP_X_FORWARDED_FOR')); //We add slashes to that because the header is under users' control
$url = getenv("SCRIPT_NAME");
if ($q = getenv("QUERY_STRING"))
	$url.="?$q";

$log = false;
$logpermset = array();

if (!empty($_COOKIE['user']) && !empty($_COOKIE['pass'])) {
	if($user = checkuid($_COOKIE['user'], unpacklcookie($_COOKIE['pass']))) {
		$log = true;
		$loguser = $user;
		load_user_permset();
	} else {
		setcookie('user',0);
		setcookie('pass','');
		load_guest_permset();
	}
} else {
	load_guest_permset();
}

if ($config['lockdown']) {
	//lock down
	if (has_perm('bypass-lockdown'))
		echo "<h1><span style=\"color:red\"><center>LOCKDOWN!! LOCKDOWN!! LOCKDOWN!!</center></span></h1>";
	else {
		echo <<<HTML
<body style="background-color:#C02020;padding:5em;color:#ffffff;margin:auto;max-width:50em;">
	Access to the board has been restricted by the administration.
	Please forgive any inconvenience caused and stand by until the underlying issues have been resolved.
</body>
HTML;
		die();
	}
}

if (!$log) {
	$loguser = array();
	$loguser['id'] = 0;
	$loguser['group_id'] = 1;
	$loguser['timezone'] = "UTC";
	$loguser['fontsize'] = $defaultfontsize; //2/22/2007 xkeeper - guests have "normal" by default, like everyone else
	$loguser['dateformat'] = "Y-m-d";
	$loguser['timeformat'] = "H:i";
	$loguser['signsep'] = 0;
	$loguser['theme'] = $defaulttheme;
	$loguser['ppp'] = 20;
	$loguser['tpp'] = 20;
}

date_default_timezone_set($loguser['timezone']);
dobirthdays(); //Called here to account for timezone bugs.

if ($loguser['ppp'] < 1) $loguser['ppp'] = 20;
if ($loguser['tpp'] < 1) $loguser['tpp'] = 20;

//Unban users whose tempbans have expired. - SquidEmpress
$defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default`=1");
$sql->query('UPDATE users SET group_id=' . $defaultgroup . ', title="", tempbanned="0" WHERE tempbanned<' . time() . ' AND tempbanned>0');

$dateformat = "$loguser[dateformat] $loguser[timeformat]";

$bot = 0;

if (str_replace($botlist, "x", strtolower($_SERVER['HTTP_USER_AGENT'])) != strtolower($_SERVER['HTTP_USER_AGENT'])) {
	$bot = 1;
}
if ($bot) {
	load_bot_permset();
}
if (substr($url, 0, strlen("$config[path]rss.php")) != "$config[path]rss.php") {
	$sql->query("DELETE FROM `guests` WHERE `ip`='$userip' OR `date`<" . (time() - 300));
	if ($log) {
		$sql->query("UPDATE `users` SET `lastview`=" . time() . ",`ip`='$userip', `ipfwd`='$userfwd', `url`='" . addslashes($url) . "', `ipbanned`='0' WHERE `id`='$loguser[id]'");
	} else {
		$sql->query('INSERT INTO `guests` (`date`, `ip`, `url`, `useragent`, `bot`) VALUES (' . time() . ",'$userip','" . addslashes($url) . "', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "', '$bot')");
	}

	//[blackhole89]
	if ($config['log'] >= '5') {
		$postvars = "";
		foreach ($_POST as $k => $v) {
			if ($k == "pass" || $k == 'pass2')
				$v = "(snip)";
			$postvars.="$k=$v ";
		}
		@$sql->query("INSERT DELAYED INTO `log` VALUES(UNIX_TIMESTAMP(),'$userip','$loguser[id]','" . addslashes($_SERVER['HTTP_USER_AGENT']) . " :: " . addslashes($url) . " :: $postvars')");
	}

	if (!$bot) {
		$sql->query("UPDATE `misc` SET `intval`=`intval`+1 WHERE `field`='views'");
	} else {
		$sql->query('UPDATE `misc` SET `intval`=`intval`+1 WHERE `field`="botviews"');
	}

	$views = $sql->resultq("SELECT `intval` FROM `misc` WHERE `field`='views'");
	$botviews = $sql->resultq("SELECT `intval` FROM `misc` WHERE `field`='botviews'");

	$count = $sql->fetchq("SELECT (SELECT COUNT(*) FROM users) u, (SELECT COUNT(*) FROM threads) t, (SELECT COUNT(*) FROM posts) p");
	$date = date("m-d-y", time());
}

//[KAWA] ABXD-style theme system
$themelist = unserialize(file_get_contents("themes_serial.txt"));

//Config definable theme override
if ($config['override_theme']) {
	$theme = $config['override_theme'];
} elseif (isset($_GET['theme'])) {
	$theme = $_GET['theme'];
} else {
	$theme = $loguser['theme'];
}

if (is_file("theme/" . $theme . "/" . $theme . ".css")) {
	//try CSS first
	$themefile = $theme . ".css";
} elseif (is_file("theme/" . $theme . "/" . $theme . ".php")) {
	//then try PHP
	$themefile = $theme . ".php";
} else { //then fall back to Standard
	$theme = $themelist[0][1];
	$themefile = $theme . ".css";
}

$logofile = $defaultlogo;

$sql->query('DELETE FROM ipbans WHERE expires<'.time().' AND expires>0');

$r = $sql->query("SELECT * FROM ipbans WHERE '$userip' LIKE ipmask");
if (@$sql->numrows($r) > 0) {
	if ($loguser) $sql -> query("UPDATE `users` SET `ipbanned` = '1' WHERE `id` = '$loguser[id]'");
	else $sql->query("UPDATE `guests` SET `ipbanned` = '1' WHERE `ip` = '". $_SERVER['REMOTE_ADDR'] ."'");

	$bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE `banned`=1");

	$i = $sql->fetch($r);
	if ($i['hard']) {
		pageheader('IP banned');
		echo '<table class="c1"><tr class="n2"><td class="b n1 center">Sorry, but your IP address has been banned.</td></tr></table>';
		pagefooter();
		die();
	} else if (!$i['hard'] && (!$log || $loguser['group_id'] == $bannedgroup['id'])) {
		if (!strstr($_SERVER['PHP_SELF'], "login.php")) {
			pageheader('IP restricted');
			echo '<table class="c1"><tr class="n2"><td class="b n1 center">Access from your IP address has been limited.<br><a href=login.php>Login</a></table>';
			pagefooter();
			die();
		}
	}
}

function pageheader($pagetitle = "", $fid = 0) {
	global $dateformat, $sql, $log, $loguser, $sqlpass, $views, $botviews, $sqluser, $boardtitle, $extratitle, $boardlogo, $homepageurl,
	$theme, $themefile, $logofile, $url, $config, $favicon, $showonusers, $count, $pwdsalt, $pwdsalt2, $bot;

	if (ini_get("register_globals")) {
		echo "<span style=\"color: red;\"> Warning: register_globals is enabled.</style>";
	}
	// this is the only common.php location where we reliably know $fid.
	if ($log) {
		$sql->query("UPDATE `users` SET `lastforum`='$fid' WHERE `id`='$loguser[id]'");
	} else {
		$sql->query("UPDATE `guests` SET `lastforum`='$fid' WHERE `ip`='$_SERVER[REMOTE_ADDR]'");
	}
	$timezone = new DateTimeZone($loguser['timezone']);
	$tzoff = $timezone->getOffset(new DateTime("now"));
	$minover_ii = isset($_GET['minover']) ? (int)$_GET['minover'] : -1;
	$themefile .= "?tz=$tzoff&minover=$minover_ii";

	if ($pagetitle)
		$pagetitle .= " - ";

	$t = $sql->resultq("SELECT `txtval` FROM `misc` WHERE `field`='attention'");

	if ($t != "")
		$extratitle = <<<HTML
<table class="c1 center" width="100%">
	<tr class="h"><td class="b h">News</td></tr>
	<tr class="n2 center"><td class="b sfont">$t</td></tr>
</table>
HTML;

	if ($extratitle) {
		$boardlogo = <<<HTML
<table width=100%><tr class="center">
	<td class="nb" valign="center">$boardlogo</td>
	<td class="nb" valign="center" width="300">$extratitle</td>
</tr></table>
HTML;
	}

	if ($log) {
		$logbar = $loguser;
	}

	?><!DOCTYPE html>
<html>
	<head>
		<title><?=$pagetitle.$boardtitle?></title>
		<?=$config['meta']?>
		<link rel="icon" type="image/png" href="<?=$favicon?>">
		<link rel="stylesheet" href="theme/<?=$theme?>/<?=$themefile?>">
		<link rel="stylesheet" href="theme/common.css">
		<link href="lib/prettify/sunburst.css" type="text/css" rel="stylesheet" />
		<link rel='alternate' type='application/rss+xml' title='RSS Feed' href='rss.php'>
		<script type="text/javascript" src="lib/prettify/prettify.js"></script>
	</head>
	<body style="font-size:<?=$loguser['fontsize']?>%" onload="prettyPrint()">
		<table class="c1">
			<tr class="nt n2 center"><td class="b n1 center" colspan="3"><?=$boardlogo?></td></tr>
			<tr class="n2 center">
				<td class="b"><div style="width: 150px">Views: <?=number_format($views) ?></div></td>
				<td class="b" width="100%">
					<a href="./">Main</a>
					| <a href="faq.php">FAQ</a>
					| <a href="memberlist.php">Memberlist</a>
					| <a href="activeusers.php">Active users</a>
					| <a href="thread.php?time=86400">Latest posts</a>
					| <a href="ranks.php">Ranks</a>
					| <a href="online.php">Online users</a>
					| <a href="search.php">Search</a>
				</td>
				<td class="b"><div style="width: 150px"><?=cdate($dateformat, time())?></div></td>
				<tr class="n1 center"><td class="b" colspan="3"><?=($log ? userlink($logbar) : "Guest")?> 
<?php
	if ($log) {
		$unreadpms = $sql->resultq("SELECT COUNT(*) FROM `pmsgs` WHERE `userto`='$loguser[id]' AND `unread`=1 AND `del_to`='0'");

		if (has_perm('view-own-pms')) {
			echo '<a href="private.php">
			<img src="img/pm'.(!$unreadpms ? '-off' : '').'.png" width="20" alt="Private messages" title="Private message"></a>
			'.($unreadpms ?  "($unreadpms new)" : '').' | ';
		}
	}

	checknumeric($fid);
	if ($fid)
		$markread = array("url" => "index.php?action=markread&fid=$fid", "title" => "Mark forum read");
	else
		$markread = array("url" => "index.php?action=markread&fid=all", "title" => "Mark all forums read");

	$userlinks = array();

	if (!$log) {
		if (!$bot) {
			$userlinks[] = array('url' => "register.php", 'title' => 'Register');
			$userlinks[] = array('url' => "login.php", 'title' => 'Login');
		}
	} else {
		$userlinks[] = array('url' => "javascript:document.logout.submit()", 'title' => 'Logout');
	}
	if ($log) {
		if (has_perm("update-own-profile"))
			$userlinks[] = array('url' => "editprofile.php", 'title' => 'Edit profile');
		if (has_perm('manage-board'))
			$userlinks[] = array('url' => 'management.php', 'title' => 'Management');
		$userlinks[] = $markread;
	}

	$c = 0;
	foreach ($userlinks as $k => $v) {
		if ($c > 0) echo " | ";
		echo "<a href=\"{$v['url']}\">{$v['title']}</a>";
		$c++;
	}

	echo "</td>";
	if ($log) {
		?><form action="login.php" method="post" name="logout">
			<input type="hidden" name="action" value="logout">
			<input type="hidden" name="p" value="<?=md5($pwdsalt2 . $loguser['pass'] . $pwdsalt) ?>">
		</form><?php
	}
	echo "</table><br>";

	if ($fid) {
		$onusers = $sql->query("SELECT " . userfields() . ", `lastpost`, `lastview`
			FROM `users`
			WHERE (`lastview` > " . (time() - 300) . " OR `lastpost` > " . (time() - 300) . ") AND `lastforum`='$fid'
			ORDER BY `name`");
		$onuserlist = "";
		$onusercount = 0;
		while ($user = $sql->fetch($onusers)) {
			$onuserlog = ($user['lastpost'] <= $user['lastview']);
			$offline1 = ($onuserlog ? "" : "[");
			$offline2 = ($onuserlog ? "" : "]");
			$onuserlist .= ($onusercount ? ", " : "") . $offline1 . userlink($user) . $offline2;
			$onusercount++;
		}

		$fname = $sql->resultq("SELECT `title` FROM `forums` WHERE `id`='$fid'");
		$onuserlist = "$onusercount user" . ($onusercount != 1 ? "s" : "") . " currently in $fname" . ($onusercount > 0 ? ": " : "") . $onuserlist;

		//[Scrydan] Changed from the commented code below to save a query.
		$numbots = 0;
		$numguests = 0;
		if($result = $sql->query("SELECT COUNT(*) as guest_count, SUM(`bot`) as bot_count FROM `guests` WHERE `lastforum` = '$fid' AND `date` > '" . (time() - 300) . "'")) {
			if($data = $sql->fetch($result)) {
				$numbots = $data['bot_count'];
				$numguests = $data['guest_count'] - $numbots;
			}
		}

		if ($numguests) {
			$onuserlist .= " | $numguests guest" . ($numguests != 1 ? "s" : "");
		}
		if ($numbots) {
			$onuserlist .= " | $numbots bot" . ($numbots != 1 ? "s" : "");
		}

		?><table class="c1"><tr class="n1"><td class="b n1 center"><?=$onuserlist ?></td></tr></table><br><?php
	} else if ($showonusers) {
		//[KAWA] Copypastadaption from ABXD, with added activity limiter.
		$birthdaylimit = 86400 * 30;
		$rbirthdays = $sql->query("SELECT `birth`, " . userfields() . " FROM `users`
								WHERE `birth` LIKE '" . date('m-d') . "%' AND `lastview` > " . (time() - $birthdaylimit) . " ORDER BY `name`");
		$birthdays = array();
		while ($user = $sql->fetch($rbirthdays)) {
			$b = explode('-', $user['birth']);
			if ($b['2'] <= 0 && $b['2'] > -2)
				$p = "";
			else
				$p = "(";
			//Patch to fix 2 digit birthdays. Needs retooled to a modern datetime system. -Emuz
			if ($b['2'] <= 99 && $b['2'] > 15)
				$y = date("Y") - ($b['2'] + 1900) . ")";
			else if ($b['2'] <= 14 && $b['2'] > 0)
				$y = date("Y") - ($b['2'] + 2000) . ")";
			else if ($b['2'] <= 0 && $b['2'] > -2)
				$y = "";
			else
				$y = date("Y") - $b[2] . ")";
			$birthdays[] = userlink($user) . " " . $p . "" . $y;
		}

		$birthdaybox = '';
		if (count($birthdays)) {
			$birthdaystoday = implode(", ", $birthdays);
			$birthdaybox = "<tr class=\"n1 center\"><td class=\"b n2 center\">Birthdays today: $birthdaystoday</td></tr>";
		}

		$count['d'] = $sql->resultq("SELECT COUNT(*) FROM `posts` WHERE `date` > '" . (time() - 86400) . "'");
		$count['h'] = $sql->resultq("SELECT COUNT(*) FROM `posts` WHERE `date` > '" . (time() - 3600) . "'");
		$lastuser = $sql->fetchq("SELECT " . userfields() . " FROM `users` ORDER BY `id` DESC LIMIT 1");

		$onusers = $sql->query("SELECT " . userfields() . ", `lastpost`, `lastview` FROM `users`
							WHERE (`lastview` > " . (time() - 300) . " OR `lastpost` > " . (time() - 300) . ") ORDER BY `name`");
		$onuserlist = "";
		$onusercount = 0;
		while ($user = $sql->fetch($onusers)) {
			$onuserlog = ($user['lastpost'] <= $user['lastview']);
			$offline1 = ($onuserlog ? "" : "[");
			$offline2 = ($onuserlog ? "" : "]");
			$onuserlist.=($onusercount ? ", " : "") . $offline1 . userlink($user) . $offline2;
			$onusercount++;
		}

		$maxpostsday = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="maxpostsday"');
		$maxpostshour = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="maxpostshour"');
		$maxusers = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="maxusers"');

		if ($count['d'] > $maxpostsday) {
			$sql->query("UPDATE `misc` SET `intval`='$count[d]' WHERE `field`='maxpostsday'");
			$sql->query("UPDATE `misc` SET `intval`='" . time() . "' WHERE `field`='maxpostsdaydate'");
		}
		if ($count['h'] > $maxpostshour) {
			$sql->query("UPDATE `misc` SET `intval`='$count[h]' WHERE `field`='maxpostshour'");
			$sql->query("UPDATE `misc` SET `intval`='" . time() . "' WHERE `field`='maxpostshourdate'");
		}
		if ($onusercount > $maxusers) {
			$sql->query("UPDATE `misc` SET `intval`='$onusercount' WHERE `field`='maxusers'");
			$sql->query("UPDATE `misc` SET `intval`='" . time() . "' WHERE `field`='maxusersdate'");
			$sql->query("UPDATE `misc` SET `txtval`='" . addslashes($onuserlist) . "' WHERE `field`='maxuserstext'");
		}

		$onuserlist = "$onusercount user" . ($onusercount != 1 ? 's' : '') . ' online' . ($onusercount > 0 ? ': ' : '') . $onuserlist;

		$numbots = 0;
		$numguests = 0;
		if($result = $sql->query("SELECT COUNT(*) as guest_count, SUM(`bot`) as bot_count FROM `guests` WHERE `lastforum` = '$fid' AND `date` > '" . (time() - 300) . "'")) {
			if($data = $sql->fetch($result)) {
				$numbots = $data['bot_count'];
				$numguests = $data['guest_count'] - $numbots;
			}
		}

		if ($numguests > 0) {
			$onuserlist .= " | $numguests guest" . ($numguests != 1 ? "s" : "");
		}
		if ($numbots > 0) {
			$onuserlist .= " | $numbots bot" . ($numbots != 1 ? "s" : "");
		}

		?>
		<table class="c1">
			<?=$birthdaybox ?>
			<tr>
				<td class="b n1">
					<table style="width:100%">
						<tr>
							<td class="nb" width="170"></td>
							<td class="nb center"><span class="white-space:nowrap">
									<?=$count['t'] ?> threads and <?=$count['p'] ?> posts total.<br><?=$count['d'] ?> new posts
									today, <?=$count['h'] ?> last hour.<br>
							</span></td>
							<td class="nb right" width="170">
								<?=$count['u'] ?> registered users<br> Newest: <?=userlink($lastuser) ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="b n2 center"><?=$onuserlist ?></td>
			</tr>
		</table><br>
		<?php
	}
}

function checknumeric(&$var) {
	if (!is_numeric($var)) {
		$var = 0;
		return false;
	}
	return true;
}

function pagestats() {
	global $start, $sql;
	$time = usectime() - $start;
	echo sprintf("Page rendered in %1.3f seconds. (%dKB of memory used)", $time, memory_get_usage(false) / 1024) . "<br>
		MySQL - queries: $sql->queries, rows: $sql->rowsf/$sql->rowst, time: " . sprintf("%1.3f seconds.", $sql->time) . "<br>";
}

function noticemsg($name, $msg) {
	?><table class="c1">
		<tr class="h"><td class="b h center"><?=$name ?></td></tr>
		<tr><td class="b n1 center"><?=$msg ?></td></tr>
	</table><?php
}

function error($name, $msg) {
	pageheader('Error');
	echo "<br>";
	noticemsg($name, $msg);
	pagefooter();
	die();
}

function pagefooter() {
	global $abversion, $boardprog;
	?><br>
	<table class="c2">
		<tr>
			<td class="b n2 sfont">
  				<span style="float:right; text-align:right;">
  					<?php pagestats() ?>
				</span>
				<a href="http://github.com/rasmusolle/acmlmboard"><img src="img/poweredbyacmlm.png" title="Acmlmboard 2" style="float:left; margin-right:4px;"></a>
				Acmlmboard v<?=$abversion ?><br>
				&copy; 2005-2019 <?=$boardprog ?>
			</td>
		</tr>
	</table><?php
}

?>