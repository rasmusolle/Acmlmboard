<?php
require('lib/common.php');

if (!has_perm('edit-forums')) noticemsg("Error", "You have no permissions to do this!", true);

$error = '';

if (isset($_POST['savecat'])) {
	// save new/existing category
	$cid = $_GET['cid'];
	$title = $_POST['title'];
	$ord = (int)$_POST['ord'];
	if (!trim($title))
		$error = 'Please enter a title for the category.';
	else {
		if ($cid == 'new') {
			$cid = $sql->resultq("SELECT MAX(id) FROM categories");
			if (!$cid) $cid = 0;
			$cid++;
			$sql->prepare("INSERT INTO categories (id,title,ord) VALUES (?,?,?)", [$cid, $title, $ord]);
		} else {
			$cid = (int)$cid;
			if (!$sql->resultp("SELECT COUNT(*) FROM categories WHERE id=?",[$cid])) redirect('manageforums.php');
			$sql->prepare("UPDATE categories SET title = ?, ord = ? WHERE id = ?", [$title, $ord, $cid]);
		}
		redirect('manageforums.php?cid='.$cid);
	}
} else if (isset($_POST['delcat'])) {
	// delete category
	$cid = (int)$_GET['cid'];
	$sql->prepare("DELETE FROM categories WHERE id = ?",[$cid]);

	redirect('manageforums.php');
} else if (isset($_POST['saveforum'])) {
	// save new/existing forum
	$fid = $_GET['fid'];
	$cat = (int)$_POST['cat'];
	$title = $_POST['title'];
	$descr = $_POST['descr'];
	$ord = (int)$_POST['ord'];
	$private = isset($_POST['private']) ? 1 : 0;
	$readonly = isset($_POST['readonly']) ? 1 : 0;

	if (!trim($title))
		$error = 'Please enter a title for the forum.';
	else {
		if ($fid == 'new') {
			$fid = $sql->resultq("SELECT MAX(id) FROM forums");
			if (!$fid) $fid = 0;
			$fid++;
			$sql->prepare("INSERT INTO forums (id,cat,title,descr,ord,private,readonly) VALUES (?,?,?,?,?,?,?)",
				[$fid, $cat, $title, $descr, $ord, $private, $readonly]);
		} else {
			$fid = (int)$fid;
			if (!$sql->resultp("SELECT COUNT(*) FROM forums WHERE id=?",[$fid]))
				redirect('manageforums.php');
			$sql->prepare("UPDATE forums SET cat=?, title=?, descr=?, ord=?, private=?, readonly=? WHERE id=?",
				[$cat, $title, $descr, $ord, $private, $readonly, $fid]);
		}
		saveperms('forums', $fid);
		redirect('manageforums.php?fid='.$fid);
	}
} else if (isset($_POST['delforum'])) {
	// delete forum
	$fid = (int)$_GET['fid'];
	$sql->prepare("DELETE FROM forums WHERE id=?",[$fid]);
	deleteperms('forums', $fid);
	redirect('manageforums.php');
}

pageheader('Forum management');

?>
<script>function toggleAll(cls, enable) {
	var elems = document.getElementsByClassName(cls);
	for (var i = 0; i < elems.length; i++) elems[i].disabled = !enable;
}</script>
<style type="text/css">label { white-space: nowrap; } input:disabled { opacity: 0.5; }</style>
<?php

if ($error) noticemsg("Error", $error);

if (isset($_GET['cid']) && $cid = $_GET['cid']) {
	// category editor
	if ($cid == 'new') {
		$cat = ['id' => 0, 'title' => '', 'ord' => 0];
	} else {
		$cid = (int)$cid;
		$cat = $sql->fetchp("SELECT * FROM categories WHERE id=?",[$cid]);
	}
	?><form action="" method="POST">
		<table class="c1">
			<tr class="h"><td class="b h" colspan="2"><?=($cid == 'new' ? 'Create' : 'Edit') ?> category</td></tr>
			<tr>
				<td class="b n1 center">Title:</td>
				<td class="b n2"><input type="text" name="title" value="<?=htmlspecialchars($cat['title']) ?>" size="50" maxlength="500"></td>
			</tr><tr>
				<td class="b n1 center">Display order:</td>
				<td class="b n2"><input type="text" name="ord" value="<?=$cat['ord'] ?>" size="4" maxlength="10"></td>
			</tr>
			<tr class="h"><td class="b h" colspan="2">&nbsp;</td></tr>
			<tr>
				<td class="b n1 center"></td>
				<td class="b n2">
					<input type="submit" class="submit" name="savecat" value="Save category">
						<?=($cid == 'new' ? '' : '<input type="submit" class="submit" name="delcat" value="Delete category" onclick="if (!confirm("Really delete this category?")) return false;"> ') ?>
					<button type="button" class="submit" id="back" onclick="window.location='manageforums.php';">Back</button>
				</td>
			</tr>
		</table>
	</form><?php
} else if (isset($_GET['fid']) && $fid = $_GET['fid']) {
	// forum editor
	if ($fid == 'new') {
		$forum = ['id' => 0, 'cat' => 1, 'title' => '', 'descr' => '', 'ord' => 0, 'private' => 0, 'readonly' => 0];
	} else {
		$fid = (int)$fid;
		$forum = $sql->fetchp("SELECT * FROM forums WHERE id=?",[$fid]);
	}
	$qcats = $sql->query("SELECT id,title FROM categories ORDER BY ord, id");
	$cats = [];
	while ($cat = $sql->fetch($qcats))
		$cats[$cat['id']] = $cat['title'];
	$catlist = fieldselect('cat', $forum['cat'], $cats);

	?><form action="" method="POST">
		<table class="c1">
			<tr class="h"><td class="b h" colspan="2"><?=($fid == 'new' ? 'Create' : 'Edit') ?> forum</td></tr>
			<tr>
				<td class="b n1 center">Title:</td>
				<td class="b n2"><input type="text" name="title" value="<?=htmlspecialchars($forum['title']) ?>" size="50" maxlength="500"></td>
			</tr><tr>
				<td class="b n1 center">Description:<br><small>HTML allowed.</small></td>
				<td class="b n2"><textarea wrap="virtual" name="descr" rows="3" cols="50"><?=htmlspecialchars($forum['descr']) ?></textarea></td>
			</tr><tr>
				<td class="b n1 center">Category:</td>
				<td class="b n2"><?=$catlist ?></td>
			</tr><tr>
				<td class="b n1 center">Display order:</td>
				<td class="b n2"><input type="text" name="ord" value="<?=$forum['ord'] ?>" size="4" maxlength="10"></td>
			</tr><tr>
				<td class="b n1 center"></td>
				<td class="b n2">
					<label><input type="checkbox" name="private" value="1" <?=($forum['private'] ? ' checked="checked"':'') ?>> Private forum</label>
					<label><input type="checkbox" name="readonly" value="1"<?=($forum['readonly'] ? ' checked="checked"' : '')?>> Read-only</label>
				</td>
			</tr>
			<tr class="h"><td class="b h" colspan="2">&nbsp;</td></tr>
			<tr>
				<td class="b n1 center"></td>
				<td class="b n2">
					<input type="submit" class="submit" name="saveforum" value="Save forum">
					<?php ($fid == 'new' ? '' : '<input type="submit" class="submit" name="delforum" value="Delete forum" onclick="if (!confirm("Really delete this forum?")) return false;">') ?>
					<button type="button" class="submit" id="back" onclick="window.location='manageforums.php';">Back</button>
				</td>
			</tr>
		</table><br>
		<?php permtable('forums', $fid) ?>
	</form><?php
} else {
	// main page -- category/forum listing

	$qcats = $sql->query("SELECT id,title FROM categories ORDER BY ord, id");
	$cats = [];
	while ($cat = $sql->fetch($qcats))
		$cats[$cat['id']] = $cat;

	$qforums = $sql->query("SELECT f.id,f.title,f.cat FROM forums f LEFT JOIN categories c ON c.id=f.cat ORDER BY c.ord, c.id, f.ord, f.id");
	$forums = [];
	while ($forum = $sql->fetch($qforums))
		$forums[$forum['id']] = $forum;

	$catlist = ''; $c = 1;
	foreach ($cats as $cat) {
		$catlist .= "<tr><td class=\"b n$c\"><a href=\"?cid={$cat['id']}\">{$cat['title']}</a></td></tr>";
		$c = ($c == 1) ? 2 : 1;
	}

	$forumlist = ''; $c = 1; $lc = -1;
	foreach ($forums as $forum) {
		if ($forum['cat'] != $lc) {
			$lc = $forum['cat'];
			$forumlist .= "<tr class=\"c\"><td class=\"b c\">{$cats[$forum['cat']]['title']}</td></tr>";
		}
		$forumlist .= "<tr><td class=\"b n$c\"><a href=\"?fid={$forum['id']}\">{$forum['title']}</a></td></tr>";
		$c = ($c==1) ? 2:1;
	}

	?><table style="width:100%;">
		<tr>
			<td class="nb" style="width:50%; vertical-align:top;">
				<table class="c1">
					<tr class="h"><td class="b">Categories</td></tr>
					<?=$catlist ?>
					<tr class="h"><td class="b">&nbsp;</td></tr>
					<tr><td class="b n1"><a href="?cid=new">New category</a></td></tr>
				</table>
			</td>
			<td class="nb" style="width:50%; vertical-align:top;">
				<table class="c1">
					<tr class="h"><td class="b">Forums</td></tr>
					<?=$forumlist ?>
					<tr class="h"><td class="b">&nbsp;</td></tr>
					<tr><td class="b n1"><a href="?fid=new">New forum</a></td></tr>
				</table>
			</td>
		</tr>
	</table><?php
}

pagefooter();

function rec_grouplist($parent, $level, $tgroups, $groups) {
	$total = count($tgroups);
	foreach ($tgroups as $g) {
		if ($g['inherit_group_id'] != $parent)
			continue;

		$g['indent'] = $level;
		$groups[] = $g;

		$groups = rec_grouplist($g['id'], $level+1, $tgroups, $groups);
	}
	return $groups;
}
function grouplist() {
	global $sql, $usergroups;

	$groups = [];
	$groups = rec_grouplist(0, 0, $usergroups, $groups);

	return $groups;
}
function permtable($bind, $id) {
	global $sql, $rootgroup;

	$qperms = $sql->prepare("SELECT id,title FROM perm WHERE permbind_id=?",[$bind]);
	$perms = [];
	while ($perm = $sql->fetch($qperms))
		$perms[$perm['id']] = $perm['title'];

	$groups = grouplist();

	$qpermdata = $sql->prepare("SELECT x.x_id,x.perm_id,x.revoke FROM x_perm x LEFT JOIN perm p ON p.id=x.perm_id WHERE x.x_type=? AND p.permbind_id=? AND x.bindvalue=?",
		['group',$bind,$id]);
	$permdata = [];
	while ($perm = $sql->fetch($qpermdata))
		$permdata[$perm['x_id']][$perm['perm_id']] = !$perm['revoke'];

	?><table class="c1">
		<tr class="h"><td class="b">Group</td><td class="b" colspan="2">Permissions</td></tr>
	<?php
	$c = 1;
	foreach ($groups as $group) {
		if ($group['id'] == $rootgroup) break;

		$gid = $group['id'];
		$gtitle = htmlspecialchars($group['title']);

		$pf = $group['visible'] ? '<strong' : '<span';
		if ($group['nc']) $pf .= ' style="color: #'.htmlspecialchars($group['nc']).';"';
		$pf .= '>';
		$sf = $group['visible'] ? '</strong>' : '</span>';
		$gtitle = "{$pf}{$gtitle}{$sf}";

		$doinherit = false;
		$inherit = '';
		if ($group['inherit_group_id']) {
			$doinherit = !isset($permdata[$gid]) || empty($permdata[$gid]);

			$check = $doinherit ? ' checked="checked"' : '';
			$inherit = "<label><input type=\"checkbox\" name=\"inherit[{$gid}]\" value=1 onclick=\"toggleAll('perm_{$gid}',!this.checked);\"{$check}> Inherit from parent</label>&nbsp;";
		}

		$permlist = '';
		foreach ($perms as $pid => $ptitle) {
			if ($doinherit) $check = ' disabled="disabled"';
			else $check = $permdata[$gid][$pid] ? ' checked="checked"':'';

			$permlist .= "<label><input type=\"checkbox\" name=\"perm[{$gid}][{$pid}]\" value=1 class=\"perm_{$gid}\"{$check}> {$ptitle}</label> ";
		}

		?><tr class="n<?=$c ?>">
			<td class="b" style="width:200px;"><span style="white-space:nowrap;"><?=str_repeat('&nbsp; &nbsp; ', $group['indent']) . $gtitle ?></span></td>
			<td class="b" style="width:100px;"><?=$inherit ?></td>
			<td class="b"><?=$permlist ?></td>
		</tr><?php

		$c = ($c == 1) ? 2 : 1;
	}

	?><tr class="n<?=$c ?>">
		<td class="b"></td>
		<td class="b" colspan="2">
			<input type="submit" class="submit" name="save<?=($bind == 'forums' ? 'forum' : 'cat') ?>" value="Save <?=($bind == 'forums' ? 'forum' : 'category') ?>">
		</td>
	</tr></table><?php
}


function deleteperms($bind, $id) {
	global $sql;

	$sql->prepare("DELETE x FROM x_perm x LEFT JOIN perm p ON p.id=x.perm_id WHERE x.x_type=? AND p.permbind_id=? AND x.bindvalue=?",
		['group', $bind, $id]);
}

function saveperms($bind, $id) {
	global $sql, $usergroups;

	$qperms = $sql->prepare("SELECT id FROM perm WHERE permbind_id=?",[$bind]);
	$perms = [];
	while ($perm = $sql->fetch($qperms))
		$perms[] = $perm['id'];

	// delete the old perms
	deleteperms($bind, $id);

	// apply the new perms
	foreach ($usergroups as $gid=>$group) {
		if (is_root_gid($gid)) continue;

		if ($_POST['inherit'][$gid])
			continue;

		$myperms = $_POST['perm'][$gid];
		foreach ($perms as $perm)
			$sql->prepare("INSERT INTO `x_perm` (`x_id`,`x_type`,`perm_id`,`permbind_id`,`bindvalue`,`revoke`)
				VALUES (?,?,?,?,?,?)", [$gid, 'group', $perm, $bind, $id, $myperms[$perm]?0:1]);
	}
}