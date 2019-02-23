<?php
require("lib/common.php");

if (!isset($_GET['rankset']) || !is_numeric($_GET['rankset'])) $getrankset = 1;
else $getrankset = $_GET['rankset'];

$linkuser = array();
$allusers = $sql->query("SELECT " . userfields() . ", `posts`, `lastview` FROM `users` WHERE `rankset` = $getrankset ORDER BY `id`");

while ($row = $sql->fetch($allusers)) { $linkuser[$row['id']] = $row; }

$rankselection = '';
$ranksetcount = 0;

foreach ($rankset_names as $rankset) {
	if ($ranksetcount != 0) {
		if ($ranksetcount == 1) 
			$rankselection .= "<a href=\"ranks.php?rankset=$ranksetcount\">$rankset</a>";
		else
			$rankselection .= " | <a href=\"ranks.php?rankset=$ranksetcount\">$rankset</a>";
	}
	$ranksetcount++;
}

pageheader("Ranks");

if ($ranksetcount != 2) { ?>
<table class="c1" style="width:auto;text-align:center">
	<tr class="h"><td class="b">Rank Set</td></tr>
	<tr class="n1"><td class="bn1"><?=$rankselection ?></td></tr>
</table><br>
<?php } ?>
<table class="c1">
	<tr class="h">
		<td class="b" width="150px">Rank</td>
		<td class="b" width="40px">Posts</td>
		<td class="b" width="50px">Users</td>
		<td class="b">Users On Rank</td>
	</tr>
<?php
$i = 1;

foreach ($rankset_data[$rankset_names[$getrankset]] as $rank) {
	$neededposts = $rank['p'];
	if (isset($rankset_data[$rankset_names[$getrankset]][$i]['p']))
		$nextneededposts = $rankset_data['Mario'][$i]['p'];
	else
		$nextneededposts = 2147483647;
	$usercount = 0;
	$idlecount = 0;
	foreach ($linkuser as $user) {
		$postcount = $user['posts'];
		if (($postcount >= $neededposts) && ($postcount < $nextneededposts)) {
			if (isset($_GET['showinactive']) || $user['lastview'] > (time() - (86400 * $inactivedays))) {
				$usersonthisrank = '';
				if ($usersonthisrank)
					$usersonthisrank .= ", ";
				$usersonthisrank .= userlink_by_id($user['id']);
			} else
				$idlecount++;
			$usercount++;
		}
	}
	?><tr>
		<td class="b n1"><?php echo (($usercount - $idlecount) ? $rank['str'] : '???'); ?></td>
		<td class="b n2" align="center"><?php echo (($usercount - $idlecount) ? $neededposts : '???'); ?></td>
		<td class="b n2" align="center"><?php echo $usercount; ?></td>
		<td class="b n1" align="center"><?php echo (isset($usersonthisrank) ? $usersonthisrank : '') . ($idlecount ? "($idlecount inactive)" : ""); ?></td>
	</tr><?php
	unset($usersonthisrank);
	$i++;
}
?></table><?php
pagefooter();
?>