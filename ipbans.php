<?php
require('lib/common.php');
pageheader('IP bans');

$action = (isset($_GET['action']) ? $_GET['action'] : 'needle');
$what = (isset($_GET['what']) ? $_GET['what'] : 'needle');

function ipfmt($a) {
	$expl = explode(".",$a);
	$dot = "<span~style='color:#808080'>.</span>";
	for ($i = 0; $i < 4; $i++) {
		if (!isset($expl[$i])) {
			$expl[$i] = '*';
		}
	}
	return str_replace("~"," ",str_replace(" ","&nbsp;",sprintf("%3s%s%3s%s%3s%s%3s",$expl[0],$dot,$expl[1],$dot,$expl[2],$dot,$expl[3])));
}

if (!has_perm('edit-ip-bans')) {
	noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
} else {
	if ($action == "del") {
		$data = explode(",",decryptpwd($what));
		$sql->query("DELETE FROM ipbans WHERE ipmask='$data[0]' AND expires='$data[1]'");
	} else if ($action == "add") {
		if ($_POST['ipmask']) {
			echo (isset($_POST['hard']) ? 1 : 0);
			$hard = $_POST['hard'];
			$expires = ($_POST['expires'] > 0 ? ($_POST['expires'] + time()) : 0);
			$sql->query("INSERT INTO ipbans (ipmask,hard,expires,banner,reason) VALUES "
				."('$_POST[ipmask]','$hard','$expires','" . addslashes($loguser['name']) . "','$_POST[reason]')");
		} else {
			$err = "You must enter an IP mask";
		}
	}
	$ipbans = $sql->query("SELECT * FROM ipbans");
	if (isset($err)) noticemsg("Error", $err);
	?><form action="ipbans.php?action=add" method="post">
		<table class="c1">
			<tr class="h"><td class="b h" colspan="9">New IP ban</td></tr>
			<tr>
				<td class="b n1">IP mask</td>
				<td class="b n2"><input type="text" name="ipmask"></td>
				<td class="b n1">Hard?</td>
				<td class="b n2"><input type="checkbox" name="hard" value="1"></td>
				<td class="b n1">Expires?</td>
				<td class="b n2"><?=fieldselect("expires",0,array("600"=>"10 minutes",
							"3600" => "1 hour", "10800" => "3 hours", "86400" => "1 day",
							"172800" => "2 days", "259200" => "3 days", "604800" => "1 week",
							"1209600" => "2 weeks", "2419200" => "1 month", "4838400" => "2 months",
							"0" => "never")) ?></td>
				<td class="b n1">Comment</td>
				<td class="b n2" style="width:100%"><input type="text" name="reason" style="width:100%">
				<td class="b n2 center" colspan="8"><input type="submit" class="submit" value='Add IP ban'>
		</table>
	</form><br>
	<table class="c1">
		<tr class="h"><td class="b h" colspan="6">IP bans</td></tr>
		<tr class="c">
			<td class="b">IP mask</td>
			<td class="b">Hard</td>
			<td class="b">Expires</td>
			<td class="b">Banner</td>
			<td class="b" width="100%">Comment</td>
			<td class="b" width=20></td>
	<?php
	while ($i = $sql->fetch($ipbans)) {
		?>
		<tr>
			<td class="b n1"><span style="font-family:'Courier New',monospace"><?=ipfmt($i['ipmask']) ?></span></td>
			<td class="b n2 center"><span style="color:<?=($i['hard'] ? "red\">Yes" : "green\">No") ?>"></span></td>
			<td class="b n2 center">
				<?=($i['expires'] ? cdate($loguser['dateformat'],$i['expires'])."&nbsp;".cdate($loguser['timeformat'],$i['expires']) : "never") ?>
			</td>
			<td class="b n2 center"><?=$i['banner'] ?></td>
			<td class="b n2"><?=stripslashes($i['reason']) ?></td>
			<td class="b n2 center">
				<a href="ipbans.php?action=del&what=<?=urlencode(encryptpwd($i['ipmask'].",".$i['expires'])) ?>"><img src="img/smilies/no.png" align=absmiddle></a>
			</td>
		</tr>
		<?php
	}
	?></table><?php
}

pagefooter();
?>