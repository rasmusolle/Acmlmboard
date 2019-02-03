<?php
require('lib/common.php');
pageheader('Active users');
  
if (isset($_GET['time'])) {
	$time = $_GET['time'];
} else {
	$time = 86400;
}
  
checknumeric($time);
if ($time < 1)
	$time = 86400;

$query = 'SELECT '.userfields('u').',u.posts,u.regdate,COUNT(*) num '
	.'FROM users u '
	.'LEFT JOIN posts p ON p.user=u.id '
	.'WHERE p.date>' . (ctime() - $time)
	.' GROUP BY u.id ORDER BY num DESC';
$users = $sql->query($query);

?>
<table class="c1" style="width:auto">
	<tr class="h"><td class="b">Active users during the last <?php echo timeunits2($time); ?>:
	<tr class="n1"><td class="b n1">
<?php echo timelink(3600).'|'.timelink(86400).'|'.timelink(604800).'|'.timelink(2592000); ?>
</table><br>
<table class="c1">
	<tr class="h">
		<td class="b h" width="30">#</td>
		<td class="b h">Username</td>
		<td class="b h" width="200">Registered on</td>
		<td class="b h" width="50">Posts</td>
		<td class="b h" width="50">Total</td>
	</tr>
<?php
$post_total = 0;
$post_overall = 0;
$j = 0;
$tr = 'n1';
for($i = 1; $user = $sql->fetch($users); $i++) {
	$post_total += $user['num'];
	$post_overall += $user['posts'];
	$tr = ($i % 2 ? 'n1': 'n2');
	?>
	<tr class="<?php echo $tr; ?>" align="center">
		<td class="b"><?php echo $i; ?>.</td>
		<td class="b" align="left"><?php echo userlink($user); ?></td>
		<td class="b"><?php echo cdate($dateformat,$user['regdate']); ?></td>
		<td class="b"><b><?php echo $user['num']; ?></b></td>
		<td class="b"><b><?php echo $user['posts'] ?></b></td>
	</tr>
	<?php
    $j++;
}
?>
	<tr class="h"><td class="b h" colspan="5">Totals</td></tr>
	<tr class="<?php echo $tr; ?>" align="center">
		<td class="nb"><b><?php echo $j; ?></b></td>
		<td class="nb"></td>
		<td class="nb"></td>
		<td class="b"><b><?php echo $post_total; ?></b></td>
		<td class="b"><b><?php echo $post_overall; ?></b></td>
	</tr>
</table>
<?php

pagefooter();

function timelink($timex){
	global $time;
	return ($time == $timex ? " <span style='font-weight:bold;font-family:Verdana'>".timeunits2($timex)."</span> " : " <a href=activeusers.php?time=$timex>".timeunits2($timex).'</a> ');
}
?>