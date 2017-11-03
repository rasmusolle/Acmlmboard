<?php

require 'lib/common.php';

$rdmsg = "";
if (isset($_COOKIE['pstbon'])) {
	header("Set-Cookie: pstbon=" . $_COOKIE['pstbon'] . "; Max-Age=1; Version=1");
	$rdmsg = "<script language=\"javascript\">
	function dismiss()
	{
		document.getElementById(\"postmes\").style['display'] = \"none\";
	}
</script>
	<div id=\"postmes\" onclick=\"dismiss()\" title=\"Click to dismiss.\"><br>
" . "<table cellspacing=\"0\" class=\"c1\" width=\"100%\" id=\"edit\"><tr class=\"h\"><td class=\"b h\">";
	if ($_COOKIE['pstbon'] == -1) {
		$rdmsg.="You are now registered!<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">Please login.</td></tr></table></div>";
	}
}

$act = (isset($_POST['action']) ? $_POST['action'] : 'needle');
if ($act == 'Login') {
	if ($userid = checkuser($_POST['name'], md5($pwdsalt2 . $_POST['pass'] . $pwdsalt))) {
		setcookie('user', $userid, 2147483647);
		setcookie('pass', packlcookie(md5($pwdsalt2 . $_POST[pass] . $pwdsalt), implode(".", array_slice(explode(".", $_SERVER['REMOTE_ADDR']), 0, 2)) . ".*"), 2147483647);
		die(header("Location: ./"));
	} else {
		$err = "Invalid username or password, cannot log in.";
	}
	$echo = "  <td class=\"b n1\" align=\"center\">$echo</td>";
} elseif ($act == 'logout') {
	setcookie('user', 0);
	setcookie('pass', '');
	die(header("Location: ./"));
}

pageheader('Login');
if (isset($_COOKIE['pstbon'])) { echo $rdmsg; }
if (isset($err))
	noticemsg("Error", $err);
echo "<table cellspacing=\"0\" class=\"c1\">
<form action=login.php method=post>
" . "  <tr class=\"h\">
" . "    <td class=\"b h\" colspan=2>Login</td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\" width=120>Username:</td>
" . "    <td class=\"b n2\"><input type=\"text\" name=name size=25 maxlength=25></td>
" . "  <tr>
" . "    <td class=\"b n1\" align=\"center\">Password:</td>
" . "    <td class=\"b n2\"><input type=\"password\" name=pass size=13 maxlength=32> - <a href=\"resetpassword.php\">Lost password?</a></td>
" . "  <tr class=\"n1\">
" . "    <td class=\"b\">&nbsp;</td>
" . "    <td class=\"b\"><input type=\"submit\" class=\"submit\" name=action value=Login></td>
" . " </form>
" . "</table>
";
pagefooter();
?>
