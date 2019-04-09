<?php
require('lib/common.php');

/*
'special' permissions:
 * no-restrictions: cancels out all the 'normal' others
 * show-as-staff: for memberlist
 * banned, staff: seem like useless/unimplemented permissions
*/
$permlist = null;

if (!has_perm('edit-permissions')) noticemsg("Error", "You have no permissions to do this!", true);

if (isset($_GET['gid'])) {
	$id = (int)$_GET['gid'];
	if ((is_root_gid($id) || (!can_edit_group_assets($id) && $id!=$loguser['group_id'])) && !has_perm('no-restrictions')) {
		noticemsg("Error", "You have no permissions to do this!", true);
	}
	if ($loguser['group_id'] == $id && !has_perm('edit-own-permissions')) {
		noticemsg("Error", "You have no permissions to do this!", true);
	}
	$permowner = $sql->fetchp("SELECT id,title,inherit_group_id FROM groups WHERE id=?", [$id]);
	$type = 'group';
} else if (isset($_GET['uid'])) {
	$id = (int)$_GET['uid'];

	$tuser = $sql->fetchp("SELECT `group_id` FROM users WHERE id=?",[$id]);
	if ((is_root_gid($tuser[$u.'group_id']) || (!can_edit_user_assets($tuser[$u.'group_id']) && $id!=$loguser['id'])) && !has_perm('no-restrictions')) {
		noticemsg("Error", "You have no permissions to do this!", true);
	}

	if ($id == $loguser['id'] && !has_perm('edit-own-permissions')) {
		noticemsg("Error", "You have no permissions to do this!", true);
	}
	$permowner = $sql->fetchp("SELECT u.id,u.name AS title,u.group_id,g.title AS group_title FROM users u LEFT JOIN groups g ON g.id=u.group_id WHERE u.id=?", [$id]);
	$type = 'user';
} else if (isset($_GET['fid'])) {
	$id = (int)$_GET['fid'];
	$permowner = $sql->fetchp("SELECT id,title FROM forums WHERE id=?", [$id]);
	$type = 'forum';
} else {
	$id = 0;
	$permowner = null;
	$type = '';
}

if (!$permowner) noticemsg("Error", "Invalid {$type} ID.", true);

$errmsg = '';

if (isset($_POST['addnew'])) {
	$revoke = (int)$_POST['revoke_new'];
	$permid = stripslashes($_POST['permid_new']);
	$bindval = (int)$_POST['bindval_new'];

	if (has_perm('no-restrictions') || $permid != 'no-restrictions') {
		$sql->prepare("INSERT INTO `x_perm` (`x_id`,`x_type`,`perm_id`,`permbind_id`,`bindvalue`,`revoke`) VALUES (?,?,?,'',?,?)",
			[$id, $type, $permid, $bindval, $revoke]);
		$msg = "The ".title_for_perm($permid)." permission has been successfully assigned!";
	} else {
		$msg = "You do not have the permissions to assign the ".title_for_perm($permid)." permission!";
	}
} else if (isset($_POST['apply'])) {
	$keys = array_keys($_POST['apply']);
	$pid = $keys[0];

	$revoke = (int)$_POST['revoke'][$pid];
	$permid = stripslashes($_POST['permid'][$pid]);
	$bindval = (int)$_POST['bindval'][$pid];

	if (has_perm('no-restrictions') || $permid != 'no-restrictions') {
		$sql->prepare("UPDATE `x_perm` SET `perm_id`=?, `bindvalue`=?, `revoke`=? WHERE `id`=?",
			[$permid, $bindval, $revoke, $pid]);
		$msg = "The ".title_for_perm($permid)." permission has been successfully edited!";
	} else {
		$msg = "You do not have the permissions to edit the ".title_for_perm($permid)." permission!";
	}
} else if (isset($_POST['del'])) {
	$keys = array_keys($_POST['del']);
	$pid = $keys[0];
	$permid = stripslashes($_POST['permid'][$pid]);
	if (has_perm('no-restrictions') || $permid != 'no-restrictions') {
		$sql->prepare("DELETE FROM `x_perm`WHERE `id`=?", [$pid]); $msg="The ".title_for_perm($permid)." permission has been successfully deleted!";
	} else {
		$msg = "You do not have the permissions to delete the ".title_for_perm($permid)." permission!";
	}
}

pageheader('Edit permissions');

$pagebar = [
	'breadcrumb' => [['href'=>'./', 'title'=>'Main']],
	'title' => 'Edit permissions',
	'actions' => [],
	'message' => (isset($msg) ? $msg : '')
];

RenderPageBar($pagebar);

echo '<br><form action="" method="POST">';

$header = ['c0' => ['caption' => '&nbsp;'], 'c1' => ['caption' => '&nbsp;']];
$data = [];

$permset = PermSet($type, $id);
$row = []; $i = 0;
while ($perm = $sql->fetch($permset)) {
	$pid = $perm['id'];

	$field = RevokeSelect("revoke[{$pid}]", $perm['revoke']);
	$field .= PermSelect("permid[{$pid}]", $perm['perm_id']);
	$field .= "for ID <input type=\"text\" name=\"bindval[{$pid}]\" value=\"".$perm['bindvalue']."\" size=3 maxlength=8> ";
	$field .= "<input type=\"submit\" name=\"apply[{$pid}]\" value=\"Apply changes\">";
	$field .= "<input type=\"submit\" name=\"del[{$pid}]\" value=\"Remove\">";
	$row['c'.$i] = $field;

	$i++;
	if ($i == 2) {
		$data[] = $row;
		$row = [];
		$i = 0;
	}
}
if (($i % 2) != 0) {
	$row['c1'] = '&nbsp;';
	$data[] = $row;
}

RenderTable($data, $header);

$header = ['c0' => ['caption' => 'Add permission']];
$field = RevokeSelect("revoke_new", 0);
$field .= PermSelect("permid_new", null);
$field .= "for ID <input type=\"text\" name=\"bindval_new\" value=\"\" size=3 maxlength=8> ";
$field .= "<input type=\"submit\" name=\"addnew\" value=\"Add\">";
$data = [['c0' => $field]];
RenderTable($data, $header);

echo "</form><br>";

$permset = PermSet($type, $id);
$permsassigned = [];

$permoverview = '<strong>'.ucfirst($type).' permissions:</strong><br>';
$permoverview .= PermTable($permset);

if ($type == 'group' && $permowner['inherit_group_id'] > 0) {
	$permoverview .= '<br><hr>';
	$permoverview .= '<strong>Permissions inherited from parent groups:</strong><br>';

	$parentid = $permowner['inherit_group_id'];
	while ($parentid > 0) {
		$parent = $sql->fetchp("SELECT title,inherit_group_id FROM groups WHERE id=?", [$parentid]);
		$permoverview .= '<br>'.htmlspecialchars($parent['title']).':<br>';
		$permoverview .= PermTable(PermSet('group', $parentid));
		$parentid = $parent['inherit_group_id'];
	}
} else if ($type == 'user') {
	$permoverview .= '<hr>';
	$permoverview .= '<strong>Permissions inherited from the group \''.htmlspecialchars($permowner['group_title']).'\':</strong><br>';

	$parentid = $permowner['group_id'];
	while ($parentid > 0) {
		$parent = $sql->fetchp("SELECT title,inherit_group_id FROM groups WHERE id=?", [$parentid]);
		$permoverview .= '<br>'.htmlspecialchars($parent['title']).':<br>';
		$permoverview .= PermTable(PermSet('group', $parentid));
		$parentid = $parent['inherit_group_id'];
	}
}

$header = ['cell' => ['caption'=>"Permissions overview for {$type} '".htmlspecialchars($permowner['title'])."'"]];
$data = [['cell' => $permoverview]];
RenderTable($data, $header);

echo '<br>';
$pagebar['message'] = '';
RenderPageBar($pagebar);

pagefooter();

function PermSelect($name, $sel) {
	global $sql, $permlist;

	$cat = -1;
	if (!$permlist) {
		$perms = $sql->query("
			SELECT p.id AS permid, p.title AS permtitle, pc.id AS cat, pc.title AS cattitle
			FROM perm p LEFT JOIN permcat pc ON pc.id=p.permcat_id
			ORDER BY pc.sortorder ASC, p.title ASC");

		$permlist = [];
		while ($perm = $sql->fetch($perms))
			$permlist[] = $perm;
	}

	$out = "\t<select name=\"{$name}\">\n";
	foreach ($permlist as $perm) {
		if ($perm['cat'] != $cat) {
			if ($cat != -1) $out .= "\t\t</optgroup>\n";
			$cat = $perm['cat'];
			$out .= "\t\t<optgroup label=\"".($perm['cattitle'] ? htmlspecialchars($perm['cattitle']) : 'General')."\">\n";
		}

		$chk = ($perm['permid'] == $sel) ? ' selected="selected"' : '';
		$out .= "\t\t\t<option value=\"".htmlspecialchars($perm['permid'])."\"{$chk}>".htmlspecialchars($perm['permtitle'])."</option>\n";
	}
	$out .= "\t\t</optgroup>\n\t</select>\n";

	return $out;
}

function RevokeSelect($name, $sel) {
	$out = "\t<select name=\"{$name}\">\n";
	$out .= "\t\t<option value=\"0\"".($sel==0 ? ' selected="selected"':'').">Grant</option>\n";
	$out .= "\t\t<option value=\"1\"".($sel==1 ? ' selected="selected"':'').">Revoke</option>\n";
	$out .= "\t</select>\n";
	return $out;
}

function PermSet($type, $id) {
	global $sql;
	return $sql->prepare("
		SELECT x.*, p.title AS permtitle, pb.title AS bindtitle
		FROM x_perm x LEFT JOIN perm p ON p.id=x.perm_id LEFT JOIN permbind pb ON pb.id=p.permbind_id
		WHERE x.x_type=? AND x.x_id=?",
		[$type,$id]);
  }

function PermTable($permset) {
	global $sql, $permsassigned;
	$ret = '';

	$i = 0;
	while ($perm = $sql->fetch($permset)) {
		$key = $perm['perm_id'];
		if ($perm['bindvalue']) $key .= '['.$perm['bindvalue'].']';

		$discarded = false;
		if (isset($permsassigned[$key])) $discarded = true;
		else $permsassigned[$key] = true;

		$permtitle = $perm['permtitle'];
		if (!$permtitle) $permtitle = $perm['perm_id'];

		$ret .= "<td style=\"width:25%;\">&bull; ";
		if ($discarded) $ret .= '<s>';
		if ($perm['revoke']) $ret .= '<span style="color:#f88;">Revoke</span>: ';
		else $ret .= '<span style="color:#8f8;">Grant</span>: ';
		$ret .= '\''.htmlspecialchars($permtitle).'\'';

		if ($perm['bindvalue']) {
			$bindtitle = strtolower($perm['bindtitle']);
			if (!$bindtitle) $bindtitle = $perm['permbind_id'];
			if (!$bindtitle) $bindtitle = 'ID';
			$ret .= ' for '.htmlspecialchars($bindtitle).' #'.$perm['bindvalue'];
		}

		if ($discarded) $ret .= '</s>';

		$ret .= "</td>\n";

		$i++;
		if (($i % 4) == 0) $ret .= "</tr>\n<tr>\n";
	}

	if (($i % 4) != 0)
		$ret .= "<td colspan=\"".(4-($i%4))."\">&nbsp;</td>\n";

	if (!$ret) $ret = "<td>&bull; None</td>\n";

	return "<table style=\"width:100%;\">\n<tr>\n{$ret}</tr>\n</table>\n";
}