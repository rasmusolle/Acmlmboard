<?php

// [Mega-Mario] preload group data, makes things a lot easier afterwards
$usergroups = [];
$r = $sql->query("SELECT * FROM groups");
while ($g = $sql->fetch($r))
	$usergroups[$g['id']] = $g;

//this processes the permission stack, in this order:
//-user permissions
//-user's primary group permissions, then the parent group's permissions, recursively until it reaches the top
//first encountered occurence of a permission has precendence (+/-)
function load_user_permset() {
	global $logpermset, $loguser, $sql, $loggroups;

	//load user specific permissions
	$logpermset = perms_for_x('user',$loguser['id']);
	$logpermset = apply_group_permissions($logpermset,$loguser['group_id']);
}

//Badge permset

function permset_for_user($userid) {
	global $sql;
	$permset = [];
	//load user specific permissions
	$permset = perms_for_x('user',$userid);

	$permset = apply_group_permissions($permset,gid_for_user($userid));
	return $permset;
}

function is_root_gid($gid) {
	global $sql;
	$result = $sql->resultp("SELECT `default` FROM groups WHERE id = ?",[$gid]);
	if ($result < 0) return true;
	return false;
}

function gid_for_user($userid) {
	global $sql;
	$row = $sql->fetchp("SELECT group_id FROM users WHERE id=?",[$userid]);
	return $row['group_id'];
}

function load_guest_permset() {
	global $logpermset;
	$logpermset = [];
	$loggroups = [1];
	foreach ($loggroups as $gid) {
		$logpermset = apply_group_permissions($logpermset,$gid);
	}
}

function load_bot_permset() {
	global $logpermset;
	$logpermset = [];
	$loggroups = [];
	foreach ($loggroups as $gid) {
		$logpermset = apply_group_permissions($logpermset,$gid);
	}
}

function title_for_perm($permid) {
	global $sql;
	$row = $sql->fetchp("SELECT title FROM perm WHERE id=?",[$permid]);
	return $row['title'];
}

function apply_group_permissions($permset,$gid) {
	//apply group permissions from lowest node upwards
	while ($gid > 0) {
		$gpermset = perms_for_x('group',$gid);
		foreach ($gpermset as $k => $v) {
			//remove already added permissions
			if (in_permset($permset,$v)) unset($gpermset[$k]);
		}
		//merge permissions
		$permset = array_merge($permset,$gpermset);
		$gid = parent_group_for_group($gid);
	}
	return $permset;
}

function in_permset($permset,$perm) {
	foreach ($permset as $v) {
		if (($v['id'] == $perm['id']) && ($v['bindvalue'] == $perm['bindvalue']))
			return true;
	}
	return false;
}

function can_view_cat($cat) {
	//can view public categories
	if (!has_perm('view-public-categories')) return false;

	//is it a private category?
	if ($cat['private']) {
		//can view the forum's category
		if (!has_perm('view-all-private-categories') &&	!has_perm_with_bindvalue('view-private-category',$cat['id'])) return false;
	}
	return true;
}

function can_edit_post($post) {
	global $loguser;
	if (isset($post['user']) && $post['user'] == $loguser['id'] && has_perm('update-own-post')) return true;
	else if (has_perm('update-post')) return true;
	else if (isset($post['tforum']) && can_edit_forum_posts($post['tforum'])) return true;
	return false;
}

function can_edit_group_assets($gid) {
	global $sql,$loguser;
	if (has_perm('edit-all-group')) return true;
	else if (has_perm_with_bindvalue('can-edit-group', $gid)) return true;
	return false;
}

function can_edit_user_assets($gid) {
	global $sql,$loguser;
	if (has_perm('edit-all-group-member')) return true;
	else if (has_perm_with_bindvalue('can-edit-group-member', $gid)) return true;
	return false;
}

function can_edit_user($uid) {
	global $sql,$loguser;

	$gid = gid_for_user($uid);
	if (is_root_gid($gid) && !has_perm('no-restrictions')) return false;
	if ((!can_edit_user_assets($gid) && $uid!=$loguser['id']) && !has_perm('no-restrictions')) return false;

	if ($uid == $loguser['id'] && has_perm('update-own-profile')) return true;
	else if (has_perm('update-profiles')) return true;
	else if (has_perm_with_bindvalue('update-user-profile',$uid)) return true;
	return false;
}

function cats_with_view_perm() {
	global $sql;
	static $cache = "";
	if ($cache != "") return $cache;
	$cache = "(";
	$r = $sql->query("SELECT id,private FROM categories");
	while ($d=$sql->fetch($r)) {
		if (can_view_cat($d)) $cache .= "$d[id],";
	}
	$cache .= "NULL)";
	return $cache;
}

function forums_with_view_perm() {
	global $sql;
	static $cache = "";
	if ($cache != "") return $cache;
	$cache = "(";
	$r = $sql->query("SELECT f.id, f.private, f.cat, c.private cprivate FROM forums f LEFT JOIN categories c ON c.id=f.cat");
	while ($d = $sql->fetch($r)) {
		if (can_view_forum($d)) $cache .= "$d[id],";
	}
	$cache .= "NULL)";
	return $cache;
}

function forums_with_edit_posts_perm() {
	global $sql;
	static $cache = "";
	if ($cache != "") return $cache;
	$cache = "(";
	$r = $sql->query("SELECT id FROM forums");
	while ($d = $sql->fetch($r)) {
		if (can_edit_forum_posts($d['id'])) $cache .= "$d[id],";
	}
	$cache .= "NULL)";
	return $cache;
}

function forums_with_delete_posts_perm() {
	global $sql;
	static $cache = "";
	if ($cache != "") return $cache;
	$cache = "(";
	$r = $sql->query("SELECT id FROM forums");
	while ($d = $sql->fetch($r)) {
		if (can_delete_forum_posts($d['id'])) $cache .= "$d[id],";
	}
	$cache .= "NULL)";
	return $cache;
}

function forums_with_edit_threads_perm() {
	global $sql;
	static $cache = "";
	if ($cache != "") return $cache;
	$cache = "(";
	$r = $sql->query("SELECT id FROM forums");
	while ($d = $sql->fetch($r)) {
		if (can_edit_forum_threads($d['id'])) $cache .= "$d[id],";
	}
	$cache .= "NULL)";
	return $cache;
}

function forums_with_delete_threads_perm() {
	global $sql;
	static $cache = "";
	if ($cache != "") return $cache;
	$cache = "(";
	$r = $sql->query("SELECT id FROM forums");
	while ($d = $sql->fetch($r)) {
		if (can_delete_forum_threads($d['id'])) $cache .= "$d[id],";
	}
	$cache .= "NULL)";
	return $cache;
}

function can_view_forum($forum) {
	//must fulfill the following criteria
	if (!can_view_cat(['id'=>$forum['cat'], 'private'=>$forum['cprivate']])) return false;

	//can view public forums
	if (!has_perm('view-public-forums')) return false;

	//and if the forum is private
	if ($forum['private']) {
		//can view the forum
		if (!has_perm('view-all-private-forums') && !has_perm_with_bindvalue('view-private-forum',$forum['id'])) return false;
	}
	return true;
}

function needs_login($head=0) {
	global $log;
	if (!$log) {
		if ($head) pageheader('Login required');
		$err = "You need to be logged in to do that!<br><a href=login.php>Please login here.</a>";
		echo '<table class="c1"><tr class="n2"><td class="b n1 center">'.$err.'</td></tr></table>';
		pagefooter();
		die();
	}
}

function can_create_forum_thread($forum) {
	global $log;
	if ($forum['readonly'] && !has_perm('override-readonly-forums')) return false;

	//must fulfill the following criteria

	//can create public threads
	if (!has_perm('create-public-thread')) return false;
	if (!$log) return false;

	//and if the forum is private
	if (isset($forum['private']) && $forum['private']) {
		//can view the forum
		if (!has_perm('create-all-private-forum-threads') && !has_perm_with_bindvalue('create-private-forum-thread',$forum['id'])) return false;
	}
	return true;
}

function can_create_forum_post($forum) {
	global $log;
	if ($forum['readonly'] && !has_perm('override-readonly-forums')) return false;

	//must fulfill the following criteria

	//can create public threads
	if (!has_perm('create-public-post')) return false;
	if (!$log) return false;

	//and if the forum is private
	if ($forum['private']) {
		//can view the forum
		if (!has_perm('create-all-private-forum-posts') && !has_perm_with_bindvalue('create-private-forum-post',$forum['id'])) return false;
	}
	return true;
}

function can_edit_forum_posts($forumid) {
	if (!has_perm('update-post') && !has_perm_with_bindvalue('edit-forum-post',$forumid)) return false;
	return true;
}

function can_delete_forum_posts($forumid) {
	if (!has_perm('delete-post') && !has_perm_with_bindvalue('delete-forum-post',$forumid)) return false;
	return true;
}

function can_edit_forum_threads($forumid) {
	if (!has_perm('update-thread') && !has_perm_with_bindvalue('edit-forum-thread',$forumid)) return false;
	return true;
}

function can_delete_forum_threads($forumid) {
	if (!has_perm('delete-thread') && !has_perm_with_bindvalue('delete-forum-thread',$forumid)) return false;
	return true;
}

function catid_of_forum($forumid) {
	global $sql;
	$row = $sql->fetchp("SELECT cat FROM forums WHERE id=?",[$forumid]);
	return $row['cat'];
}

function has_perm($permid) {
	global $logpermset;
	foreach ($logpermset as $k => $v) {
		if ($v['id'] == 'no-restrictions') return true;
		if ($permid == $v['id'] && !$v['revoke']) return true;
	}
	return false;
}

function has_perm_revoked($permid) {
	global $logpermset;
	foreach ($logpermset as $k => $v) {
		if ($v['id'] == 'no-restrictions') return false;
		if ($permid == $v['id'] && $v['revoke']) return true;
	}
	return false;
}

function has_perm_with_bindvalue($permid,$bindvalue) {
	global $logpermset;
	foreach ($logpermset as $k => $v) {
		if ($v['id'] == 'no-restrictions') return true;
		if ($permid == $v['id'] && !$v['revoke'] && $bindvalue == $v['bindvalue'])
		return true;
	}
	return false;
}

function has_special_perm($permid) {
	global $logpermset;
	//This function does the same as has_perm, but does not check for no-retrictions.
	foreach ($logpermset as $k => $v) {
		if ($permid == $v['id'] && !$v['revoke']) return true;
	}
	return false;
}

function parent_group_for_group($groupid) {
	global $usergroups;

	$gid = $usergroups[$groupid]['inherit_group_id'];
	if ($gid > 0) {
		return $gid;
	} else {
		return 0;
	}
}

function perms_for_x($xtype,$xid) {
	global $sql;
	$res = $sql->prepare("SELECT * FROM x_perm WHERE x_type=? AND x_id=?",
					[$xtype,$xid]);

	$out = [];
	$c = 0;
	while ($row = $sql->fetch($res)) {
		$out[$c++] = [
			'id' => $row['perm_id'],
			'bind_id' => $row['permbind_id'],
			'bindvalue' => $row['bindvalue'],
			'revoke' => $row['revoke'],
			'xtype' => $xtype,
			'xid' => $xid
		];
	}
	return $out;
}

function forumlink_by_id($fid) {
	global $sql;
	$f = $sql->fetchp("SELECT id,title FROM forums WHERE id=? AND id IN ".forums_with_view_perm(),[$fid]);
	if ($f) return "<a href=forum.php?id=$f[id]>$f[title]</a>";
	else return 0;
}

function threadlink_by_id($tid) {
	global $sql;
	$thread = $sql->fetchp("SELECT id,title FROM threads WHERE id=? AND forum IN ".forums_with_view_perm(),[$tid]);
	if ($thread) return "<a href=thread.php?id=$thread[id]>".forcewrap(htmlval($thread[title]))."</a>";
	else return 0;
}
