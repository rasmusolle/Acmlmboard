<?php

if (isset($_GET['p'])) header("Location: thread.php?pid={$_GET['p']}#{$_GET['p']}");
if (isset($_GET['t'])) header("Location: thread.php?id={$_GET['t']}");
if (isset($_GET['u'])) header("Location: profile.php?id={$_GET['u']}");
if (isset($_GET['a'])) header("Location: thread.php?announce={$_GET['a']}");

$showonusers = 1;
require('lib/common.php');

$action = isset($_GET['action']) ? $_GET['action'] : '';

//mark forum read
if ($log && $action == 'markread') {
	$fid = $_GET['fid'];
	if ($fid != 'all') {
		checknumeric($fid);
		//delete obsolete threadsread entries
		$sql->query("DELETE r FROM threadsread r LEFT JOIN threads t ON t.id=r.tid WHERE t.forum=$fid AND r.uid=" . $loguser['id']);
		//add new forumsread entry
		$sql->query("REPLACE INTO forumsread VALUES ($loguser[id],$fid," . time() . ')');
	} else {
		//mark all read
		$sql->query("DELETE FROM threadsread WHERE uid=" . $loguser['id']);
		$sql->query("REPLACE INTO forumsread (uid,fid,time) SELECT " . $loguser['id'] . ",f.id," . time() . " FROM forums f");
	}
	header('Location: index.php');
}

pageheader();

$categs = $sql->query("SELECT * FROM categories ORDER BY ord,id");
while ($c = $sql->fetch($categs)) {
	if (can_view_cat($c))
		$categ[$c['id']] = $c;
}

$forums = $sql->query("SELECT f.*" . ($log ? ", r.time rtime" : '') . ", c.private cprivate, " . userfields('u', 'u') . " "
		. "FROM forums f "
		. "LEFT JOIN users u ON u.id=f.lastuser "
		. "LEFT JOIN categories c ON c.id=f.cat "
		. ($log ? "LEFT JOIN forumsread r ON r.fid=f.id AND r.uid=$loguser[id] " : '')
		. " ORDER BY c.ord,c.id,f.ord,f.id");
$cat = -1;

?>
<table class="c1">
	<?php echo announcement_row(2, 3); ?>
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
			<td class="b" colspan="5"><?php echo ($categ[$cat]['private'] ? ('(' . ($categ[$cat]['title']) . ')') : ($categ[$cat]['title'])); ?></td>
		</tr><?php
	}

	if ($forum['posts'] > 0 && $forum['lastdate'] > 0)
		$lastpost = '<nobr>' . cdate($dateformat, $forum['lastdate']) . '</nobr><br><span class=sfont>by&nbsp;' . userlink($forum, 'u') . "&nbsp;<a href='thread.php?pid=" . $forum['lastid'] . "#" . $forum['lastid'] . "'>&raquo;</a></span>";
	else
		$lastpost = 'None';

	if ($forum['lastdate'] > ($log ? $forum['rtime'] : time() - 3600)) {
		if ($log) {
			$thucount = $sql->resultq("SELECT count(*) FROM threads t"
					. " LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id])"
					. " LEFT JOIN forumsread f ON (f.fid=t.forum AND f.uid=$loguser[id])"
					. " WHERE t.forum=$forum[id]"
					. " AND ((r.time < t.lastdate OR isnull(r.time)) AND (f.time < t.lastdate OR isnull(f.time)))"
					. " AND (r.uid=$loguser[id] OR isnull(r.uid))");
			$status = rendernewstatus("n", $thucount);
		} else {
			$status = '&nbsp;';
		}
	} else {
		$status = '&nbsp;';
	}

	?>
	<tr class="center">
		<td class="b n1"><?php echo $status; ?></td>
		<td class="b n2 left">
			<?php echo ($forum['private'] ? '(' : ''); ?><a href="forum.php?id=<?php echo $forum['id']; ?>"><?php echo $forum['title']; ?></a><?php echo ($forum['private'] ? ')' : ''); ?>
			<br><span class=sfont><?php echo str_replace("%%%SPATULANDOM%%%", $spatulas[$spaturand], $forum['descr']); ?></span>
		</td>
		<td class="b n1"><?php echo $forum['threads']; ?></td>
		<td class="b n1"><?php echo $forum['posts']; ?></td>
		<td class="b n2"><?php echo $lastpost; ?></td>
	<?php
}
?></table><?php
pagefooter();
?>