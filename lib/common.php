<?php
require "lib/function.php";

header("Content-type: text/html; charset=utf-8");

//[Scrydan] Added these three variables to make editing quicker.
$boardprog = "Acmlm, Emuz, <a href='credits.php'>et al</a>.";
$abdate = "7/13/2015";
$abversion = "2.5.3<i>pre</i> <span style=\"color: #BCDE9A; font-style: italic; font-size:8pt;\">Development</span>";

if ($config['sqlconfig']) {
	// Fallback to the config.php settings in the event that the SQL settings don't load properly or aren't set.
	$configsql = array();
	foreach ($config as $cfg_key => $cfg_value) {
		$configsql[$cfg_key] = array('intval' => (int) $cfg_value, 'txtval' => $cfg_value);
	}

	if ($res = $sql->query("SELECT * from `misc`")) {
		while ($row = $sql->fetch($res)) {
			$configsql[$row['field']] = $row;
		}
	}

	$trashid = $configsql['trashid']['intval'];
	$boardtitle = $configsql['boardtitle']['txtval'];
	$defaulttheme = $configsql['defaulttheme']['txtval'];
	$defaultfontsize = $configsql['defaultfontsize']['intval'];

	$avatardimx = $configsql['avatardimx']['intval'];
	$avatardimy = $configsql['avatardimy']['intval'];

	$config['topposts'] = $configsql['topposts']['intval'];
	$config['topthreads'] = $configsql['topthreads']['intval'];

	$config['memberlistcolorlinks'] = $configsql['memberlistcolorlinks']['intval'];

	$config['threadprevnext'] = $configsql['threadprevnext']['intval'];

	$config['displayname'] = $configsql['displayname']['intval'];
	$config['perusercolor'] = $configsql['perusercolor']['intval'];
	$config['useshadownccss'] = $configsql['useshadownccss']['intval'];
	$config['nickcolorcss'] = $configsql['nickcolorcss']['intval'];

	$config['atnname'] = $configsql['atnname']['txtval'];
}

$userip = $_SERVER['REMOTE_ADDR'];
$userfwd = addslashes(getenv('HTTP_X_FORWARDED_FOR')); //We add slashes to that because the header is under users' control
$url = getenv("SCRIPT_NAME");
if ($q = getenv("QUERY_STRING"))
	$url.="?$q";

require "lib/login.php";

$a = $sql->fetchq("SELECT `intval`,`txtval` FROM `misc` WHERE `field`='lockdown'");
if ($a['intval']) {
	//lock down
	if (has_perm('bypass-lockdown'))
		echo "<h1><font color=\"red\"><center>LOCKDOWN!! LOCKDOWN!! LOCKDOWN!!</center></font></h1>";
	else {
		include "lib/locked.php";
		die();
	}
}

if (!$log) {
	$loguser = array();
	$loguser['id'] = 0;
	$loguser['group_id'] = 1;
	$loguser['tzoff'] = 0;
	$loguser['timezone'] = "UTC";
	$loguser['fontsize'] = $defaultfontsize; //2/22/2007 xkeeper - guests have "normal" by default, like everyone else
	$loguser['dateformat'] = "m-d-y";
	$loguser['timeformat'] = "h:i A";
	$loguser['signsep'] = 0;
	$loguser['theme'] = $defaulttheme;
	$loguser['ppp'] = 20;
	$loguser['tpp'] = 20;

	if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE 6.0") !== false)
		$loguser['theme'] = "minerslament";
}

$flocalmod = $sql->fetchq("SELECT `uid` FROM `forummods`");
if ($loguser['id'] == $flocalmod['uid']) {
	$loguser['modforums'] = array();
	$modf = $sql->query("SELECT `fid` FROM `forummods` WHERE `uid`='$loguser[id]'");
	while ($m = $sql->fetch($modf)) {
		$loguser['modforums'][$m['fid']] = 1;
	}
}

require "lib/timezone.php";
dobirthdays(); //Called here to account for timezone bugs.

if ($loguser['ppp'] < 1)
	$loguser['ppp'] = 20;
if ($loguser['tpp'] < 1)
	$loguser['tpp'] = 20;

//2007-02-19 blackhole89 - needs to be here because it requires loguser data


//Unban users whose tempbans have expired. - SquidEmpress
$defaultgroup = $sql->resultq("SELECT id FROM `group` WHERE `default`=1");
$sql->query('UPDATE users SET group_id=' . $defaultgroup . ', title="", tempbanned="0" WHERE tempbanned<' . ctime() . ' AND tempbanned>0');

$dateformat = "$loguser[dateformat] $loguser[timeformat]";

$bots = array();
$bota = $sql->query("SELECT `bot_agent` FROM `robots`");
while ($robots = $sql->fetch($bota)) {
	$bots[] = $robots['bot_agent'];
}
$bot = 0;

if (str_replace($bots, "x", $_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT']) {
	$bot = 1;
}
if ($bot) {
	load_bot_permset();
}
if (substr($url, 0, strlen("$config[path]rss.php")) != "$config[path]rss.php") {
	$sql->query("DELETE FROM `guests` WHERE `ip`='$userip' OR `date`<" . (ctime() - $config['oldguest']));
	if ($log) {
		//AB-SPECIFIC
		//if (has_perm('track-ip-change') && ($userip != ($oldip = $loguser['ip']))) { }

		$sql->query("UPDATE `users` SET `lastview`=" . ctime() . ",`ip`='$userip', `ipfwd`='$userfwd', `url`='" . (isssl() ? "!" : "") . addslashes($url) . "', `ipbanned`='0' WHERE `id`='$loguser[id]'");
	} else {
		$sql->query('INSERT INTO `guests` (`date`, `ip`, `url`, `useragent`, `bot`) VALUES (' . ctime() . ",'$userip','" . (isssl() ? "!" : "") . addslashes($url) . "', '" . addslashes($_SERVER['HTTP_USER_AGENT']) . "', '$bot')");
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

	if(!empty($_SERVER['HTTP_REFERER'])) {
		$ref = $_SERVER['HTTP_REFERER'];
		$ref2 = substr($ref, 0, 25);
		if ($ref && !strpos($ref2, $config['address'])) {
			$sql->query("INSERT INTO `ref` SET `time`='" . ctime() . "', `userid`='$loguser[id]', `urlfrom`='" . addslashes($ref) . "',
											  `urlto`='" . addslashes($url) . "', `ipaddr`='" . $_SERVER['REMOTE_ADDR'] . "'");
		}
	}

	if (!$bot) {
		$sql->query("UPDATE `misc` SET `intval`=`intval`+1 WHERE `field`='views'");
	} else {
		$sql->query('UPDATE `misc` SET `intval`=`intval`+1 WHERE `field`="botviews"');
	}

	$views = $sql->resultq("SELECT `intval` FROM `misc` WHERE `field`='views'");
	$botviews = $sql->resultq("SELECT `intval` FROM `misc` WHERE `field`='botviews'");

	if (($views + 100) % 1000000 <= 200) {
		$sql->query("INSERT INTO `views` SET `view`=$views, `user`='$loguser[id]', `time`='" . ctime() . "'");
	}

	$count = $sql->fetchq("	SELECT
								(SELECT COUNT(*) FROM users) u,
								(SELECT COUNT(*) FROM threads) t,
								(SELECT COUNT(*) FROM posts) p");
	$date = date("m-d-y", ctime());
	$sql->query("REPLACE INTO `dailystats` (`date`, `users`, `threads`, `posts`, `views`)
                 VALUES ('$date', '$count[u]', '$count[t]', '$count[p]', '$views')");

	//2/21/2007 xkeeper - adding, uh, hourlyviews
	$sql->query("INSERT INTO `hourlyviews` (`hour`, `views`)
                 VALUES (" . floor(ctime() / 3600) . ",1)
                 ON DUPLICATE KEY UPDATE `views`=`views`+1");
}

//[KAWA] ABXD-style theme system
$themelist = unserialize(file_get_contents("themes_serial.txt"));

//Config definable theme override
if ($config['override_theme'] && !has_special_perm("bypass-theme-override")) { //If defined in config & current user does not have the special bypass perm; use the theme defined.
	$theme = $config[override_theme];
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



if ($config['override_logo'] && !has_special_perm("bypass-logo-override")) //Config override for the logo file
	$logofile = $config[override_logo];
elseif (is_file("theme/" . $theme . "/logo.png"))
	$logofile = "theme/" . $theme . "/logo.png";
else
	$logofile = $defaultlogo;

require "lib/ipbans.php";

$statusimageset = '';

if (is_file("theme/" . $theme . "/status/new.png"))
	$statusimageset = "theme/" . $theme . "/status/";

$feedicons = "";

/*
  Salvaged from "Xkeeper's Nifty Page-o-Hacks". Why? I don't really know, however it's a nice bit of code
  for 'just in case' purposes I guess. Basically it'll get axed when we clean up the other fragments anyway.
  -Emuz

  if(strstr($url,"UNION%20SELECT") && $loguser[power]<3) {
  $sql->query("INSERT INTO ipbans VALUES ('$REMOTE_ADDR',1,'','automatic','UNION SELECT')");
  print "(insert sound of something blowing up here)";
  die();
  }
 */

//2/21/2007 xkeeper - todo: add $forumid attribute (? to add "forum user is in" and markread links
// also added number_format to views
// also changed the title to be "pagetitle - boardname" and not vice-versa
function pageheader($pagetitle = "", $fid = 0) {
	global  $dateformat, $sql, $log, $loguser, $sqlpass, $views, $botviews, $sqluser, $boardtitle, $extratitle, $boardlogo, $homepageurl, $theme, $themefile,
	$logofile, $url, $config, $feedicons, $favicon, $showonusers, $count, $lastannounce, $lastforumannounce, $inactivedays, $pwdsalt, $pwdsalt2;

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

	if (has_perm("edit-attentions-box") && $log)
		$ae = "(<a href=\"editattn.php\">edit</a>)";
	else
		$ae = "";

	$extratitle = "
                     <table cellspacing=\"0\" class=\"c1\" width=\"100%\" align=\"center\">
                       <tr class=\"h\">
                          <td class=\"b h\">$config[atnname] $ae</td>
                        <tr class=\"n2\" align=\"center\">
                          <td class=\"b sfont\">" . ($t = $sql->resultq("SELECT `txtval` FROM `misc` WHERE `field`='attention'")) . "
                          </td>
                     </table>";
	if ($t == "")
		$extratitle = $ae;

	if ($extratitle) {
		$boardlogo = "
             <table cellspacing=\"0\" width=100%>
               <tr align=\"center\">
                 <td class=\"b\" style=\"border:none!important\" valign=\"center\"><a href=\"$homepageurl\"><img src=\"$logofile\"></a></td>
                 <td class=\"b\" style=\"border:none!important\" valign=\"center\" width=\"300\">
                   $extratitle
                 </td>
             </table>";
	}

	if (isssl()) {
		$ssllnk = "<img src=\"img/sslon.gif\" title=\"SSL enabled\">";
	} else if (!$config['showssl']) {
		$ssllnk = "";
	} else {
		$ssllnk = "<a href=\"$config[sslbase]$url\" title=\"View in SSL mode\"><img border=\"0\" src=\"img/ssloff.gif\"></a>";
	}

	if ($log) {
		$logbar = $loguser;
		$logbar['showminipic'] = 1;
	}

	echo "<!DOCTYPE html>
      <html>
      <head>
      <title>$pagetitle$boardtitle</title>
      $config[meta]
      <link rel=\"icon\" type=\"image/png\" href=\"$favicon\">
      <style>

      </style>
      <link rel=\"stylesheet\" href=\"theme/$theme/$themefile\">
      <link rel=\"stylesheet\" href=\"theme/common.css\">
      <link href=\"lib/prettify/sunburst.css\" type=\"text/css\" rel=\"stylesheet\" />
	  <link rel='alternate' type='application/rss+xml' title='RSS Feed' href='rss.php'>
      <script type=\"text/javascript\" src=\"lib/prettify/prettify.js\"></script>
      </head>
      <body style=\"font-size:$loguser[fontsize]%\" onload=\"prettyPrint()\">
      <table cellspacing=\"0\" class=\"c1\">
        <tr class=\"nt n2\" align=\"center\">
        <td class=\"b n1\" align=\"center\" colspan=\"3\">$boardlogo</td>
        </tr>
        <tr class=\"n2\" align=\"center\">
          <td class=\"b\">
          <div style=\"width: 150px\">Views: 
          <span title=\"And " . number_format($botviews) . " views by search engine spiders.\">" . number_format($views) . "</span></div></td>
          <td class=\"b\" width=\"100%\"><span style=\"float:right\">
		    <a href='rss.php'><img src='img/feed.png' style='margin-right:5px' title='RSS Feed'></a>$ssllnk</span>
            <a href=\"./\">Main</a>
          | <a href=\"faq.php\">FAQ</a>
          | <a href=\"irc.php\">IRC</a>
          | <a href=\"memberlist.php\">Memberlist</a>
          | <a href=\"activeusers.php\">Active users</a>
          | <a href=\"thread.php?time=86400\">Latest posts</a>
          | <a href=\"stats.php\">Stats</a>
          | <a href=\"ranks.php\">Ranks</a>
          | <a href=\"online.php\">Online users</a>
          | <a href=\"search.php\">Search</a>
          </td>
          <td class=\"b\"><div style=\"width: 150px\">" . cdate($dateformat, ctime()) . "</div></td>
        <tr class=\"n1\" align=\"center\">
          <td class=\"b\" colspan=\"3\">
            " . ($log ? userlink($logbar) : "Guest") . ": ";

	if ($log) {
		//2/25/2007 xkeeper - framework laid out. Naturally, the SQL queries are a -mess-. --;
		$pmsgs = $sql->fetchq("SELECT `p`.`id` `id`, `p`.`date` `date`, " . userfields('u', 'u') . "
                            FROM `pmsgs` `p`
                            LEFT JOIN `users` `u` ON `u`.`id`=`p`.`userfrom`
                            WHERE `p`.`userto`='$loguser[id]'
                            ORDER BY `date` DESC LIMIT 1");

		$unreadpms = $sql->resultq("SELECT COUNT(*) FROM `pmsgs` WHERE `userto`='$loguser[id]' AND `unread`=1 AND `del_to`='0'");
		$totalpms = $sql->resultq("SELECT COUNT(*) FROM `pmsgs` WHERE `userto`='$loguser[id]' AND `del_to`='0'");

		if ($unreadpms) {
			$status = rendernewstatus("n");
			$unreadpms = " ($unreadpms new)";
		} else {
			$status = "";
			$unreadpms = "";
		}

		if (has_perm('view-own-pms')) {
			if ($unreadpms) {
				$pmimage = "gfx/pm.png";
			} else {
				$pmimage = "gfx/pm-off.png";
			}
			$pmsgbox = '<a href="private.php"><img src="' . $pmimage . '" width="16" height="9" alt="Private messages" title="Private message"></a> ' . $unreadpms . ' | ';
		} else {
			$pmsgbox = "";
		}
	}
	if (!empty($pmsgbox)) {
		echo $pmsgbox;
	}

	//mark forum read
	checknumeric($fid);
	if ($fid)
		$markread = array("url" => "index.php?action=markread&fid=$fid", "title" => "Mark forum read");
	else
		$markread = array("url" => "index.php?action=markread&fid=all", "title" => "Mark all forums read");

	$userlinks = array();
	$ul = 0;

	if (!$log) {
		if (has_perm("register"))
			$userlinks[$ul++] = array('url' => "register.php", 'title' => 'Register');
		if (has_perm("view-login"))
			$userlinks[$ul++] = array('url' => "login.php", 'title' => 'Login');
	}
	else {
		if (has_perm("logout"))
			$userlinks[$ul++] = array('url' => "javascript:document.logout.submit()", 'title' => 'Logout');
	}
	if ($log) {
		if (has_perm("update-own-profile"))
			$userlinks[$ul++] = array('url' => "editprofile.php", 'title' => 'Edit profile');
		if (has_perm("update-own-moods"))
			$userlinks[$ul++] = array('url' => "mood.php", 'title' => 'Edit mood avatars');
		if (has_perm('manage-board'))
			$userlinks[$ul++] = array('url' => 'management.php', 'title' => 'Management');
		if (has_perm("mark-read"))
			$userlinks[$ul++] = $markread;
	}

	$c = 0;

	foreach ($userlinks as $k => $v) {
		if ($c > 0) {
			echo " | ";
		}
		echo "<a href=\"{$v['url']}\">{$v['title']}</a>";
		$c++;
	}

	echo "</td>";
	if ($log) {
		?><form action="login.php" method="post" name="logout">
			<input type="hidden" name="action" value="logout">
			<input type="hidden" name="p" value="<?php echo md5($pwdsalt2 . $loguser['pass'] . $pwdsalt); ?>">
		</form><?php
	}

	echo "
			</table>
			<br>";

	$hiddencheck = "AND `hidden`='0' ";
	if (has_perm('view-hidden-users')) {
		$hiddencheck = "";
	}

	if ($fid) {
		$onusers = $sql->query("SELECT " . userfields() . ", `lastpost`, `lastview`, `minipic`, `hidden`
                              FROM `users`
                              WHERE (`lastview` > " . (ctime() - 300) . " OR `lastpost` > " . (ctime() - 300) . ") $hiddencheck AND `lastforum`='$fid'
                              ORDER BY `name`");
		$onuserlist = "";
		$onusercount = 0;
		while ($user = $sql->fetch($onusers)) {
			$user['showminipic'] = 1;
			$onuserlog = ($user['lastpost'] <= $user['lastview']);
			$offline1 = ($onuserlog ? "" : "[");
			$offline2 = ($onuserlog ? "" : "]");
			$onuserlist .= ($onusercount ? ", " : "") . $offline1 . ($user['hidden'] ? "(" . userlink($user) . ")" : userlink($user)) . $offline2;
			$onusercount++;
		}

		$fname = $sql->resultq("SELECT `title` FROM `forums` WHERE `id`='$fid'");
		$onuserlist = "$onusercount user" . ($onusercount != 1 ? "s" : "") . " currently in $fname" . ($onusercount > 0 ? ": " : "") . $onuserlist;

		//[Scrydan] Changed from the commented code below to save a query.
		$numbots = 0;
		$numguests = 0;
		if($result = $sql->query("SELECT COUNT(*) as guest_count, SUM(`bot`) as bot_count FROM `guests` WHERE `lastforum` = '$fid' AND `date` > '" . (ctime() - 300) . "'")) {
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

		?>
		<table class="c1">
			<tr class="n1">
				<td class="b n1" align="center"><?php echo $onuserlist; ?></td>
			</tr>
		</table><br>
		<?php
	} else if ($showonusers) {
		//[KAWA] Copypastadaption from ABXD, with added activity limiter.
		$birthdaylimit = 86400 * $inactivedays;
		$rbirthdays = $sql->query("SELECT `birth`, " . userfields() . "
                                 FROM `users`
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
			$birthdaybox = "
        <tr class=\"n1\" align=\"center\">
        <td class=\"b n2\" align=\"center\">
        Birthdays today: $birthdaystoday";
		}

		$count['d'] = $sql->resultq("SELECT COUNT(*) FROM `posts` WHERE `date` > '" . (ctime() - 86400) . "'");
		$count['h'] = $sql->resultq("SELECT COUNT(*) FROM `posts` WHERE `date` > '" . (ctime() - 3600) . "'");
		$lastuser = $sql->fetchq("SELECT " . userfields() . " FROM `users` ORDER BY `id` DESC LIMIT 1");

		$hiddencheck = "AND `hidden`='0' ";
		if (has_perm('view-hidden-users')) {
			$hiddencheck = "";
		}

		$onusers = $sql->query("SELECT " . userfields() . ", `lastpost`, `lastview`, `minipic`, `hidden` FROM `users`
                           WHERE (`lastview` > " . (ctime() - 300) . " OR `lastpost` > " . (ctime() - 300) . ") $hiddencheck ORDER BY `name`");
		$onuserlist = "";
		$onusercount = 0;
		while ($user = $sql->fetch($onusers)) {
			$user['showminipic'] = 1;
			$onuserlog = ($user['lastpost'] <= $user['lastview']);
			$offline1 = ($onuserlog ? "" : "[");
			$offline2 = ($onuserlog ? "" : "]");
			$onuserlist.=($onusercount ? ", " : "") . $offline1 . ($user['hidden'] ? '(' . userlink($user) . ')' : userlink($user)) . $offline2;
			$onusercount++;
		}

		$maxpostsday = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="maxpostsday"');
		$maxpostshour = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="maxpostshour"');
		$maxusers = $sql->resultq('SELECT `intval` FROM `misc` WHERE `field`="maxusers"');

		if ($count['d'] > $maxpostsday) {
			$sql->query("UPDATE `misc` SET `intval`='$count[d]' WHERE `field`='maxpostsday'");
			$sql->query("UPDATE `misc` SET `intval`='" . ctime() . "' WHERE `field`='maxpostsdaydate'");
		}
		if ($count['h'] > $maxpostshour) {
			$sql->query("UPDATE `misc` SET `intval`='$count[h]' WHERE `field`='maxpostshour'");
			$sql->query("UPDATE `misc` SET `intval`='" . ctime() . "' WHERE `field`='maxpostshourdate'");
		}
		if ($onusercount > $maxusers) {
			$sql->query("UPDATE `misc` SET `intval`='$onusercount' WHERE `field`='maxusers'");
			$sql->query("UPDATE `misc` SET `intval`='" . ctime() . "' WHERE `field`='maxusersdate'");
			$sql->query("UPDATE `misc` SET `txtval`='" . addslashes($onuserlist) . "' WHERE `field`='maxuserstext'");
		}

		$onuserlist = "$onusercount user" . ($onusercount != 1 ? 's' : '') . ' online' . ($onusercount > 0 ? ': ' : '') . $onuserlist;

		$numbots = 0;
		$numguests = 0;
		if($result = $sql->query("SELECT COUNT(*) as guest_count, SUM(`bot`) as bot_count FROM `guests` WHERE `lastforum` = '$fid' AND `date` > '" . (ctime() - 300) . "'")) {
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

		$activeusers = $sql->resultq("SELECT COUNT(*) FROM `users` WHERE `lastpost` > '" . (ctime() - 86400) . "'");
		$activethreads = $sql->resultq("SELECT COUNT(*) FROM `threads` WHERE `lastdate` > '" . (ctime() - 86400) . "'");

		?>
		<table class="c1">
			<?php echo $birthdaybox; ?>
			<tr>
				<td class="b n1">
					<table style="width:100%">
						<tr>
							<td class="nb" width="150"></td>
							<td class="nb" align="center"><span class="white-space:nowrap">
									<?php echo $count['t']; ?> threads and <?php echo $count['p']; ?> posts total.<br><?php echo $count['d']; ?> new posts
									today, <?php echo $count['h']; ?> last hour.<br>
							</span></td>
							<td class="nb" align="right" width="150">
								<?php echo $count['u']; ?> registered users<br> Newest: <?php echo userlink($lastuser); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="b n2" align="center"><?php echo $onuserlist; ?></td>
			</tr>
		</table><br>
		<?php
	}
}

function pagestats() {
	global $start, $sql;
	$time = usectime() - $start;
	echo sprintf("Page rendered in %1.3f seconds. (%dKB of memory used)", $time, memory_get_usage(false) / 1024) . "<br>
          MySQL - queries: $sql->queries, rows: $sql->rowsf/$sql->rowst, time: " . sprintf("%1.3f seconds.", $sql->time) . "<br>";
}

function noticemsg($name, $msg) {
	?>
	<table class="c1">
		<tr class="h">
			<td class="b h" align="center"><?php echo $name; ?></td>
		</tr>
		<tr>
			<td class="b n1" align="center"><?php echo $msg; ?></td>
		</tr>
	</table><br>
	<?php
}

function error($name, $msg) {
	global $abversion, $abdate, $boardprog;
	pageheader('Error');
	echo "<br>";
	noticemsg($name, $msg);
	pagefooter();
	die();
}

function pagefooter() {
	global $abversion, $abdate, $boardprog;
	?><br>
	<table class="c2">
		<tr>
			<td class="left b n2">
  				<span style="float:right; text-align:right;">
  					<?php pagestats(); ?>
				</span>
				<a href="http://github.com/rasmusolle/acmlmboard"><img src="img/poweredbyacmlm.png" title="Acmlmboard 2" style="float:left; margin-right:4px;"></a>
				Acmlmboard v<?php echo $abversion . ' (' . $abdate . ')'; ?><br>
				&copy; 2005-2015 <?php echo $boardprog; ?>
			</td>
		</tr>
	</table>
	<?php
}

?>