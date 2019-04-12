<?php
if (isset($_GET['p'])) redirect("thread.php?pid={$_GET['p']}#{$_GET['p']}");
if (isset($_GET['t'])) redirect("thread.php?id={$_GET['t']}");
if (isset($_GET['u'])) redirect("profile.php?id={$_GET['u']}");

$showonusers = 1;
require('lib/common.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';

//mark forum read
if ($log && $action == 'markread') {
	$fid = $_GET['fid'];
	if ($fid != 'all') {
		//delete obsolete threadsread entries
		$sql->prepare("DELETE r FROM threadsread r LEFT JOIN threads t ON t.id = r.tid WHERE t.forum = ? AND r.uid = ?", [$fid, $loguser['id']]);
		//add new forumsread entry
		$sql->prepare("REPLACE INTO forumsread VALUES (?,?,?)", [$loguser['id'], $fid, time()]);
	} else {
		//mark all read
		$sql->prepare("DELETE FROM threadsread WHERE uid=" . $loguser['id']);
		$sql->prepare("REPLACE INTO forumsread (uid,fid,time) SELECT " . $loguser['id'] . ",f.id," . time() . " FROM forums f");
	}
	redirect('index.php');
}

pageheader();

$categs = $sql->query("SELECT * FROM categories ORDER BY ord,id");
while ($c = $sql->fetch($categs)) {
	$categ[$c['id']] = $c;
}

$forums = $sql->prepare("SELECT f.*".($log ? ", r.time rtime" : '').", ".userfields('u', 'u')." "
		. "FROM forums f "
		. "LEFT JOIN users u ON u.id=f.lastuser "
		. "LEFT JOIN categories c ON c.id=f.cat "
		. ($log ? "LEFT JOIN forumsread r ON r.fid = f.id AND r.uid = $loguser[id] " : '')
		. " ORDER BY c.ord,c.id,f.ord,f.id", []);
$cat = -1;

?>
<table class="c1">
	<?=announcement_row(5) ?>
	<tr class="h">
		<td class="b h" width=17>&nbsp;</td>
		<td class="b h">Forum</td>
		<td class="b h" width=50>Threads</td>
		<td class="b h" width=50>Posts</td>
		<td class="b h" width=150>Last post</td>
	</tr>
<?php

while ($forum = $sql->fetch($forums)) {
	if (!can_view_forum($forum))
		continue;

	if ($forum['cat'] != $cat) {
		$cat = $forum['cat'];
		?><tr class="c">
			<td class="b" colspan="5"><?=$categ[$cat]['title'] ?></td>
		</tr><?php
	}

	if ($forum['posts'] > 0 && $forum['lastdate'] > 0)
		$lastpost = '<nobr>' . date($dateformat, $forum['lastdate']) . '</nobr><br><span class=sfont>by&nbsp;' . userlink($forum, 'u') . "&nbsp;<a href='thread.php?pid=" . $forum['lastid'] . "#" . $forum['lastid'] . "'>&raquo;</a></span>";
	else
		$lastpost = 'None';

	if ($forum['lastdate'] > ($log ? $forum['rtime'] : time() - 3600)) {
		$status = rendernewstatus("n");
	} else {
		$status = '';
	}

	?>
	<tr class="center">
		<td class="b n1"><?=$status ?></td>
		<td class="b n2 left">
			<?=($forum['private'] ? '(' : '') ?><a href="forum.php?id=<?=$forum['id'] ?>"><?=$forum['title'] ?></a><?=($forum['private'] ? ')' : '') ?>
			<br><span class=sfont><?=str_replace("%%%SPATULANDOM%%%", $spatulas[$spaturand], $forum['descr']) ?></span>
		</td>
		<td class="b n1"><?=$forum['threads'] ?></td>
		<td class="b n1"><?=$forum['posts'] ?></td>
		<td class="b n2"><?=$lastpost ?></td>
	<?php
}
?></table><?php
pagefooter();