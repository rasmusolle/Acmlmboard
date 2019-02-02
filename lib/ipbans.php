<?php
$sql->query('DELETE FROM ipbans WHERE expires<'.ctime().' AND expires>0');

$r = $sql->query("SELECT * FROM ipbans WHERE '$userip' LIKE ipmask");
if (@$sql->numrows($r) > 0) {
	if ($loguser) $sql -> query("UPDATE `users` SET `ipbanned` = '1' WHERE `id` = '$loguser[id]'");
	else $sql->query("UPDATE `guests` SET `ipbanned` = '1' WHERE `ip` = '". $_SERVER['REMOTE_ADDR'] ."'");

	$bannedgroup = $sql->resultq("SELECT id FROM `group` WHERE `banned`=1");

	$i = $sql->fetch($r);
	if ($i['hard']) {
		//hard IP ban; always restrict access fully

		pageheader('IP banned');
		echo '<table class="c1"><tr class="n2"><td class="b n1" align="center">Sorry, but your IP address has been banned.</td></tr></table>';
		pagefooter();
		die();
    } else if (!$i['hard'] && (!$log || $loguser['group_id'] == $bannedgroup['id'])) {
		//"soft" IP ban allows non-banned users with existing accounts to log on
		if (!strstr($_SERVER['PHP_SELF'], "login.php")) {
			pageheader('IP restricted');
			echo '<table class="c1"><tr class="n2"><td class="b n1" align="center">Access from your IP address has been limited.<br><a href=login.php>Login</a></table>';
			pagefooter();
			die();
		}
	}
}
?>