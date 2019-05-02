<?php
require('lib/common.php');

if (!has_perm('edit-groups')) noticemsg("Error", "You have no permissions to do this!", true);

$act = (isset($_GET['act']) ? $_GET['act'] : '');
$errmsg = '';
$caneditperms = has_perm('edit-permissions');

if ($act == 'delete') {
	$id = unpacksafenumeric($_GET['id']);
	$group = $sql->fetchp("SELECT * FROM groups WHERE id = ?", [$id]);

	if (!$group)
		$errmsg = 'Cannot delete group: invalid group ID';
	else {
		$usercount = $sql->resultp("SELECT COUNT(*) FROM users WHERE group_id = ?", [$group['id']]);
		if ($usercount > 0) $errmsg = 'This group cannot be deleted because it contains users';

		if (!$errmsg && !$caneditperms) {
			$permcount = $sql->resultp("SELECT COUNT(*) FROM x_perm WHERE x_type = 'group' AND x_id = ?", [$group['id']]);
			if ($permcount > 0) $errmsg = 'This group cannot be deleted because it has permissions attached and you may not edit permissions.';
		}

		if (!$errmsg) {
			$sql->prepare("DELETE FROM groups WHERE id = ?", [$group['id']]);
			$sql->prepare("DELETE FROM x_perm WHERE x_type = 'group' AND x_id = ?", [$group['id']]);
			$sql->prepare("UPDATE groups SET inherit_group_id = 0 WHERE inherit_group_id = ?", [$group['id']]);
			redirect('editgroups.php');
		}
	}
} else if (isset($_POST['submit']) && ($act == 'new' || $act == 'edit')) {
	$title = trim($_POST['title']);

	$parentid = $_POST['inherit_group_id'];
	if ($parentid < 0 || $parentid > $sql->resultq("SELECT MAX(id) FROM groups")) $parentid = 0;

	if ($act == 'edit') {
		$recurcheck = [$_GET['id']];
		$pid = $parentid;
		while ($pid > 0) {
			if ($pid == $recurcheck[0]) {
				$errmsg = 'Endless recursion detected, choose another parent for this group';
				break;
			}

			$recurcheck[] = $pid;
			$pid = $sql->resultp("SELECT inherit_group_id FROM groups WHERE id = ?",[$pid]);
		}
	}

	if (!$errmsg) {
		$sortorder = (int)$_POST['sortorder'];

		$visible = $_POST['visible'] ? 1:0;

		if (empty($title))
			$errmsg = 'You must enter a name for the group.';
		else {
			$values = [$title, $_POST['nc'], $parentid, $sortorder, $visible];

			if ($act == 'new')
				$sql->prepare("INSERT INTO groups VALUES (0,?,?,?,?,?)", $values);
			else {
				$values[] = $_GET['id'];
				$sql->prepare("UPDATE groups SET title = ?,nc = ?,inherit_group_id = ?,sortorder = ?,visible = ? WHERE id = ?", $values);
			}
			redirect('editgroups.php');
		}
	}
}

pageheader('Edit groups');

if ($act == 'new' || $act == 'edit') {
	$pagebar = [
		'breadcrumb' => [['href'=>'./', 'title'=>'Main'], ['href'=>'management.php', 'title'=>'Management'], ['href'=>'editgroups.php', 'title'=>'Edit groups']],
		'title' => '',
		'actions' => [['href'=>'editgroups.php?act=new', 'title'=>'New group']],
		'message' => $errmsg
	];

	if ($act == 'new') {
		$group = ['id'=>0, 'title'=>'', 'nc'=>'', 'inherit_group_id'=>0, 'sortorder'=>0, 'visible'=>0];
		$pagebar['title'] = 'New group';
	} else {
		$group = $sql->fetchp("SELECT * FROM groups WHERE id = ?",[$_GET['id']]);
		if (!$group) { noticemsg("Error", "Invalid group ID."); pagefooter(); die(); }
		$pagebar['title'] = 'Edit group';
	}

	if ($group) {
		$grouplist = [0 => '(none)'];
		$allgroups = $sql->prepare("SELECT id,title FROM groups WHERE id != ? ORDER BY sortorder",[$group['id']]);
		while ($g = $sql->fetch($allgroups))
			$grouplist[$g['id']] = $g['title'];

		$visiblelist = [1=>'Visible', 0=>'Invisible'];

		$form = [
			'action' => '',
			'method' => 'POST',
			'title' => 'Group settings',
			'fields' => [
				'title' => ['title'=>'Name', 'type'=>'text', 'length'=>255, 'size'=>50, 'value'=>$group['title']],
				'inherit_group_id' => ['title'=>'Parent group', 'type'=>'dropdown', 'choices'=>$grouplist, 'value'=>$group['inherit_group_id']],
				'sortorder' => ['title'=>'Sort order', 'type'=>'numeric', 'length'=>8, 'size'=>4, 'value'=>$group['sortorder']],
				'visible' => ['title'=>'Visibility', 'type'=>'radio', 'choices'=>$visiblelist, 'value'=>$group['visible']],
				'nc' => ['title'=>'Username color', 'type'=>'text', 'length'=>6, 'size'=>6, 'value'=>$group['nc']],
				'submit' => ['title'=>($act=='new' ? 'Create group':'Apply changes'), 'type'=>'submit'],
			],
		];

		RenderPageBar($pagebar);
		echo '<br>';
		RenderForm($form);
		echo '<br>';
		$pagebar['message'] = '';
		RenderPageBar($pagebar);
	}
} else {
	$pagebar = [
		'breadcrumb' => [['href'=>'./', 'title'=>'Main'], ['href'=>'management.php', 'title'=>'Management']],
		'title' => 'Edit groups',
		'actions' => [['href'=>'editgroups.php?act=new', 'title'=>'New group']],
		'message' => $errmsg
	];

	RenderPageBar($pagebar);
	echo '<br>';

	$header = [
		'sort' => ['name'=>'Order', 'width'=>'32px', 'align'=>'center'],
		'id' => ['name'=>'#', 'width'=>'32px', 'align'=>'center'],
		'name' => ['name'=>'Name', 'align'=>'center'],
		'parent' => ['name'=>'Parent group', 'width' => '240px', 'align'=>'center'],
		'actions' => ['name'=>'', 'width'=>'240px', 'align'=>'right'],
	];

	$groups = $sql->query("SELECT g.*, pg.title parenttitle FROM groups g LEFT JOIN groups pg ON pg.id=g.inherit_group_id ORDER BY sortorder");
	$data = [];

	while ($group = $sql->fetch($groups)) {
		$name = htmlspecialchars($group['title']);
		if ($group['visible']) $name = "<strong>{$name}</strong>";
		if ($group['nc']) $name = str_replace('<strong>', "<strong style=\"color: #{$group['nc']};\">", $name);

		$actions = [];
		if ($caneditperms) $actions[] = ['href'=>'editperms.php?gid='.$group['id'], 'title'=>'Edit perms'];
		$actions[] = ['href'=>'editgroups.php?act=edit&id='.$group['id'], 'title'=>'Edit'];
		if ($caneditperms && $group['id'] > 7) 
			$actions[] = ['href'=>'editgroups.php?act=delete&id='.urlencode(packsafenumeric($group['id'])), 'title'=>'Delete',
				'confirm'=>'Are you sure you want to delete the group "'.htmlspecialchars($group['title']).'"?'];

		$data[] = [
			'sort' => $group['sortorder'],
			'id' => $group['id'],
			'name' => $name,
			'parent' => $group['parenttitle'] ? htmlspecialchars($group['parenttitle']) : '<small>(none)</small>',
			'actions' => RenderActions($actions,true),
		];
	}

	RenderTable($data, $header);
	echo '<br>';
	$pagebar['message'] = '';
	RenderPageBar($pagebar);
}

pagefooter();