<?php

require 'lib/common.php';

$regdis = $sql->fetchq("SELECT intval, txtval FROM misc WHERE field='regdisable'");
if ($regdis['intval'] == 1) {
	pageheader('Register');

	if ($regdis['txtval'] != "")
		$reason = $regdis['txtval'];
	else 
		$reason = "Registration is currently disabled.";

	?>
	<table class="c1">
		<tr class="h"><td class="b h" colspan="2">Registration is disabled</td></tr>
		<tr>
			<td class="b n1" align="center" width="120">
				<?php echo $reason; ?> For more information please read the board announcements
				or visit us on <a href="irc.php">IRC</a>
			</td>
		</tr>
	</table>
	<?php
	pagefooter();
	die();
}


$boardemailaddress = $sql->resultq("SELECT `emailaddress` FROM `misc` WHERE `field`='boardemail'");
if (isProxy()) {
	pageheader('Register');

	if ($regdis['txtval'] != "") 
		$reason = $regdis['txtval'];
	else 
		$reason = "Security Check Failure";

	?>
	<table class="c1">
		<tr class="h"><td class="b h" colspan="2">Registration is denied</td></tr>
		<tr>
			<td class="b n1" align="center" width="120">
				Our site has detected your IP is either a proxy, or listed as a known spammer.
				If you feel this is in error contact the board admins at <?php echo $boardemailaddress; ?>
			</td>
		</tr>
	</table>
	<?php

	pagefooter();
	die();
}

function randstr($l) {
	$str = "";
	$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz$+/~";
	for($i = 0; $i < $l; ++$i)
		$str .= $chars[rand(0, strlen($chars)-1)];
	return $str;
}

$act = (isset($_POST['action']) ? $_POST['action'] : '');
if ($act == 'Register') {
	$name = trim(stripslashes($_POST['name']));

	$cname = str_replace(array(' ',"\xC2\xA0"),'',$name);
	$cname = strtolower($cname);

	$dupe = $sql->resultp("SELECT COUNT(*) FROM users WHERE LOWER(REPLACE(REPLACE(name,' ',''),0xC2A0,''))=? OR LOWER(REPLACE(REPLACE(displayname,' ',''),0xC2A0,''))=?", array($cname,$cname));
	
	$sex = (int)$_POST['sex'];
	if ($sex < 0 || $sex > 2) $sex = 1;
	
	$timezone = $_POST['timezone'];

	$err = '';
	if ($dupe)
		$err = 'This username is already taken, please choose another.';
	elseif ($name == '' || $cname == '')
		$err = 'The username must not be empty, please choose one.';
	elseif (strlen($_POST['pass']) < 4)
		$err = 'Your password must be at least 4 characters long.';
	elseif ($_POST['pass'] != $_POST['pass2'])
		$err = "The two passwords you entered don't match.";
	elseif ($config['registrationpuzzle'] && $_POST['puzzle'] != $puzzleAnswer)
		$err = "You are either a bot or very bad at simple mathematics.";

	if (empty($err)) {
		$name = $sql->escape($name);
		$salted_password = md5($pwdsalt2 . $_POST['pass'] . $pwdsalt);
		$query_string = sprintf("INSERT INTO users (name,pass,regdate,lastview,ip,sex,timezone,fontsize,theme) VALUES ('%s', '%s', %d, %d, '%s', %d, '%s', %d, '%s');",
		$name, $salted_password, ctime(), ctime(), $userip, $sex, $timezone, $defaultfontsize, $defaulttheme);
		$res = $sql->query($query_string);
		if ($res) {
			$id = $sql->insertid();
			$sql->query("INSERT INTO usersrpg (id) VALUES ($id)");

			$ugid = 0;
			// Derp killer
			if ($id == 1 || $_POST['name'] == 'Needle') {
				$row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?", array(-1));
				$ugid = $row['id'];
			} else {
				$row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?", array(1));
				$ugid = $row['id'];
			}
			$sql->prepare("UPDATE users SET group_id=? WHERE id=?",array($ugid,$id));

			// [Mega-Mario] mark existing threads and forums as read
			$sql->prepare("INSERT INTO threadsread (uid,tid,time) SELECT ?,id,? FROM threads", array($id, ctime()));
			$sql->prepare("INSERT INTO forumsread (uid,fid,time) SELECT ?,id,? FROM forums", array($id, ctime()));

			/* count matches for IP and hash */
			//hash
			$a = $sql->fetchq("SELECT COUNT(*) as c FROM users WHERE pass='".md5($pwdsalt2.$_POST[pass].$pwdsalt)."'");
			$m_hash = $a['c']-1;
			//split the IP
			$ipparts = explode(".",$userip);
			// /32 matches
			$a = $sql->fetchq("SELECT count(*) as c FROM users WHERE ip='$userip'");
			$m_ip32 = $a['c']-1;
			// /24
			$a = $sql->fetchq("SELECT count(*) as c FROM users WHERE ip LIKE '$ipparts[0].$ipparts[1].$ipparts[2].%'");
			$m_ip24 = $a['c']-1;
			// /16
			$a = $sql->fetchq("SELECT count(*) as c FROM users WHERE ip LIKE '$ipparts[0].$ipparts[1].%'");
			$m_ip16 = $a['c']-1;

			//fancy colouring (if matches exist, make it red); references to make foreach not operate on copies
			$clist = array(&$m_hash, &$m_ip32, &$m_ip24, &$m_ip16);
			foreach($clist as &$c) {
				if($c>0) $c = "{irccolor-no}$c"; else $c="{irccolor-yes}$c";
			}

			setcookie('user', $id, 2147483647);
			setcookie('pass', packlcookie(md5($pwdsalt2 . $_POST['pass'] . $pwdsalt), implode(".", array_slice(explode(".", $_SERVER['REMOTE_ADDR']), 0, 2)) . ".*"), 2147483647);
			
			?><span style='text-align:center;'>
				If you aren't redirected, then please <a href="./">go here.</a>
				<?php echo '<meta http-equiv="refresh" content="1;url=./">'; ?>
			</span><?php
			die();
		} else {
			$err = "Registration failed: ".$sql->error();
		}
	}
}

pageheader('Register');
$listsex = array('Male','Female','N/A');
$alltz = $sql->query("SELECT name FROM `timezones`"); 

$listtimezones = array();
while ($tz = $sql->fetch($alltz)) {
	$listtimezones[$tz['name']] = $tz['name'];
}

$cap = encryptpwd($_SERVER['REMOTE_ADDR'].",".($str=randstr(6)));
if(!empty($err)) noticemsg("Error", $err);
?>
<form action="register.php" method="post">
	<table class="c1">
		<tr class="h">
			<td class="b h" colspan="2">Register</td>
		</tr><tr>
			<td class="b n1" align="center" width=120>Username:</td>
			<td class="b n2"><input type="text" name=name size=25 maxlength=25></td>
		</tr><tr>
			<td class="b n1" align="center">Password:</td>
			<td class="b n2"><input type="password" name=pass size=13 maxlength=32></td>
		</tr><tr>
			<td class="b n1" align="center">Password (again):</td>
			<td class="b n2"><input type="password" name=pass2 size=13 maxlength=32></td>
		</tr>
		<?php
		echo fieldrow('Sex',fieldoption('sex',2,$listsex));
		echo fieldrow('Timezone',fieldselect('timezone','UTC',$listtimezones));
		if ($config['registrationpuzzle']) { ?>
			<tr>
				<td class="b n1" align="center" width="120"><?php echo $puzzle; ?></td>
				<td class="b n2"><input type="text" name="puzzle" size="13" maxlength="20"></td>
			</tr>
		<?php } ?>
		<tr class="n1">
			<td class="b">&nbsp;</td>
			<td class="b">
				<input type="submit" class="submit" name="action" value="Register">
				<font class='sfont'>Please take a moment to read the <a href='faq.php'>FAQ</a> before registering.</font>
			</td>
		</tr>
	</table>
</form>
<?php pagefooter(); ?>