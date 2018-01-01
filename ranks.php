<?php
require "lib/common.php";

// TODO: fix pstbon
$rdmsg = "";
if (isset($_COOKIE['pstbon'])) {
	header("Set-Cookie: pstbon=" . $_COOKIE['pstbon'] . "; Max-Age=1; Version=1");
	$rdmsg = '<script>function dismiss() { document.getElementById("postmes").style[\'display\'] = "none"; }</script>
<div id="postmes" onclick="dismiss()" title="Click to dismiss."><br>
<table cellspacing="0" class="c1" width="100%" id="edit"><tr class="h"><td class="b h">';
	if ($_COOKIE['pstbon'] == 1) {
		$rdmsg .= 'Rankset Added<div style="float: right"><a style="cursor: pointer;" onclick="dismiss()">[x]</a></td></tr>
<tr><td class="b n1" align="left">The rankset has been successfully added.</td></tr></table></div>';
	} elseif ($_COOKIE['pstbon'] == 2) {
		$rdmsg .= "Rankset Edited<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
<tr><td class=\"b n1\" align=\"left\">The rankset has been successfully edited.</td></tr></table></div>";
	} elseif ($_COOKIE['pstbon'] == 3) {
		$rdmsg .= "Rankset Deleted<div style=\"float: right\"><a style=\"cursor: pointer;\" onclick=\"dismiss()\">[x]</a></td></tr>
" . "<tr><td class=\"b n1\" align=\"left\">The rankset has been successfully deleted.</td></tr></table></div>";
	}
}

if (!isset($_GET['rankset']))
	$getrankset = 1;
else
	$getrankset = $_GET['rankset']; // Changed to allow the Kirby Rank to show.
if (!is_numeric($getrankset))
	$getrankset = 1;
$totalranks = $sql->resultq("SELECT count(*) FROM `ranksets` WHERE id > '0';");

if ($getrankset < 1 || $getrankset > $totalranks)
	$getrankset = 1; // Should be made dynamic based on rank sets.

$linkuser = array();
$allusers = $sql->query("SELECT " . userfields() . ", `posts`, `minipic`, `lastview` FROM `users` WHERE `rankset` = " . $getrankset . " ORDER BY `id`");

while ($row = $sql->fetch($allusers)) { $linkuser[$row['id']] = $row; }
$blockunknown = true;

$rankposts = array();

if (!isset($_GET['action'])) 
	$_GET['action'] = 'needle';
if (!isset($_POST['action']))
	$_POST['action'] = 'needle';

if (($_GET['action'] == 'addrankset' || $_GET['action'] == 'editrankset' || $_GET['action'] == 'deleterankset' || $_GET['action'] == 'editranks') && ! has_perm('edit-ranks')) {
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
}

if ($_GET['action'] == 'deleterankset' && ($getrankset < 2 || $getrankset > $totalranks)) {
	error("Error", "The Mario, Dots, and None ranksets may not be deleted on the board.<br> <a href=./>Back to main</a>");
}

if ($_GET['action'] == 'deleterankset' && $getrankset >= 2 && $getrankset != unpacksafenumeric($_GET['token'])) {
	error("Error", "Invalid token.<br> <a href=./>Back to main</a>");
}

if (($_GET['action'] == 'addrankset' && $_POST['action'] == 'Submit' && $_POST['newname'] == '') || ($_GET['action'] == 'editrankset' && $_POST['action'] == 'Submit' && $_POST['editname'] == '')) {
	error("Error", "Please enter a name for this rankset.<br> <a href=./>Back to main</a>");
}

if ($_GET['action'] == 'addrankset' && $_POST['action'] == 'Submit' && has_perm('edit-ranks')) {
	$newname = $sql->escape($_POST['newname']);
	$getrankset = $sql->resultq("SELECT MAX(id) FROM ranksets");
	if (! $getrankset)
		$getrankset = 0;
	$getrankset++;
	$sql->prepare("INSERT INTO ranksets (`id`,`name`) VALUES (?,?)", array($getrankset, $newname));
	redirect("ranks.php", 1);
}

if ($_GET['action'] == 'editrankset' && $_POST['action'] == 'Submit' && has_perm('edit-ranks')) {
	$getrankset = intval($getrankset);
	$editname = $sql->escape($_POST['editname']);
	$sql->prepare("UPDATE ranksets SET `name`=?  WHERE id=?", array($editname, $getrankset));
	redirect("ranks.php", 2);
}

if ($_GET['action'] == 'deleterankset' && $getrankset >= 2 && $getrankset == unpacksafenumeric($_GET['token']) && has_perm('edit-ranks')) {
	$getrankset = intval($getrankset);
	$sql->prepare("DELETE FROM ranksets WHERE id=?", array($getrankset));
	redirect("ranks.php", 3);
}

$editlinks = "";
if (has_perm("edit-ranks")) {
	if ($getrankset != 1) {
		$deletelink = " |  
                   <a href=\"ranks.php?action=deleterankset&rankset=$getrankset&token=" . urlencode(packsafenumeric($getrankset)) . "\" onclick=\"if (!confirm('Really delete this rankset?')) return false;\">Delete Rank</a>";
	}
	$editlinks = " | 
                   <a href=\"ranks.php?action=addrankset\">Add Rank</a> | 
                   <a href=\"ranks.php?action=editrankset&rankset=$getrankset\">Edit Rank</a>".(isset($deletelink) ? $deletelink : '');
}

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

if ($_GET['action'] == 'addrankset' && has_perm('"edit-ranks')) {
	pageheader("Rankset Listing");
	?>
	<form action='ranks.php?action=addrankset' method='post' enctype='multipart/form-data'>
		<table class="c1">
			<?php echo catheader('New Rankset'); ?>
			<tr>
				<td class="b n1" align="center">Name:</td>
				<td class="b n2"><input type="text" name='newname' size='40' maxlength='255' class='right'></td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b"><input type="submit" class="submit" name=action value='Submit'></td>
			</tr>
		</table>
	</form>
	<?php
	pagefooter();
	die();
}

if ($_GET['action'] == 'editrankset' && has_perm('"edit-ranks')) {
	pageheader("Rankset Listing");
	$editrankset = $sql->resultq("SELECT `name` FROM `ranksets` WHERE `id`='$getrankset'");
	?>
	<form action="ranks.php?action=editrankset&rankset=<?php echo $getrankset; ?>" method="post" enctype="multipart/form-data">
		<table class="c1">
			<?php echo catheader('Edit Rankset'); ?>
			<tr>
				<td class="b n1" align="center">Name:</td>
				<td class="b n2"><input type="text" name="editname" size="40" maxlength="255" value="<?php echo $editrankset; ?>" class="right"></td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b"><input type="submit" class="submit" name="action" value="Submit"></td>
			</tr>
		</table>
	</form>
	<?php
	pagefooter();
	die();
}

pageheader("Rankset Listing");
if (isset($_COOKIE['pstbon'])) {
	echo $rdmsg;
}
?>
<table>
	<tr>
		<td>
			<table class="c1">
				<tr class="h">
					<td class="b n1" width="50%">Rank Set</td>
				</tr>
				<tr class="n1">
					<td class="bn1"><?php echo "$rankselection$inaclnk$editlinks"; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br>
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
				if ($user['minipic'])
					$minpic = "<img style='vertical-align:text-bottom' src='" . $user['minipic'] . "'/> ";
				else
					$minpic = "";
				$usersonthisrank .= $minpic . userlink_by_id($user['id']) . $climbingagain;
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