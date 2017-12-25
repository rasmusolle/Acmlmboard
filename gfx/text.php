<?php
include 'fontlib.php';

header('Content-type: image/gif');

$im = imagecreatetruecolor(8 * strlen($_GET['str']), 8);

$black = imagecolorallocate($im, 255, 0, 255);

imagefilledrectangle($im, 0, 0, 8 * strlen($_GET['str']), 8, $black);

$fontW = fontc((isset($r1) ? $r1 : ''), (isset($g1) ? $g1 : ''), (isset($b1) ? $b1 : ''),
			   (isset($r2) ? $r2 : ''), (isset($g2) ? $g2 : ''), (isset($b2) ? $b2 : ''),
			   (isset($r3) ? $r3 : ''), (isset($g3) ? $g3 : ''), (isset($b3) ? $b3 : ''));
frender($im, $fontW, 0, 0, 0, $_GET['str']);

imagecolortransparent($im, $black);

imagegif($im);

?>
