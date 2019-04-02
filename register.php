<?php
require('lib/common.php');

$regdis = $sql->fetchq("SELECT intval, txtval FROM misc WHERE field = 'regdisable'");
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
			<td class="b n1 center" width="120">
				<?=$reason ?> For more information please read the board announcements.
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

	$cname = str_replace([' ',"\xC2\xA0"],'',$name);
	$cname = strtolower($cname);

	$dupe = $sql->resultp("SELECT COUNT(*) FROM users WHERE LOWER(REPLACE(REPLACE(name,' ',''),0xC2A0,''))=? OR LOWER(REPLACE(REPLACE(displayname,' ',''),0xC2A0,''))=?", [$cname,$cname]);

	$gender = (int)$_POST['gender'];
	if ($gender < 0 || $gender > 2) $gender = 1;

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
		$salted_password = md5($pwdsalt2 . $_POST['pass'] . $pwdsalt);
		$res = $sql->prepare("INSERT INTO users (name,pass,regdate,lastview,ip,gender,timezone,theme) VALUES (?,?,?,?,?,?,?);",
			$name, $salted_password, time(), time(), $userip, $gender, $timezone, $defaulttheme);
		if ($res) {
			$id = $sql->insertid();

			$ugid = 0;
			// Derp killer
			if ($id == 1 || $_POST['name'] == 'Needle') {
				$row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?", [-1]);
				$ugid = $row['id'];
			} else {
				$row = $sql->fetchp("SELECT id FROM `group` WHERE `default`=?", [1]);
				$ugid = $row['id'];
			}
			$sql->prepare("UPDATE users SET group_id=? WHERE id=?",[$ugid,$id]);

			// [Mega-Mario] mark existing threads and forums as read
			$sql->prepare("INSERT INTO threadsread (uid,tid,time) SELECT ?,id,? FROM threads", [$id, time()]);
			$sql->prepare("INSERT INTO forumsread (uid,fid,time) SELECT ?,id,? FROM forums", [$id, time()]);

			setcookie('user', $id, 2147483647);
			setcookie('pass', packlcookie(md5($pwdsalt2 . $_POST['pass'] . $pwdsalt), implode(".", array_slice(explode(".", $_SERVER['REMOTE_ADDR']), 0, 2)) . ".*"), 2147483647);

			?><span style='text-align:center;'>
				If you aren't redirected, then please <a href="./">go here.</a>
				<?='<meta http-equiv="refresh" content="1;url=./">' ?>
			</span><?php
			die();
		} else {
			$err = "Registration failed: ".$sql->error();
		}
	}
}

pageheader('Register');
$listgender = ['Male','Female','N/A'];

$listtimezones = [];
foreach (timezone_identifiers_list() as $tz) {
	$listtimezones[$tz] = $tz;
}

$cap = encryptpwd($_SERVER['REMOTE_ADDR'].",".($str=randstr(6)));
if(!empty($err)) noticemsg("Error", $err);
?>
<form action="register.php" method="post">
	<table class="c1">
		<tr class="h">
			<td class="b h" colspan="2">Register</td>
		</tr><tr>
			<td class="b n1 center" width=150>Username:</td>
			<td class="b n2"><input type="text" name=name size=25 maxlength=25></td>
		</tr><tr>
			<td class="b n1 center">Password:</td>
			<td class="b n2"><input type="password" name=pass size=25 maxlength=32></td>
		</tr><tr>
			<td class="b n1 center">Password (again):</td>
			<td class="b n2"><input type="password" name=pass2 size=25 maxlength=32></td>
		</tr>
		<?php
		echo fieldrow('Gender',fieldoption('gender',2,$listgender));
		echo fieldrow('Timezone',fieldselect('timezone','UTC',$listtimezones));
		if ($config['registrationpuzzle']) { ?>
			<tr>
				<td class="b n1 center" width="120"><?=$puzzle ?></td>
				<td class="b n2"><input type="text" name="puzzle" size="25" maxlength="20"></td>
			</tr>
		<?php } ?>
		<tr class="n1">
			<td class="b"></td>
			<td class="b">
				<input type="submit" class="submit" name="action" value="Register">
				<span class='sfont'>Please take a moment to read the <a href='faq.php'>FAQ</a> before registering.</span>
			</td>
		</tr>
	</table>
</form>
<?php pagefooter();