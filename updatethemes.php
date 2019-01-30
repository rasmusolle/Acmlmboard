<?php

require("lib/common.php");
pageheader();

echo "Scanning...<br>";

$themes = glob('theme/*', GLOB_ONLYDIR);
sort($themes);
foreach ($themes as $f) {
	$themename = explode("/",$f);
	$snarf = file_get_contents("theme/$themename[1]/$themename[1].css");
	$snarf = str_replace("\r\n", "\n", $snarf);
	if (preg_match("~/* META\n(.*?)\n(.*?)\n*/\n~s", $snarf, $matches)) {
		$n = $matches[1];
		$d = substr($matches[2], 0, -2);
		echo "Got a hit on ".$f."! Its name is \"$n\".<br>";
		$f2 = str_replace(".css", "", str_replace(".php", "", $themename[1]));
		if ($d != "")
			$newlist[] = array($n, $f2, $d);
		else
			$newlist[] = array($n, $f2);
	}
}

file_put_contents("themes_serial.txt", serialize($newlist));

echo "We now have ".count($newlist)." themes.";

pagefooter();

?>