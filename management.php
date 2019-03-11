<?php
require('lib/common.php');

pageheader('Management');

$mlinks = array();
//$mlinks[] = array('url' => "updatethemes.php", 'title' => 'Update Themes');
if (has_perm("edit-forums"))
	$mlinks[] = array('url' => "manageforums.php", 'title' => 'Manage forums');
if (has_perm("edit-ip-bans"))
	$mlinks[] = array('url' => "ipbans.php", 'title' => 'Manage IP bans');
if (has_perm("edit-groups"))
	$mlinks[] = array('url' => "editgroups.php", 'title' => 'Manage groups');
if (has_perm("edit-attentions-box"))
	$mlinks[] = array('url' => "editattn.php", 'title' => 'Edit news box');

//Inspired by Tierage's dashboard.php in Blargboard Plus. - SquidEmpress
$mlinkstext = '';
foreach ($mlinks as $l)
	$mlinkstext .= ($mlinkstext?' ':'').'&nbsp;<a href="' . $l['url'] . '"><input type="submit" class="submit" name=action value="' . $l['title'] . '"></a></a>';

if ($mlinkstext == '') $mlinkstext = "There's no board management tools you're allowed to use.";

?>
<table class="c1">
	<tr class="h"><td class="b">Board management tools</td></tr>
	<tr>
		<td class="b n1 center">
			<br><?=$mlinkstext ?><br><br>
		</td>
	</tr>
</table>
<?php
pagefooter();
?>