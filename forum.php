<?php
require('lib/common.php');

$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$fid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$uid = isset($_GET['user']) ? (int)$_GET['user'] : 0;

if (isset($_GET['id']) && $fid = $_GET['id']) {
	if ($log) {
		$forum = $sql->fetchp("SELECT f.*, r.time rtime FROM forums f LEFT JOIN forumsread r ON (r.fid = f.id AND r.uid = ?) "
			. "WHERE f.id = ? AND f.id IN " . forums_with_view_perm(), [$loguser['id'], $fid]);
		if (!$forum['rtime']) $forum['rtime'] = 0;
	} else
		$forum = $sql->fetchp("SELECT * FROM forums WHERE id = ? AND id IN " . forums_with_view_perm(), [$fid]);

	if (!isset($forum['id'])) noticemsg("Error", "Forum does not exist.", true);

	//append the forum's title to the site title
	pageheader($forum['title'], $fid);

	$threads = $sql->prepare("SELECT " . userfields('u1', 'u1') . "," . userfields('u2', 'u2') . ", t.*"
		. ($log ? ", (NOT (r.time<t.lastdate OR isnull(r.time)) OR t.lastdate<'$forum[rtime]') isread" : '') . ' '
		. "FROM threads t "
		. "LEFT JOIN users u1 ON u1.id=t.user "
		. "LEFT JOIN users u2 ON u2.id=t.lastuser "
		. ($log ? "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id])" : '')
		. "WHERE t.forum = ? AND t.announce = 0 "
		. "ORDER BY t.sticky DESC, t.lastdate DESC "
		. "LIMIT " . (($page - 1) * $loguser['tpp']) . "," . $loguser['tpp'],
		[$fid]);

	$topbot = [
		'breadcrumb' => [['href' => './', 'title' => 'Main']],
		'title' => $forum['title']
	];
	if (can_create_forum_thread($forum))
		$topbot['actions'] = [['href' => "newthread.php?id=$fid", 'title' => 'New thread']];
} elseif (isset($_GET['user']) && $uid = $_GET['user']) {
	$user = $sql->fetchp("SELECT * FROM users WHERE id = ?", [$uid]);

	if (!isset($user)) noticemsg("Error", "User does not exist.", true);

	pageheader("Threads by " . ($user['displayname'] ? $user['displayname'] : $user['name']));

	$threads = $sql->prepare("SELECT " . userfields('u1', 'u1') . "," . userfields('u2', 'u2') . ", t.*, f.id fid, f.title ftitle,"
		. ($log ? " (NOT (r.time<t.lastdate OR isnull(r.time)) OR t.lastdate<fr.time) isread " : ' ')
		. "FROM threads t "
		. "LEFT JOIN users u1 ON u1.id=t.user "
		. "LEFT JOIN users u2 ON u2.id=t.lastuser "
		. "LEFT JOIN forums f ON f.id=t.forum "
		. ($log ? "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) "
			. "LEFT JOIN forumsread fr ON (fr.fid=f.id AND fr.uid=$loguser[id]) " : '')
		. "LEFT JOIN categories c ON f.cat=c.id "
		. "WHERE t.user = ? "
		. "AND f.id IN " . forums_with_view_perm() . " "
		. "ORDER BY t.sticky DESC, t.lastdate DESC "
		. "LIMIT " . (($page - 1) * $loguser['tpp']) . "," . $loguser['tpp'],
		[$uid]);

	$forum['threads'] = $sql->resultp("SELECT count(*) FROM threads t "
		. "LEFT JOIN forums f ON f.id = t.forum LEFT JOIN categories c ON f.cat = c.id "
		. "WHERE t.user = ? AND f.id IN " . forums_with_view_perm(), [$uid]);

	$topbot = [
		'breadcrumb' => [['href' => './', 'title' => 'Main'], ['href' => "profile.php?id=$uid", 'title' => ($user['displayname'] ? $user['displayname'] : $user['name'])]],
		'title' => 'Threads'
	];
} elseif ($time = $_GET['time']) {
	if (is_numeric($time))
		$mintime = time() - $time;
	else
		$mintime = 86400;

	pageheader('Latest posts');

	$threads = $sql->query("SELECT " . userfields('u1', 'u1') . "," . userfields('u2', 'u2') . ", t.*, f.id fid,
		f.title ftitle" . ($log ? ', (NOT (r.time<t.lastdate OR isnull(r.time)) OR t.lastdate<fr.time) isread ' : ' ')
		. "FROM threads t "
		. "LEFT JOIN users u1 ON u1.id=t.user "
		. "LEFT JOIN users u2 ON u2.id=t.lastuser "
		. "LEFT JOIN forums f ON f.id=t.forum "
		. "LEFT JOIN categories c ON f.cat=c.id "
		. ($log ? "LEFT JOIN threadsread r ON (r.tid=t.id AND r.uid=$loguser[id]) "
			. "LEFT JOIN forumsread fr ON (fr.fid=f.id AND fr.uid=$loguser[id]) " : '')
		. "WHERE t.lastdate>$mintime "
		. "  AND f.id IN " . forums_with_view_perm() . " "
		. "ORDER BY t.lastdate DESC "
		. "LIMIT " . (($page - 1) * $loguser['tpp']) . "," . $loguser['tpp']);
	$forum['threads'] = $sql->resultq("SELECT count(*) "
		. "FROM threads t "
		. "LEFT JOIN forums f ON f.id=t.forum "
		. "LEFT JOIN categories c ON f.cat=c.id "
		. "WHERE t.lastdate > $mintime "
		. "AND f.id IN " . forums_with_view_perm() . " ");

	$topbot = [];
} else {
	noticemsg("Error", "Forum does not exist.", true);
}

$showforum = (isset($time) ? $time : $uid);

if ($forum['threads'] <= $loguser['tpp']) {
	$fpagelist = (!isset($time) ? '<br>' : '');
	$fpagebr = '';
} else {
	$fpagelist = '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">Pages:';
	for ($p = 1; $p <= 1 + floor(($forum['threads'] - 1) / $loguser['tpp']); $p++)
		if ($p == $page)
			$fpagelist .= " $p";
		elseif ($fid)
			$fpagelist .= " <a href=forum.php?id=$fid&page=$p>$p</a>";
		elseif ($uid)
			$fpagelist .= " <a href=forum.php?user=$uid&page=$p>$p</a>";
		elseif ($time)
			$fpagelist .= " <a href=forum.php?time=$time&page=$p>$p</a>";
	$fpagelist .= '</div>';
	$fpagebr = '<br>';
}

RenderPageBar($topbot);

if (isset($time)) {
	?><table class="c1" style="width:auto">
		<tr class="h"><td class="b">Latest Threads</td></tr>
		<tr><td class="b n1 center">
			By Threads | <a href=thread.php?time=<?=$time ?>>By Posts</a></a><br><br>
			<?=timelink(900,'forum').' | '.timelink(3600,'forum').' | '.timelink(86400,'forum').' | '.timelink(604800,'forum') ?>
		</td></tr>
	</table><?php
}

?><br>
<table class="c1">
	<?=($fid ? announcement_row(3, 4) : '')?>
	<tr class="h">
		<td class="b h" width=17>&nbsp;</td>
		<?=($showforum ? '<td class="b h">Forum</td>' : '') ?>
		<td class="b h">Title</td>
		<td class="b h" width=130>Started by</td>
		<td class="b h" width=50>Replies</td>
		<td class="b h" width=50>Views</td>
		<td class="b h" width=130>Last post</td>
	</tr><?php
$lsticky = 0;

if_empty_query($threads, "No threads found.", ($showforum ? 7 : 6));

for ($i = 1; $thread = $sql->fetch($threads); $i++) {
	$pagelist = '';
	if ($thread['replies'] >= $loguser['ppp']) {
		for ($p = 1; $p <= ($pmax = (1 + floor($thread['replies'] / $loguser['ppp']))); $p++) {
			if ($p < 7 || $p > ($pmax - 7) || !($p % 10))
				$pagelist.=" <a href=thread.php?id=$thread[id]&page=$p>$p</a>";
			else if (substr($pagelist, -1) != ".")
				$pagelist.=" ...";
		}
		$pagelist = " <span class=sfont>(pages: $pagelist)</span>";
	}

	$status = '';
	if ($thread['closed']) $status .= 'o';

	if ($log) {
		if (!$thread['isread']) $status .= 'n';
	} else {
		if ($thread['lastdate'] > (time() - 3600)) $status .= 'n';
	}

	if ($status)
		$status = rendernewstatus($status);
	else
		$status = '';

	if (!$thread['title'])
		$thread['title'] = '�';

	if ($thread['sticky'])
		$tr = 'n1';
	else
		$tr = ($i % 2 ? 'n2' : 'n3');

	if (!$thread['sticky'] && $lsticky)
		echo '<tr class="c"><td class="b" colspan='.($showforum ? 8 : 7).' style="font-size:1px">&nbsp;</td>';
	$lsticky = $thread['sticky'];

	?><tr class="<?=$tr ?> center">
		<td class="b n1"><?=$status ?></td>
		<?=($showforum ? "<td class=\"b\"><a href=forum.php?id=$thread[fid]>$thread[ftitle]</a></td>" : '')?>
		<td class="b left"><a href="thread.php?id=<?=$thread['id'] ?>"><?=forcewrap(htmlval($thread['title'])) ?></a><?=$pagelist ?></td>
		<td class="b"><?=userlink($thread, 'u1') ?></td>
		<td class="b"><?=$thread['replies'] ?></td>
		<td class="b"><?=$thread['views'] ?></td>
		<td class="b">
			<nobr><?=date($dateformat, $thread['lastdate']) ?></nobr><br>
			<span class="sfont">by <?=userlink($thread, 'u2') ?> <a href="thread.php?pid=<?=$thread['lastid'] ?>#<?=$thread['lastid'] ?>">&raquo;</a></span>
		</td>
	</tr><?php
}
echo "</table>$fpagelist$fpagebr";

RenderPageBar($topbot);

pagefooter();
