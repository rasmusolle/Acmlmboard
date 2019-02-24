<?php

/* Returns associative array of specified request values, unslashed, regardless
 * of PHP.ini setting
 */

function request_variables($varlist) {
	$quoted = get_magic_quotes_gpc();
	$out = array();
	foreach ($varlist as $key) {
		if (isset($_REQUEST[$key])) {
			$out[$key] = $_REQUEST[$key];
		}
	}
	return $out;
}

function redirect($url, $msg) {
	header("Set-Cookie: pstbon=" . $msg . "; Max-Age=60; Version=1");
	header("Location: " . $url);
	die();
	return 0;
}

?>