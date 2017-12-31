<?php
require ('lib/common.php');

function sslicon($a, $uid = 0) {
	if (has_perm('view-post-ips') && $a) {
		return "<img src='img/ssloff.gif'>";
	}
	return "";
}

pageheader('Online users');

$time = (isset($_GET['time']) ? $_GET['time'] : null);
checknumeric($time);

if (!$time)
	$time = 300;

$hiddencheck = "AND hidden=0 ";
if (has_perm('view-hidden-users')) {
	$hiddencheck = "";
}
$users = $sql->query("SELECT * FROM users "
					."WHERE lastview>".(ctime()-$time)." $hiddencheck"
					."ORDER BY lastview DESC");
$guests = $sql->query("SELECT g.* FROM guests g "
					."WHERE g.date>".(ctime()-$time)." "
					."AND g.bot=0 "
					."ORDER BY g.date DESC");
$bots = $sql->query("SELECT * FROM guests "
					."WHERE date>".(ctime()-$time)." "
					."AND bot=1 "
					."ORDER BY date DESC");
?>
Online users during the last <?php echo str_replace('.', '', timeunits2($time)); ?>:
<div style="margin-top: 3px; margin-bottom: 3px; display:block">
	<?php echo timelink(60).'|'.timelink(300).'|'.timelink(900).'|'.timelink(3600).'|'.timelink(86400); ?>
</div>
<table class="c1">
	<tr class="h">
		<td class="b h" width="30">#</td>
		<td class="b h">Name</td>
		<td class="b h" width="90">Last view</td>
		<td class="b h" width="140">Last post</td>
		<?php echo (has_perm('view-user-urls') ? '<td class="b h">URL</td>' : ''); ?>
		<?php echo (has_perm('view-post-ips') ? '<td class="b h" width="120">IP</td>' : ''); ?>
		<td class="b h" width="50">Posts</td>
	</tr>
<?php
for ($i = 1; $user = $sql->fetch($users); $i++) {
	if ($user['url'] == '!') {
		$user['url'] = substr($user['url'], 1);
		$user['ssl'] = 1;
	} else {
		$user['ssl'] = 0;
	}
	$tr = ($i % 2 ? 'n2' : 'n3');
	?>
	<tr class="<?php echo $tr; ?>" align="center">
		<td class="b n1"><?php echo $i; ?>.</td>
		<td class="b" align="left"><?php echo ($user['hidden'] ? '(' . userlink($user) . ')' : userlink($user)); ?></td>
		<td class="b"><?php echo cdate($loguser['timeformat'], $user['lastview']); ?></td>
		<td class="b"><?php echo ($user['lastpost'] ? cdate($dateformat, $user['lastpost']) : '-'); ?></td>
		<?php echo (has_perm('view-user-urls') ? '<td class="b" align="left">'
			. ($user['url'] ? "<a href=$user[url]>" . str_replace(array('%20','_'), ' ', $user['url']) . '</a>' : '-')
			. ($user['ipbanned'] ? ' (IP banned)' : '') . "</td>" : '')
		?>
		<?php ?>
		<td class="b"><?php echo $user['posts']; ?></td>
	</tr>
<?php } ?>
</table><br>
Guests:
<table class="c1">
	<tr class="h">
		<td class="b h" width="30">#</td>
		<td class="b h" width="70" style="min-width: 150px;">User agent (Browser)</td>
		<td class="b h" width="70">Last view</td>
		<td class="b h">URL</td>
		<?php echo (has_perm("view-post-ips") ? '<td class="b h" width="120">IP</td>' : ''); ?>
	</tr>
<?php
for ($i = 1; $guest = $sql->fetch($guests); $i++) {
	if ($guest['url'][0] == '!') {
		$guest['url'] = substr($guest[url], 1);
		$guest['ssl'] = 1;
	} else {
		$guest['ssl'] = 0;
	}
	$tr = ($i % 2 ? 'n2' : 'n3');
	?>
	<tr class="<?php echo $tr; ?>" align="center">
		<td class="b n1"><?php echo $i; ?>.</td>
		<td class="b" align="left"><span title="<?php echo htmlspecialchars($guest['useragent']); ?>" style=white-space:nowrap><?php echo htmlspecialchars(substr($guest['useragent'], 0, 65)); ?></span></td>
		<td class="b"><?php echo cdate($loguser['timeformat'], $guest['date']); ?></td>
		<td class="b" align="left">
			<a href="<?php echo $guest['url']; ?>"><?php echo str_replace(array("%20","_"), " ", $guest['url']); ?></a>
			<?php echo ($guest['ipbanned'] ? " (IP banned)" : ""); ?>
		</td> 
		<?php echo (has_perm("view-post-ips") ? '<td class="b">' . flagip($guest['ip']) . '</td>' : ''); ?>
	</tr>
	<?php
}
?>
</table><br>
Bots:
<table class="c1">
	<tr class="h">
		<td class="b h" width=30>#</td>
		<td class="b h" width=70>Bot</td>
		<td class="b h" width=70>Last view</td>
		<td class="b h">URL</td>
		<?php echo (has_perm("view-post-ips") ? '<td class="b h" width=120>IP</td>' : ''); ?>
	</tr>
<?php
for ($i = 1; $guest = $sql->fetch($bots); $i++) {
	if ($guest['url'][0] == '!') {
		$guest['url'] = substr($guest['url'], 1);
		$guest['ssl'] = 1;
	} else {
		$guest['ssl'] = 0;
	}
	$tr = ($i % 2 ? 'n2' : 'n3');
	?>
	<tr class="<?php echo $tr; ?>" align="center">
		<td class="b n1"><?php echo $i; ?>.</td>
		<td class="b" align="left">
			<span title="<?php echo htmlspecialchars($guest['useragent']); ?>" style=white-space:nowrap>
				<?php echo htmlspecialchars(substr($guest['useragent'], 0, 50)); ?>
			</span>
		</td>
		<td class="b"><?php echo cdate($loguser['timeformat'], $guest['date']); ?></td>
		<td class="b" align="left">
			<span style='float:right'><?php echo sslicon($guest['ssl']); ?></span>
			<a href=<?php echo $guest['url']; ?>><?php echo $guest['url']; ?></a>
			<?php echo ($guest['ipbanned'] ? " (IP banned)" : ""); ?>
		</td>
		<?php echo (has_perm("view-post-ips") ? '<td class="b">' . flagip($guest['ip']) . '</td>' : ''); ?>
	</tr>
<?php } ?>
</table><?php

pagefooter();

function timelink($timex) {
	global $time;
	return ($time == $timex ? " " . timeunits2($timex) . " " : " <a href=online.php?time=$timex>" . timeunits2($timex) . '</a> ');
}
?>
