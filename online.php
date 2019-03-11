<?php
require('lib/common.php');

pageheader('Online users');

$time = (isset($_GET['time']) ? $_GET['time'] : null);
checknumeric($time);

if (!$time) $time = 300;

$users = $sql->query("SELECT * FROM users WHERE lastview > ".(time()-$time)." ORDER BY lastview DESC");
?>
<table class="c1" style="width:auto">
	<tr class="h"><td class="b">Online users during the last <?=str_replace('.', '', timeunits2($time)) ?>:</td></tr>
	<tr class="n1"><td class="b n1"><?=timelink(60).'|'.timelink(300).'|'.timelink(3600).'|'.timelink(86400) ?></td></tr>
</table><br>
<table class="c1">
	<tr class="h">
		<td class="b h" width="30">#</td>
		<td class="b h" width="230">Name</td>
		<td class="b h" width="80">Last view</td>
		<td class="b h">URL</td>
		<?=(has_perm('view-post-ips') ? '<td class="b h" width="120">IP</td>' : '') ?>
	</tr>
<?php
for ($i = 1; $user = $sql->fetch($users); $i++) {
	$tr = ($i % 2 ? 'n1' : 'n2');
	?>
	<tr class="<?=$tr ?> center">
		<td class="b"><?=$i ?>.</td>
		<td class="b left"><?=userlink($user) ?></td>
		<td class="b"><?=date($loguser['timeformat'], $user['lastview']) ?></td>
		<td class="b left"><?=($user['url'] ? "<a href=$user[url]>" . str_replace(array('%20','_'), ' ', $user['url']) . '</a>' : '-') ?></td>
		<?=(has_perm("view-post-ips") ? '<td class="b">'.$user['ip'].'</td>':'') ?>
	</tr>
<?php } ?>
</table><?php

pagefooter();

function timelink($timex) {
	global $time;
	return ($time == $timex ? " " . timeunits2($timex) . " " : " <a href=online.php?time=$timex>" . timeunits2($timex) . '</a> ');
}