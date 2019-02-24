<?php

function checknumeric(&$var) {
	if (!is_numeric($var)) {
		$var = 0;
		return false;
	}
	return true;
}

function getforumbythread($tid) {
	global $sql;
	static $cache;
	return isset($cache[$tid]) ? $cache[$tid] : $cache[$tid] = $sql->resultq("SELECT forum FROM threads WHERE id='$tid'");
}

?>