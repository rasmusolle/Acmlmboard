<?php

require("lib/common.php");
pageheader();


echo "Scanning for new themes...<br>";

$files = scandir("css");
sort($files);
foreach($files as $f)
{
	if($f[0] == ".")
		continue;
	$snarf = file_get_contents("css/".$f);
	$snarf = str_replace("\r\n", "\n", $snarf);
	if(preg_match("~/* META\n(.*?)\n(.*?)\n*/\n~s", $snarf, $matches))
	{
		$n = $matches[1];
		$d = substr($matches[2], 0, -2);
		//echo "Got a hit on ".$f."! Its name is \"$n\", description \"$d\".<br>";
		$f2 = str_replace(".css", "", str_replace(".php", "", $f));
		if($d != "")
			$newlist[] = array($n, $f2, $d);
		else
			$newlist[] = array($n, $f2);
	}
}

file_put_contents("themes_serial.txt", serialize($newlist));

echo "We now have ".count($newlist)." themes.";

pagefooter();

?>