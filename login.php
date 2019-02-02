<?php
require('lib/common.php');

$act = (isset($_POST['action']) ? $_POST['action'] : 'needle');
if ($act == 'Login') {
	if ($userid = checkuser($_POST['name'], md5($pwdsalt2 . $_POST['pass'] . $pwdsalt))) {
		setcookie('user', $userid, 2147483647);
		setcookie('pass', packlcookie(md5($pwdsalt2 . $_POST['pass'] . $pwdsalt),
				implode(".", array_slice(explode(".", $_SERVER['REMOTE_ADDR']), 0, 2)) . ".*"), 2147483647);
		die(header("Location: ./"));
	} else {
		$err = "Invalid username or password, cannot log in.";
	}
	$echo = '<td class="b n1" align="center">'.$echo.'</td>';
} elseif ($act == 'logout') {
	setcookie('user', 0);
	setcookie('pass', '');
	die(header("Location: ./"));
}

pageheader('Login');
if (isset($err))
	noticemsg("Error", $err);
?>
<form action="login.php" method="post"><table class="c1">
<tr class="h"><td class="b h" colspan=2>Login</td></tr>
<tr>
	<td class="b n1" align="center" width=120>Username:</td>
	<td class="b n2"><input type="text" name=name size=25 maxlength=25></td>
</tr><tr>
	<td class="b n1" align="center">Password:</td>
	<td class="b n2"><input type="password" name=pass size=25 maxlength=32></td>
</tr><tr class="n1">
	<td class="b">&nbsp;</td>
	<td class="b"><input type="submit" class="submit" name="action" value="Login"></td>
</tr>
</table></form>
<?php pagefooter(); ?>