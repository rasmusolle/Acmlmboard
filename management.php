<?php
require 'lib/common.php';

pageheader('Management');

$mlinks = array();
//$mlinks[] = array('url' => "updatethemes.php", 'title' => 'Update Themes');
if (has_perm("edit-forums")) 
  $mlinks[] = array('url' => "manageforums.php", 'title' => 'Manage forums');
if (has_perm("edit-ip-bans")) 
  $mlinks[] = array('url' => "ipbans.php", 'title' => 'Manage IP bans');
if (has_perm("edit-spiders")) 
  $mlinks[] = array('url' => "editspiders.php", 'title' => 'Manage spiders');
if (has_perm("edit-calendar-events")) 
  $mlinks[] = array('url' => "editevents.php", 'title' => 'Manage events');
if (has_perm('edit-smilies'))
  $mlinks[] = array('url' => "editsmilies.php", 'title' => 'Manage smilies');
if (has_perm("edit-post-icons")) 
  $mlinks[] = array('url' => "editposticons.php", 'title' => 'Manage post icons');
if (has_perm('edit-profileext'))
  $mlinks[] = array('url' => "editprofileext.php", 'title' => 'Manage extended profile fields');
if (has_perm("edit-badges")) 
  $mlinks[] = array('url' => "editbadges.php", 'title' => 'Manage badges');
if (has_perm("edit-groups")) 
  $mlinks[] = array('url' => "editgroups.php", 'title' => 'Manage groups');
if (has_perm("admin-tools-access")) 
  $mlinks[] = array('url' => "administratortools.php", 'title' => 'Administrator Tools');

//Inspired by Tierage's dashboard.php in Blargboard Plus. - SquidEmpress
$mlinkstext = '';
foreach ($mlinks as $l)
	$mlinkstext .= ($mlinkstext?' ':'').'&nbsp;<a href="' . $l['url'] . '"><input type="submit" class="submit" name=action value="' . $l['title'] . '"></a></a>';

if ($mlinkstext == '') $mlinkstext = "There's no board management tools you're allowed to use.";

?>
<table class="c1">
	<tr class="h"><td class="b">Board management tools</td></tr>
	<tr>
		<td class="b n1" align="center">
			<br><?php echo $mlinkstext; ?><br><br>
		</td>
	</tr>
</table>
<?php
pagefooter();
?>