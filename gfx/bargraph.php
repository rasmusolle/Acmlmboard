<?php
require('fontlib.php');

if (!isset($_GET['n']) || !isset($_GET['b']) || !isset($_GET['g']) || !isset($_GET['r']) || !isset($_GET['z'])) {
	die('not enough parameters');
}

header('Content-type: image/png');

$im = imagecreatetruecolor(690, 20);
$n = $_GET['n'];
$b = $_GET['b'];
$g = $_GET['g'];
$r = $_GET['r'];
$z = $_GET['z'];
if ($n == 0)
	$n = 1;

imagealphablending($im, FALSE);
imagesavealpha($im, TRUE);

function cap($n) {
	return ($n > 255 ? 255 : ($n < 0 ? 0 : $n));
}

$cblank = imagecolorallocatealpha($im, 0, 0, 0, 127);
$cuhl = imagecolorallocate($im, cap($r + 80), cap($g + 80), cap($b + 80));
$chl = imagecolorallocate($im, cap($r + 50), cap($g + 50), cap($b + 50));
$cstd = imagecolorallocate($im, $r, $g, $b);
$cshadow = imagecolorallocate($im, cap($r - 80), cap($g - 80), cap($b - 80));
$calpha = imagecolorallocatealpha($im, 0, 0, 0, 110);
$calpha2 = imagecolorallocatealpha($im, 255, 255, 255, 105);
$cwhite = imagecolorallocate($im, 255, 255, 255);

imagefilledrectangle($im, 0, 0, 690, 20, $cblank);

imagefilledrectangle($im, 0, 5, 600 * $z / $n, 15, $chl);
imagefilledrectangle($im, 1, 6, 600 * $z / $n - 1, 14, $cstd);

$str = "";
$str = sprintf("%.1f%% ($z)", 100 * $z / $n);

$fontW = fontc(255, 255, 255, 210, 210, 210, 0, 0, 0);
frender($im, $fontW, floor(600 * $z / $n) + 5, 7, 0, $str);

imagesavealpha($im, FALSE);
imagealphablending($im, TRUE);
for ($i = 0; $i <= 600; $i += 10) {
	imagefilledrectangle($im, $i + 0, 0, $i + 4, 20, $calpha);
	imagefilledrectangle($im, $i + 0, $i % 60 ? 18 : ($i % 300 ? 14 : 0), $i + 0, 20, $calpha2);
}
imagealphablending($im, FALSE);
imagesavealpha($im, TRUE);

imagepng($im);

?>
