<?php
require("lib/common.php");
require("lib/threadpost.php");
loadsmilies();

pageheader("Search");

$showforum = 1;

$HARBL = "<table class=harbl";
?>
<style>
.harbl {width:100%;border-collapse:collapse;padding:0}
.activebtn {border:1px solid black}
.notactivebtn {border:1px solid black}
.bblone {border-bottom:1px solid black}form{margin:0}optgroup{font-style:normal}
</style>
<script>
var lit = 'search';
function field(show) {
	document.getElementById(lit+'btn').className='n2 notactivebtn';
	document.getElementById(lit+'div').style.display='none';
	document.getElementById(show+'btn').className='activebtn';
	document.getElementById(show+'div').style.display='block';
	lit=show;
}
</script>
<?php

$categs = $sql->query("SELECT * "
                   ."FROM categories "
                   ."WHERE id IN ".cats_with_view_perm()." "
                   ."ORDER BY ord");
while ($c = $sql->fetch($categs))
	$categ[$c['id']] = $c;
$forums = $sql->query("SELECT f.* "
                   ."FROM forums f "
                   ."LEFT JOIN categories c ON c.id=f.cat "
                   ."WHERE f.id IN ".forums_with_view_perm()." AND c.id IN ".cats_with_view_perm()." "
                   ."ORDER BY c.ord,ord");

$cat = -1;
$fsel = "<select name=f><option value=0>Any</option>";
while ($forum = $sql->fetch($forums)) {
  if ($forum['cat']!=$cat) {
    $cat = $forum['cat'];
    $fsel .= "<optgroup label='".($categ[$cat]['title'])."'>";
  }
  $sel = "";
  if (isset($_GET['f']) && $_GET['f'] == $forum['id']) $sel = " selected";
  $fsel .= "<option value=".$forum['id']."$sel>".$forum['title']."</option>";
}
$fsel .= "</select>";

if (!isset($_GET['q'])) $_GET['q'] = '';
if (!isset($_GET['w'])) $_GET['w'] = 0;
if (!isset($_GET['t'])) $_GET['t'] = '';
if (!isset($_GET['p'])) $_GET['p'] = '';


?>
<table class="c1">
	<tr class="h"><td class="b h">Search</td>
	<tr>
		<td class="b n1" style="padding:10" height="130" valign="top">
			<form action="search.php" method="get">
				<table style="cursor:default;">
					<tr>
						<td width="60" class="lame" style="border:1px solid black" align="center" id="searchbtn" onclick="field('search')"><b>Search</b></td>
						<td width="60" class="n2 superlame" align="center" id="filterbtn" onclick="field('filter')"><b>Filters</b></td>
					</tr>
				</table>
				<table>
					<tr>
						<td style="padding:3;border:1px solid black;">
							<div id="searchdiv">
								<table class="harbl">
									<tr>
										<td>Search for:</td>
										<td><input type="text" name="q" size=40 value="<?php echo htmlspecialchars(stripslashes($_GET['q']), ENT_QUOTES); ?>"></td>
										<td><input type="submit" class="submit" name="action" value="Search"></td>
									</tr>
									<tr>
										<td></td>
										<td>
											in:<input type="radio" class="radio" name="w" value="0" id="threadtitle" <?php echo (($_GET['w'] == 0) ? "checked" : ""); ?>><label for="threadtitle">thread title</label>
											<input type="radio" class="radio" name="w" value="1" id"=posttext" <?php echo (($_GET['w'] == 1) ? "checked" : ""); ?>><label for="posttext">post text</label>
										</td>
									</tr>
								</table>
							</div>
							<div id="filterdiv" style="display:none">
								<table class="harbl">
									<tr>
										<td>Forum:</td>
										<td><?php echo $fsel; ?></td>
									</tr>
									<tr>
										<td>Thread creator:</td>
										<td><input type="text" name="t" value="<?php echo htmlspecialchars(stripslashes($_GET['t']), ENT_QUOTES); ?>"></td>
									</tr>
									<tr>
										<td>Post creator:</td>
										<td><input type="text" name="p" value="<?php echo htmlspecialchars(stripslashes($_GET['p']), ENT_QUOTES); ?>"></td>
									<tr>
										<td><font class="sfont">% = Wildcard</font></td>
										<td><input type="submit" class="submit" name="action" value="Search"></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?php

if (isset($_GET['action']) && $_GET['action'] == "Search") {
	if (strlen($_GET['q']) > 3) {
		?><br>
		<div id="pleasewait">
			<table class="c1">
				<tr class="h"><td class="b h">Results</td></tr>
				<tr><td class="b n1" style="padding:25" align="center">Search in progress...</td></tr>
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
			$boldify = array();
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
			$ufields = array('id','name','posts','regdate','lastpost','lastview','location',
							 'sex','group_id','rankset','title','usepic','head','sign');
			foreach ($ufields as $field)
				$fieldlist .= "u.$field u$field,";
			if (strlen($_GET['p']))
				$dastring .= " AND u.name LIKE '$_GET[p]' ";
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

			while ($post = $sql->fetch($posts)) {
				$pthread['id'] = $post['tid'];
				$pthread['title'] = $post['ttitle'];
				$post['text'] = preg_replace($boldify,"<b>\\0</b>",$post['text']);
				echo "<br>".threadpost($post,0,$pthread);
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
			$boldify = array();
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
			$ufields = array('id','name','sex','group_id');
			foreach ($ufields as $field)
				$fieldlist .= "u1.$field u1$field, u2.$field u2$field, ";
			if (strlen($_GET['t']))
				$dastring .= " AND u1.name LIKE '$_GET[t]' ";
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
			?>
			<br>
			<table class="c1">
				<tr class="h">
					<td class="b h" width="17">&nbsp;</td>
					<?php echo ($showforum ? '<td class="b h">Forum</td>' : ''); ?>
					<td class="b h">Title</td>
					<td class="b h" width="130">Started by</td>
					<td class="b h" width="50">Replies</td>
					<td class="b h" width="50">Views</td>
					<td class="b h" width="130">Last post</td>
				</tr>
			<?php
			$lsticky = 0;
			for ($i = 1; $thread = $sql->fetch($threads); $i++) {
				$pagelist = '';
				if ($thread['replies'] >= $loguser['ppp']){
					for ($p = 1; $p <= 1+floor($thread['replies'] / $loguser['ppp']); $p++)
						$pagelist .= " <a href=thread.php?id=$thread[id]&page=$p>$p</a>";
					$pagelist = " <font class=sfont>(pages: $pagelist)</font>";
				}
				$status = '';
				if ($thread['closed']) $status .= 'off';

				if ($log) if (isset($thread['isread']) && !$thread['isread']) $status .= 'new';
				else if ($thread['lastdate'] > (ctime() - 3600)) $status .= 'new';

				if ($status) $status = '<img src="img/status/$status.png">';
				else $status = '&nbsp;';

				if (!$thread['title']) $thread['title'] = '�';

				if ($thread['sticky']) $tr = 'n1';
				else $tr = ($i % 2 ? 'n2' :'n3');

				if (!$thread['sticky'] && $lsticky)
					echo '<tr class="c">
					<td class="b" colspan="' . ($showforum ? 8 : 7) . '" style="font-size:1px">&nbsp;</td>';
				$lsticky = $thread['sticky'];
				?>
				<tr class="<?php echo $tr; ?>" align="center">
					<td class="b n1"><?php echo $status; ?></td>
					<?php echo ($showforum ? '<td class="b"><a href="forum.php?id="' . $thread['fid'] . '">' . $thread['ftitle'] . '</a></td>' : ''); ?>
					<td class="b" align="left"><?php echo (isset($thread['ispoll']) ? '<img src=img/poll.png height=10>' : ""); ?><a href=thread.php?id=<?php echo $thread['id']; ?>><?php echo forcewrap(htmlval($thread['title'])); ?></a><?php echo $pagelist; ?></td>
					<td class="b"><?php echo userlink($thread,'u1'); ?></td>
					<td class="b"><?php echo $thread['replies']; ?></td>
					<td class="b"><?php echo $thread['views']; ?></td>
					<td class="b">
						<nobr><?php echo cdate($dateformat,$thread['lastdate']); ?></nobr><br>
						<font class="sfont">by <?php echo userlink($thread,'u2'); ?></font>
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
		?></div>
		<script>
		document.getElementById('pleasewait').style.display='none';
		document.getElementById('youwaited').style.display='block';
		</script><?php
	}
}
pagefooter();

?>