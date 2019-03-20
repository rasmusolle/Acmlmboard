<?php
require("lib/common.php");

pageheader("Search");

$showforum = 1;

if (!isset($_GET['q'])) $_GET['q'] = '';
if (!isset($_GET['w'])) $_GET['w'] = 0;
if (!isset($_GET['f'])) $_GET['f'] = 0;

?>
<table class="c1">
	<tr class="h"><td class="b h">Search</td>
	<tr><td class="b n1">
		<form action="search.php" method="get"><table>
			<tr>
				<td>Search for</td>
				<td><input type="text" name="q" size=40 value="<?=htmlspecialchars(stripslashes($_GET['q']), ENT_QUOTES) ?>"></td>
			</tr><tr>
				<td></td>
				<td>
					in <input type="radio" class="radio" name="w" value="0" id="threadtitle" <?=(($_GET['w'] == 0) ? "checked" : "") ?>><label for="threadtitle">thread title</label>
					<input type="radio" class="radio" name="w" value="1" id="posttext" <?=(($_GET['w'] == 1) ? "checked" : "") ?>><label for="posttext">post text</label>
				</td>
			</tr><tr>
				<td></td>
				<td><input type="submit" class="submit" name="action" value="Search"></td>
			</tr>
		</table></form>
	</td></tr>
</table>
<?php
if (!isset($_GET['action']) || strlen($_GET['q']) < 3) {
	if (isset($_GET['action']) && strlen($_GET['q']) < 3) {
		echo '<br><table class="c1"><tr><td class="b n1 center">Please enter more than 2 characters!</td></tr></table>';
	}
	pagefooter();
	die();
}

?><br>
<div id="pleasewait">
	<table class="c1">
		<tr class="h"><td class="b h">Results</td></tr>
		<tr><td class="b n1 center" style="padding:25">Search in progress...</td></tr>
	</table>
</div>
<div id="youwaited" style="display:none">
	<table class="c1"><tr class="h"><td class="b h">Results</td></tr></table>
<?php
if ($_GET['w'] == 1) {
	$searchquery = $_GET['q'];
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
	$dastring = "";
	$lastbool = 0;
	$defbool = "AND";
	$nextbool = "";
	$searchfield = "pt.text";
	$boldify = [];
	foreach ($wordor as $numbah => $werdz) {
		if ($lastbool == 0) {
			$nextbool = $defbool;
		}
		if ((($werdz == "OR") || ($werdz == "AND")) && !empty($dastring)) {
			$nextbool = $werdz;
			$lastbool = 1;
		} else {
			if (substr($werdz, 0, 1) == "!") {
				$dastring .= $nextbool." ".$searchfield." LIKE '%".$matches[1][substr($werdz, 1)]."%' ";
				$boldify[$numbah] = "@".$matches[1][substr($werdz, 1)]."@i";
			} else {
				$dastring .= $nextbool." ".$searchfield." LIKE '%".$werdz."%' ";
				$boldify[$numbah] = "@".$werdz."@i";
			}
		}
	}
	$dastring = trim(substr($dastring, strlen($defbool)));
	$fieldlist = '';
	$ufields = ['id','name','posts','regdate','lastpost','lastview','location','sex',
					'group_id','rankset','title','usepic','head','sign','displayname','enablecolor','nick_color'];
	foreach ($ufields as $field)
		$fieldlist .= "u.$field u$field,";
	if ($_GET['f'])
		$dastring .= " AND f.id='$_GET[f]' ";
	$posts = $sql->query("SELECT $fieldlist p.*,  pt.text, pt.date ptdate, pt.user ptuser, pt.revision, t.id tid, t.title ttitle, t.forum tforum "
		."FROM posts p "
		."LEFT JOIN poststext pt ON p.id=pt.id "
		."LEFT JOIN poststext pt2 ON pt2.id=pt.id AND pt2.revision=(pt.revision+1) "
		."LEFT JOIN users u ON p.user=u.id "
		."LEFT JOIN threads t ON p.thread=t.id "
		."LEFT JOIN forums f ON f.id=t.forum "
		."LEFT JOIN categories c ON c.id=f.cat "
		."WHERE $dastring AND ISNULL(pt2.id) "
		."AND f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()." "
		."ORDER BY p.id");

	if_empty_query($posts, 'No posts found.', 1, true);

	while ($post = $sql->fetch($posts)) {
		$pthread['id'] = $post['tid'];
		$pthread['title'] = $post['ttitle'];
		$post['text'] = preg_replace($boldify,"<b>\\0</b>",$post['text']);
		echo threadpost($post,$pthread);
	}
} else {
	if (!isset($page)) $page = 1;
	$searchquery = $_GET['q'];
	$searchquery = preg_replace("@[^\" a-zA-Z0-9]@", "", $searchquery);
	preg_match_all("@\"([^\"]+)\"@", $searchquery, $matches);
	foreach ($matches[0] as $key => $value) {
		$searchquery = str_replace($value, " !".$key." ", $searchquery);
	}
	$searchquery = str_replace("\"", "", $searchquery);
	while (strpos($searchquery, "  ") !== FALSE) {
		$searchquery = str_replace("  ", " ", $searchquery);
	}
	$wordor = explode(" ", trim($searchquery));
	$dastring = "";
	$lastbool = 0;
	$defbool = "AND";
	$nextbool = "";
	$searchfield = "t.title";
	$boldify = [];
	foreach ($wordor as $numbah => $werdz) {
		if ($lastbool == 0) {
			$nextbool = $defbool;
		}
		if ((($werdz == "OR") || ($werdz == "AND")) && !empty($dastring)) {
			$nextbool = $werdz;
			$lastbool = 1;
		} else {
			if (substr($werdz, 0, 1) == "!") {
				$dastring .= $nextbool." ".$searchfield." LIKE '%".$matches[1][substr($werdz, 1)]."%' ";
				$boldify[$numbah] = "@".$matches[1][substr($werdz, 1)]."@i";
			} else {
				$dastring .= $nextbool." ".$searchfield." LIKE '%".$werdz."%' ";
				$boldify[$numbah] = "@".$werdz."@i";
			}
		}
	}
	$dastring = trim(substr($dastring, strlen($defbool)));
	$fieldlist = '';
	$ufields = ['id','name','sex','group_id','nick_color','enablecolor','displayname'];
	foreach ($ufields as $field)
		$fieldlist .= "u1.$field u1$field, u2.$field u2$field, ";
	if ($_GET['f'])
		$dastring .= " AND f.id='$_GET[f]' ";
	if ($page < 1) $page = 1;
	$threads = $sql->query("SELECT $fieldlist t.*, f.id fid, f.title ftitle "
		."FROM threads t "
		."LEFT JOIN users u1 ON u1.id=t.user "
		."LEFT JOIN users u2 ON u2.id=t.lastuser "
		."LEFT JOIN forums f ON f.id=t.forum "
		."LEFT JOIN categories c ON f.cat=c.id "
		."WHERE $dastring "
		."AND f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()." "
		."ORDER BY t.sticky DESC, t.lastdate DESC "
		."LIMIT ".(($page-1)*$loguser['tpp']).",".$loguser['tpp']);
	$forum['threads'] = $sql->resultq("SELECT count(*) "
		."FROM threads t "
		."LEFT JOIN users u1 ON u1.id=t.user "
		."LEFT JOIN forums f ON f.id=t.forum "
		."LEFT JOIN categories c ON f.cat=c.id "
		."WHERE $dastring "
		."AND f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm());
	?><table class="c1">
		<tr class="h">
			<td class="b h" width="17">&nbsp;</td>
			<td class="b h">Title</td>
			<td class="b h" width="130">Started by</td>
			<td class="b h" width="50">Replies</td>
			<td class="b h" width="50">Views</td>
			<td class="b h" width="130">Last post</td>
		</tr>
	<?php
	$lsticky = 0;
	if_empty_query($threads, "No threads found.", 6);
	for ($i = 1; $thread = $sql->fetch($threads); $i++) {
		$pagelist = '';
		if ($thread['replies'] >= $loguser['ppp']){
			for ($p = 1; $p <= 1+floor($thread['replies'] / $loguser['ppp']); $p++)
				$pagelist .= " <a href=thread.php?id=$thread[id]&page=$p>$p</a>";
			$pagelist = " <span class=sfont>(pages: $pagelist)</span>";
		}
		$status = '';
		if ($thread['closed']) $status .= 'off';

		if ($log) if (isset($thread['isread']) && !$thread['isread']) $status .= 'new';
		else if ($thread['lastdate'] > (time() - 3600)) $status .= 'new';

		if ($status) $status = '<img src="img/status/$status.png">';
		else $status = '&nbsp;';

		if (!$thread['title']) $thread['title'] = '�';

		if ($thread['sticky']) $tr = 'n1';
		else $tr = ($i % 2 ? 'n2' :'n3');

		if (!$thread['sticky'] && $lsticky)
			echo '<tr class="c">
			<td class="b" colspan="8" style="font-size:1px">&nbsp;</td>';
		$lsticky = $thread['sticky'];
		?><tr class="<?=$tr ?> center">
			<td class="b n1"><?=$status ?></td>
			<td class="b left"><a href=thread.php?id=<?=$thread['id'] ?>><?=forcewrap(htmlval($thread['title'])) ?></a><?=$pagelist ?></td>
			<td class="b"><?=userlink($thread,'u1') ?></td>
			<td class="b"><?=$thread['replies'] ?></td>
			<td class="b"><?=$thread['views'] ?></td>
			<td class="b">
				<nobr><?=date($dateformat,$thread['lastdate']) ?></nobr><br>
				<span class="sfont">by <?=userlink($thread,'u2') ?></span>
			</td>
		<?php
	}
	if ($forum['threads'] <= $loguser['tpp']) $fpagelist = '<br>';
	else {
		$fpagelist = 'Pages:';
		for ($p = 1; $p <= (1 + floor(($forum['threads'] - 1) / $loguser['tpp'])); $p++)
			if ($p == $page) $fpagelist .= " $p";
			else $fpagelist .= ' <a href=search.php?q="' . urlencode($_GET['q']) . '"&action=Search&w=0&f=0&t=&p=&page=' . $p . '>' . $p . '</a>';
	}
	?></table><?php echo $fpagelist;
}
?></div><script>document.getElementById('pleasewait').style.display='none';
document.getElementById('youwaited').style.display='block';</script><?php
pagefooter();