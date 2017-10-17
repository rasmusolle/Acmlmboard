<?php
include ('lib/common.php');
include ('lib/layout.php');

function monospace($t) {
	$t = str_replace(" ", "&nbsp;", $t);
	return "<font face='courier new'>" . $t . "</font>";
}

// partially involves code pirated on the internets, but I was lazy.
function diff($old, $new) {
	// split the source text into arrays of lines
	$t1 = explode("\n", $old);
	$x = array_pop($t1);
	if ($x > '')
		$t1[] = "$x\n\\ No newline at end of file";
	$t2 = explode("\n", $new);
	$x = array_pop($t2);
	if ($x > '')
		$t2[] = "$x\n\\ No newline at end of file";
	
	// build a reverse-index array using the line as key and line number as value
	// don't store blank lines, so they won't be targets of the shortest distance
	// search
	foreach ($t1 as $i => $x)
		if ($x > '')
			$r1[$x][] = $i;
	foreach ($t2 as $i => $x)
		if ($x > '')
			$r2[$x][] = $i;
	
	$a1 = 0;
	$a2 = 0; // start at beginning of each list
	$actions = array();
	
	// walk this loop until we reach the end of one of the lists
	while ($a1 < count($t1) && $a2 < count($t2)) {
		// if we have a common element, save it and go to the next
		if ($t1[$a1] == $t2[$a2]) {
			$actions[] = 4;
			$a1++;
			$a2++;
			continue;
		}
		
		// otherwise, find the shortest move (Manhattan-distance) from the
		// current location
		$best1 = count($t1);
		$best2 = count($t2);
		$s1 = $a1;
		$s2 = $a2;
		while (($s1 + $s2 - $a1 - $a2) < ($best1 + $best2 - $a1 - $a2)) {
			$d = - 1;
			foreach ((array) @$r1[$t2[$s2]] as $n) {
				if ($n >= $s1) {
					$d = $n;
					break;
				}
			}
			if ($d >= $s1 && ($d + $s2 - $a1 - $a2) < ($best1 + $best2 - $a1 - $a2)) {
				$best1 = $d;
				$best2 = $s2;
			}
			$d = - 1;
			foreach ((array) @$r2[$t1[$s1]] as $n)
				if ($n >= $s2) {
					$d = $n;
					break;
				}
			if ($d >= $s2 && ($s1 + $d - $a1 - $a2) < ($best1 + $best2 - $a1 - $a2)) {
				$best1 = $s1;
				$best2 = $d;
			}
			$s1++;
			$s2++;
		}
		while ($a1 < $best1) {
			$actions[] = 1;
			$a1++;
		} // deleted elements
		while ($a2 < $best2) {
			$actions[] = 2;
			$a2++;
		} // added elements
	}
	
	// we've reached the end of one list, now walk to the end of the other
	while ($a1 < count($t1)) {
		$actions[] = 1;
		$a1++;
	} // deleted elements
	while ($a2 < count($t2)) {
		$actions[] = 2;
		$a2++;
	} // added elements
	  
	// and this marks our ending point
	$actions[] = 8;
	
	// now, let's follow the path we just took and report the added/deleted
	// elements into $out.
	$op = 0;
	$x0 = $x1 = 0;
	$y0 = $y1 = 0;
	$out = array();
	foreach ($actions as $act) {
		if ($act == 1) {
			$op |= $act;
			$x1++;
			continue;
		}
		if ($act == 2) {
			$op |= $act;
			$y1++;
			continue;
		}
		if ($op > 0) {
			$xstr = ($x1 == ($x0 + 1)) ? $x1 : ($x0 + 1) . ",$x1";
			$ystr = ($y1 == ($y0 + 1)) ? $y1 : ($y0 + 1) . ",$y1";
			// modified by blackhole89 starting here
			if ($op == 1 || $op == 3)
				$out[] = "<table border=0 width=100%><tr class=\"n1\"><td class=\"b n3\">";
			while ($x0 < $x1) {
				$out[] = monospace('- ') . $t1[$x0];
				$x0++;
			} // deleted elems
			if ($op == 2)
				$out[] = "<table border=0 width=100%><tr class=\"n1\"><td class=\"b n1\" width=100%>";
			elseif ($op == 3)
				$out[] = "</table><table border=0 width=100%><tr class=\"n1\"><td class=\"b n1\">";
			while ($y0 < $y1) {
				$out[] = monospace('+ ') . $t2[$y0];
				$y0++;
			} // added elems
			$out[] = '</table>';
		}
		$out[] = monospace('  ') . $t1[$x1];
		$x1++;	$x0 = $x1;
		$y1++;	$y0 = $y1;
		$op = 0;
	}
	$out[] = '';
	return join("\n", $out);
}
?>