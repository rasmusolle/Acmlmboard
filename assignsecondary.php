<?php
require("lib/common.php");

$pagebar = array();

pageheader("Assign Secondary Groups");

$id = (isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : '');
$uid = (isset($_GET['uid']) && is_numeric($_GET['uid']) ? $_GET['uid'] : '');
$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
$act = (isset($_REQUEST['act']) ? $_REQUEST['act'] : '');

if (!has_perm('assign-secondary-groups')) { noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>"); pagefooter(); die(); }
if ($uid == '' || $uid == 0) { noticemsg("Error", "No User Requested.<br> <a href=./>Back to main</a>"); pagefooter(); die(); }

if ($action == "del") {
	unset($action);
	if((is_root_gid($id) || !can_edit_group_assets($id) && $id!=$loguser['group_id']) && !has_perm('no-restrictions')) {
		$pagebar['message'] = "You do not have the permissions to revoke this group.";
	} else if ($id > 0) {
		if ($sql->prepare('DELETE FROM user_group WHERE user_id=? AND group_id=? LIMIT 1',array($uid, $id))) {
			$pagebar['message'] = "User successfully removed from group.";
		} else {
			$pagebar['message'] = "Unable to remove user from group.";
		}
	}
}

if (empty($action)) {
	$headers = array(
		"id" => array("caption" => "#",
			"width" => "32px", "align" => "center", "color" => 1, "hidden" => 'true'),
		"group" => array("caption"=>"Name", "width"=>"1400px", "align"=>"center", "color"=>2),
		"edit" => array("caption"=>"Actions", "align" => "left", "color"=>1),
	);

	$data = array();
	$sndgReq = $sql->query("SELECT * FROM `group`
						RIGHT JOIN `user_group` ON `group`.`id` = `user_group`.`group_id`
						WHERE `user_group`.`user_id`='$uid'");
	while ($sndg = $sql->fetch($sndgReq)) {
		$actions = array(array('title' => 'Revoke','href' => 'assignsecondary.php?action=del&uid='.$uid.'&id='.$sndg['id'], 'confirm' => true));
		$data[] = array("id" => $sndg['id'], "name" => $sndg['title'], "edit" => RenderActions($actions,1));
	}
	$pagebar['title'] = 'Assign Secondary Group';
	$pagebar['actions'] = array(array('title' => 'Assign Group','href' => 'assignsecondary.php?action=new&uid='.$uid));
	RenderPageBar($pagebar);
	RenderTable($data, $headers);
} elseif ($action) {
	if (!empty($act)) {
		$s = array('group_id' => $_POST['group_id'], 'group_var' => $_POST['group_var']);
		if ((is_root_gid($s['group_id']) || !can_edit_group_assets($s['group_id']) && $s['group_id']!=$loguser['group_id']) && !has_perm('no-restrictions')) {
			$pagebar['message'] = "You do not have the permissions to assign this group.";
		} else if ($sql->prepare('INSERT INTO user_group SET user_id=?,group_id=?,sortorder=? ;', array($uid,$s['group_id'],0))) {
			$id = $sql->insertid();
			$pagebar['message'] = "Group successfully added.";
		} else {
			$pagebar['message'] = "Unable to assign user to group.";
		}
	}
	$pagebar['breadcrumb'] = array(array('title' => 'Edit Assigned Groups','href' => 'assignsecondary.php?uid='.$uid));

	if ($id > 0) {
		$t = $sql->fetchp('SELECT * FROM user_group WHERE user_id=? AND group_id=?',array($uid), array($id));
		$pagebar['title'] = $t['name'];
		$pagebar['actions'] = array(array('title' => 'Delete Badge','href' => 'assignsecondary.php?action=del&uid='.$uid.'&id='.$id, 'confirm' => true));
	} else {
		$pagebar['title'] = 'Assign Group';
		$t = array('group_id' => '');
	}
	RenderPageBar($pagebar);

	$allbdg = array();
	$qallgroups = $sql->query("SELECT `id`, `title` FROM `group`");

	while ($allbdgquery= $sql->fetch($qallgroups)) {
		$allbdg[$allbdgquery['id']]= $allbdgquery['title'];
	}

	$form = array(
		'action' => urlcreate('assignsecondary.php', array('action' => $action,'uid' => $uid)),
		'method' => 'POST',
		'categories' => array(
			'metadata' => array('title' => 'Secondary Group Assign','fields' => array('group_id' => array('title' => 'Secondary Group','type' => 'dropdown','choices' => $allbdg,'value' => 0))),
			'actions' => array('fields' => array('act' => array('title' => 'Assign Group','type' => 'submit')))
		)
	);
	RenderForm($form);
}

pagefooter();