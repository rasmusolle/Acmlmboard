<?php
require('lib/common.php');
needs_login(1);

if (!has_perm('view-own-pms')) {
	noticemsg("Error", "You have no permissions to do this!", true);
}

$fieldlist = '';
$ufields = ['posts', 'regdate', 'lastpost', 'lastview', 'location', 'rankset', 'title', 'usepic', 'head', 'sign'];
foreach ($ufields as $field)
	$fieldlist .= "u.$field u$field,";

$pid = (isset($_GET['id']) ? $_GET['id'] : null);

if (!$pid) {
	noticemsg("Error", "Private message does not exist.", true);
}

$pmsgs = $sql->fetchp("SELECT ".userfields('u','u').",$fieldlist p.* FROM pmsgs p LEFT JOIN users u ON u.id = p.userfrom WHERE p.id = ?", [$pid]);
if ($pmsgs == null) noticemsg("Error", "Private message does not exist.", true);
$tologuser = ($pmsgs['userto'] == $loguser['id']);

if (((!$tologuser && $pmsgs['userfrom'] != $loguser['id']) && !has_perm('view-user-pms')))
	noticemsg("Error", "Private message does not exist.", true);
elseif ($tologuser && $pmsgs['unread'])
	$sql->prepare("UPDATE pmsgs SET unread = 0 WHERE id = ?", [$pid]);

pageheader($pmsgs['title']);

$pagebar = [
	'breadcrumb' => [
		['href' => './', 'title' => 'Main'],
		['href' => "private.php".(!$tologuser ? '?id='.$pmsgs['userto'] : ''), 'title' => 'Private messages']
	],
	'title' => htmlval($pmsgs['title']),
	'actions' => [['href' => "sendprivate.php?pid=$pid", 'title' => 'Reply']]
];

$pmsgs['id'] = 0; $pmsgs['num'] = 0;

RenderPageBar($pagebar);
echo '<br>' . threadpost($pmsgs) . '<br>';
RenderPageBar($pagebar);

pagefooter();