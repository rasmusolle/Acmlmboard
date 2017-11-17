<?php
require 'lib/common.php';

$daynames = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
$monthnames = array(1=>'January','February','March','April','May','June',
  						'July','August','September','October','November','December');

$today = getdate(ctime());

if (isset($_REQUEST['m']) && is_numeric($_REQUEST['m'])) $month = $_REQUEST['m'];
else $month = $today['mon'];

if (isset($_REQUEST['y']) && is_numeric($_REQUEST['y'])) $year = $_REQUEST['y'];
else $year = $today['year'];

if (isset($_REQUEST['d']) && is_numeric($_REQUEST['d'])) $day = $_REQUEST['d'];
else if ($year == $today['year'] && $month == $today['mon']) $day = $today['mday'];
else $day = 0;

if ($year < -1000000) error('Error', 'The board didn\'t exist back in prehistory!');
if ($year > 1000000) error('Error', 'The board will not exist for that long!');

$mtstamp = mktime(0,0,0,$month,1,$year);
$mdays = intval(date('t', $mtstamp));
$wday = intval(date('w', $mtstamp));

//Fetch birthdays - uses the same crude but effective method as the old one
$bdaytext = array();
$bdayres = $sql->query('SELECT ' . userfields() . ',birth FROM users WHERE birth != -1');
while ($bdayarr = $sql->fetch($bdayres)) {
	$bdaydecode=explode('-',$bdayarr['birth']);
	if ($bdaydecode[0] == $month) {
		$age = $year - $bdaydecode[2];
		$t = userlink($bdayarr);
		if ($age > 0 && !$bdaydecode['2'] <= 0) {
			$t .= " turns $age";
		} else if ($bdaydecode['2'] <= 0) {
			$t .= "'s birthday";
		} else if ($age < 0) {
			$t .= " is born in ".(-$age)." year".(($age!=-1)?'s':'');
		} else {
			$t .= " is born";
		}
		if (isset($bdaytext[$bdaydecode[1]])) {
			$bdaytext[$bdaydecode[1]] .= '<br/>'.$t;
		} else {
			$bdaytext[$bdaydecode[1]] = $t;
		}
	}
}

$eventtext = array();
$eventres = $sql->query("SELECT * FROM events e LEFT JOIN users u ON u.id=e.user WHERE year = '$year' AND month = $month");
while ($eventarr = $sql->fetch($eventres)) {
	$text = $eventarr['event_title']." - ".userlink($eventarr);
	$eventtext[$eventarr['day']] = $text;
}

pageheader('Calendar');
echo '<table class="c1">
		<tr>
			<td class="b n1" colspan=7 style="text-align:center;">' . $monthnames[$month] . ' ' . $year.'</td>
		</tr>
		<tr class="h">';

for ($w = 0; $w < 7; $w++) echo '<td class="b h" width="14%">'.$daynames[$w].'</td>';
    
echo '</tr><tr style="height: 80px;">';

if ($wday > 0) echo '<td class="b n2" colspan="'.$wday.'"></td>';

for ($mday = 1; $mday <= $mdays; $mday++, $wday++) {
	if ($wday > 6) {
		$wday = 0;
		echo '</tr><tr style="height: 80px">';
	}
	$l = ($mday == $day) ? "b n3" : (($wday == 0 || $wday == 6) ? "b n2" : "b n1");
	echo "<td class=\"$l\" align=\"left\" width=\"14%\" valign=\"top\">$mday";
	$dnum=str_pad($mday,2,"0",STR_PAD_LEFT);
	if (isset($bdaytext[$dnum])) echo '<br/>'.$bdaytext[$dnum];
	if (isset($eventtext[$dnum])) echo '<br/>'.$eventtext[$dnum];
	echo "</td>\n";
}

if ($wday < 7) { echo '<td class="b n2" colspan='.(7-$wday).'></td>'; }

echo '</tr><tr><td class="b n1" align="center" colspan=7> Month:';

for ($i = 1; $i <= 12; $i++) {
	if ($i == $month) {
		echo " $i\n";
	} else {
		echo " <a href=\"calendar.php?m=$i&amp;y=$year\">$i</a>\n";
	}
}

echo " | Year:\n";

for ($i = $year-2; $i <= $year+2; $i++) {
	if ($i == $year) {
		echo " $i\n";
	} else if ($i < -1000000 || $i > 1000000) {
		// nothing
	} else {
		echo " <a href=\"calendar.php?m=$month&amp;y=$i\">$i</a>\n";
	}
}

echo "</td></tr></table>";

pagefooter();
?>