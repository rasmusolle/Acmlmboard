<?php

function editthread($id, $title = '', $forum = 0, $closed= -1, $sticky= -1, $delete = -1) {
	global $sql;
	if ($delete < 1) {
		$set = '';
		if ($title != '') $set .= ",title='$title'";
		if ($closed >= 0) $set .= ",closed=$closed";
		if ($sticky >= 0) $set .= ",sticky=$sticky";
		$set[0] = ' ';
		if (strlen(trim($set))>0&&!is_array($set)) $sql->prepare("UPDATE threads SET $set WHERE id = ?", [$id]);

		if ($forum)
			movethread($id,$forum);
	}
}

function movethread($id, $forum) {
	global $sql;

	if (!$sql->resultp("SELECT COUNT(*) FROM forums WHERE id = ?", [$forum])) return;

	$thread = $sql->fetchp("SELECT forum, replies FROM threads WHERE id = ?", [$id]);
	$sql->prepare("UPDATE threads SET forum = ? WHERE id = ?", [$forum, $id]);

	$last1 = $sql->fetchp("SELECT lastdate,lastuser,lastid FROM threads WHERE forum = ? ORDER BY lastdate DESC LIMIT 1", [$thread['forum']]);
	$last2 = $sql->fetchp("SELECT lastdate,lastuser,lastid FROM threads WHERE forum = ? ORDER BY lastdate DESC LIMIT 1", [$forum]);
	if ($last1)
		$sql->prepare("UPDATE forums SET posts = posts - (? + 1), threads = threads - 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
		[$thread['replies'], $last1['lastdate'], $last1['lastuser'], $last1['lastid'], $thread['forum']]);

	if ($last2)
		$sql->prepare("UPDATE forums SET posts = posts + (? + 1), threads = threads + 1, lastdate = ?, lastuser = ?, lastid = ? WHERE id = ?",
		[$thread['replies'], $last2['lastdate'], $last2['lastuser'], $last2['lastid'], $forum]);
}

function getforumbythread($tid) {
	global $sql;
	static $cache;
	return isset($cache[$tid]) ? $cache[$tid] : $cache[$tid] = $sql->resultP("SELECT forum FROM threads WHERE id = ?", [$tid]);
}
