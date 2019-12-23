<?php
require("lib/common.php");

pageheader("Search");

$query = (isset($_GET['q']) ? $_GET['q'] : '');
$where = (isset($_GET['w']) ? $_GET['w'] : 0);
$forum = (isset($_GET['f']) ? $_GET['f'] : 0);

?>
<table class="c1">
	<tr class="h"><td class="b h">Search</td>
	<tr><td class="b n1">
		<form action="search.php" method="get"><table>
			<tr>
				<td>Search for</td>
				<td><input type="text" name="q" size=40 value="<?=htmlspecialchars($query, ENT_QUOTES) ?>"></td>
			</tr><tr>
				<td></td>
				<td>
					in <input type="radio" class="radio" name="w" value="0" id="threadtitle" <?=(($where == 0) ? "checked" : "") ?>><label for="threadtitle">thread title</label>
					<input type="radio" class="radio" name="w" value="1" id="posttext" <?=(($where == 1) ? "checked" : "") ?>><label for="posttext">post text</label>
				</td>
			</tr><tr>
				<td></td>
				<td><input type="submit" class="submit" name="action" value="Search"></td>
			</tr>
		</table></form>
	</td></tr>
</table>
<?php
if (!isset($_GET['action']) || strlen($query) < 3) {
	if (isset($_GET['action']) && strlen($query) < 3) {
		echo '<br><table class="c1"><tr><td class="b n1 center">Please enter more than 2 characters!</td></tr></table>';
	}
	pagefooter();
	die();
}

?><br>
<table class="c1"><tr class="h"><td class="b h" style="border-bottom:0">Results</td></tr></table>
<?php
$searchquery = $query;
$searchquery = preg_replace("@[^\" a-zA-Z0-9]@", "", $searchquery);
preg_match_all("@\"([^\"]+)\"@", $searchquery, $matches);
foreach ($matches[0] as $key => $value) {
	$searchquery = str_replace($value, " !".$key." ", $searchquery);
}
$searchquery = str_replace("\"", "", $searchquery);
while (strpos($searchquery, "  ") != false) {
	$searchquery = str_replace("  ", " ", $searchquery);
}
$wordor = explode(" ", trim($searchquery));
$string = "";
$lastbool = 0;
$defbool = "AND";
$nextbool = "";
if ($where == 1) {
	$searchfield = "pt.text";
} else {
	$searchfield = "t.title";
}
$boldify = [];
foreach ($wordor as $num => $word) {
	if ($lastbool == 0) {
		$nextbool = $defbool;
	}
	if ((($word == "OR") || ($word == "AND")) && !empty($string)) {
		$nextbool = $word;
		$lastbool = 1;
	} else {
		if (substr($word, 0, 1) == "!") {
			$string .= $nextbool." ".$searchfield." LIKE '%".$matches[1][substr($word, 1)]."%' ";
			$boldify[$num] = "@".$matches[1][substr($word, 1)]."@i";
		} else {
			$string .= $nextbool." ".$searchfield." LIKE '%".$word."%' ";
			$boldify[$num] = "@".$word."@i";
		}
	}
}
$string = trim(substr($string, strlen($defbool)));
if ($forum)
	$string .= " AND f.id='$forum' ";

if ($where == 1) {
	$fieldlist = userfields_post();
	$posts = $sql->query("SELECT ".userfields('u','u').", $fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, t.title ttitle, t.forum tforum "
		."FROM posts p "
		."LEFT JOIN poststext pt ON p.id=pt.id "
		."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) "
		."LEFT JOIN users u ON p.user=u.id "
		."LEFT JOIN threads t ON p.thread=t.id "
		."LEFT JOIN forums f ON f.id=t.forum "
		."WHERE $string AND ISNULL(pt2.id) "
		."AND f.id IN ".forums_with_view_perm()
		."ORDER BY p.id");

	if_empty_query($posts, 'No posts found.', 1, true);

	while ($post = $sql->fetch($posts)) {
		$pthread['id'] = $post['tid'];
		$pthread['title'] = $post['ttitle'];
		$post['text'] = preg_replace($boldify,"<b>\\0</b>",$post['text']);
		echo '<br>' . threadpost($post,$pthread);
	}
} else {
	$page = (isset($_GET['page']) ? $_GET['page'] : 1);
	if ($page < 1) $page = 1;
	$threads = $sql->query("SELECT ".userfields('u', 'u').", t.* "
		."FROM threads t "
		."LEFT JOIN users u ON u.id=t.user "
		."LEFT JOIN forums f ON f.id=t.forum "
		."WHERE $string AND f.id IN ".forums_with_view_perm()
		."ORDER BY t.lastdate DESC "
		."LIMIT ".(($page-1)*$loguser['tpp']).",".$loguser['tpp']);
	$forum['threads'] = $sql->resultq("SELECT COUNT(*) "
		."FROM threads t "
		."LEFT JOIN users u ON u.id=t.user "
		."LEFT JOIN forums f ON f.id=t.forum "
		."WHERE $string AND f.id IN ".forums_with_view_perm());
	?><table class="c1">
		<tr class="c">
			<td class="b h">Title</td>
			<td class="b h" style="min-width:80px">Started by</td>
			<td class="b h" width=200>Date</td>
		</tr><?php
	if_empty_query($threads, "No threads found.", 6);
	for ($i = 1; $thread = $sql->fetch($threads); $i++) {
		if (!$thread['title']) $thread['title'] = '';

		$tr = ($i % 2 ? 'n2' :'n3');

		?><tr class="<?=$tr ?> center">
			<td class="b left" style="word-break:break-word">
				<a href=thread.php?id=<?=$thread['id'] ?>><?=htmlval($thread['title']) ?></a> <?=($thread['sticky'] ? ' (Sticky)' : '')?>
			</td>
			<td class="b"><?=userlink($thread,'u') ?></td>
			<td class="b"><?=date($dateformat,$thread['lastdate']) ?></td>
		</tr><?php
	}
	if ($forum['threads'] <= $loguser['tpp']) $fpagelist = '<br>';
	else {
		$fpagelist = 'Pages:';
		for ($p = 1; $p <= (1 + floor(($forum['threads'] - 1) / $loguser['tpp'])); $p++)
			if ($p == $page) $fpagelist .= " $p";
			else $fpagelist .= ' <a href=search.php?q=' . urlencode($query) . '&action=Search&w=0&f=0&t=&p=&page=' . $p . '>' . $p . '</a>';
	}
	?></table><?php echo $fpagelist;
}

pagefooter();