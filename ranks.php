<?php
require "lib/common.php";

if (!isset($_GET['rankset']))
	$getrankset = 1;
else
	$getrankset = $_GET['rankset'];
if (!is_numeric($getrankset))
	$getrankset = 1;
$totalranks = $sql->resultq("SELECT count(*) FROM `ranksets` WHERE id > '0';");

if ($getrankset < 1 || $getrankset > $totalranks)
	$getrankset = 1; // Should be made dynamic based on rank sets.

$linkuser = array();
$allusers = $sql->query("SELECT " . userfields() . ", `posts`, `lastview` FROM `users` WHERE `rankset` = " . $getrankset . " ORDER BY `id`");

while ($row = $sql->fetch($allusers)) { $linkuser[$row['id']] = $row; }
$blockunknown = true;

$rankposts = array();

if (!isset($_GET['action'])) 
	$_GET['action'] = 'needle';
if (!isset($_POST['action']))
	$_POST['action'] = 'needle';

$allranks = $sql->query("SELECT * FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
						ORDER BY `p`");
$ranks = $sql->query("SELECT * FROM `ranks` `r` LEFT JOIN `ranksets` `rs` ON `rs`.`id`=`r`.`rs`
						WHERE `rs`='$getrankset' ORDER BY `p`");

while ($rank = $sql->fetch($allranks)) {
	if ($rank['rs'] == $getrankset)
		$rankposts[] = $rank['p'];
	if (!isset($rankselection))
		$rankselection = "<a href=\"ranks.php?rankset=$rank[id]\">$rank[name]</a>";
	else {
		if ($usedranks[$rank['rs']] != true)
			$rankselection .= " | <a href=\"ranks.php?rankset=$rank[id]\">$rank[name]</a>";
	}
	$usedranks[$rank['rs']] = true;
}
if (isset($_GET['rankset'])) {
	if (!isset($_GET['showinactive']))
		$inaclnk = " | <a href=\"ranks.php?rankset=" . $_GET['rankset'] . "&showinactive=1\">Show Inactive</a>";
	else
		$inaclnk = " | <a href=\"ranks.php?rankset=" . $_GET['rankset'] . "\">Hide Inactive</a>";
} else {
	if (!isset($_GET['showinactive']))
		$inaclnk = " | <a href=\"ranks.php?showinactive=1\">Show Inactive</a>";
	else
		$inaclnk = " | <a href=\"ranks.php\">Hide Inactive</a>";
}

pageheader("Rankset Listing");
?>
			<table class="c1" style="width:auto">
				<tr class="h">
					<td class="b">Rank Set</td>
				</tr>
				<tr class="n1">
					<td class="bn1"><?php echo "$rankselection$inaclnk"; ?></td>
				</tr>
			</table>
<table class="c1">
	<tr class="h">
		<td class="b" width="150px">Rank</td>
		<td class="b" width="50px">Posts</td>
		<td class="b" width="100px">Users On Rank</td>
		<td class="b">Users On Rank</td>
	</tr>
<?php
$i = 1;

while ($rank = $sql->fetch($ranks)) {
	$neededposts = $rank['p'];
	$nextneededposts = $rankposts[$i];
	$usercount = 0;
	$idlecount = 0;
	foreach ($linkuser as $user) {
		$climbingagain = "";
		$postcount = $user['posts'];
		if ($postcount > 5100) {
			$postcount = $postcount - 5100;
			$climbingagain = " (Climbing Again (5100))";
		}
		if (($postcount >= $neededposts) && ($postcount < $nextneededposts)) {
			if (isset($_GET['showinactive']) || $user['lastview'] > (time() - (86400 * $inactivedays))) {
				$usersonthisrank = '';
				if ($usersonthisrank)
					$usersonthisrank .= ", ";
				$usersonthisrank .= userlink_by_id($user['id']) . $climbingagain;
			} else
				$idlecount++;
			$usercount++;
		}
	}
	if (isset($rank['image'])) {
		$rankimage .= "<img src=\"img/ranksets/$rank[dirname]/$rank[image]\">";
	}
	?>
	<tr>
		<td class="b n1">
			<?php echo (($usercount - $idlecount) || $blockunknown == false ? "$rank[str]" : "???"); ?>
		</td>
		<td class="b n2" align="center">
			<?php echo (($usercount - $idlecount) || $blockunknown == false ? "$neededposts" : "???"); ?>
		</td>
		<td class="b n2" align="center"><?php echo $usercount; ?></td>
		<td class="b n1" align="center">
			<?php echo (isset($usersonthisrank) ? $usersonthisrank : '') . ($idlecount ? "($idlecount inactive)" : ""); ?>
		</td>
	</tr>
	<?php
	
	unset($rankimage, $usersonthisrank);
	$i++;
}
?></table><?php
pagefooter();
?>