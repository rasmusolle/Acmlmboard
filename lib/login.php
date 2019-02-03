<?php
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

?>