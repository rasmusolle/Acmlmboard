<?php

include('lib/common.php');

$posts=$sql->query("SELECT pt.* FROM posts p "
	."LEFT JOIN poststext pt ON p.id=pt.id "
	."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) " //SQL barrel roll
	."WHERE ISNULL(pt2.id) AND p.thread=6719 AND pt.id<>139811");

while ($p=$sql->fetch($posts)) {
	$lines = explode("\n",$p[text]);
	foreach ($lines as $l) {
		if (preg_match("/^([0-9]+)\s/si",$l,&$m)) { $votes[(int)$m[0]]++; }
	}
}

arsort($votes);
$c = 0;

foreach($votes as $id=>$count) {
	echo "$count | $id | "; echo $sql->resultq("SELECT CONCAT(artist,' - ',album,' - ',title) FROM ab_radio.songs WHERE id=$id"); echo "<br>";
	$c++;
	if ($c==1) $list="$id";
	else if($c<=20) $list.=", $id";
	if ($c==20) echo "-----------<br>";
}

?>