<?php
require('lib/common.php');
pageheader('Memberlist');

$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'posts';
$pow = isset($_REQUEST['pow']) ? $_REQUEST['pow'] : '';
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
$orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';

$ppp = 50;
if ($page < 1) $page = 1;

if ($orderby == 'a') $sortby = " ASC";
else $sortby = " DESC";

$order = 'posts' . $sortby;
if ($sort == 'name') $order = 'name' . $sortby;
if ($sort == 'reg') $order = 'regdate' . $sortby;

$where = '1';

if ($pow != '' && is_numeric($pow)) {
	$where .= " AND group_id=$pow";
}

$users = $sql->query("SELECT * FROM users WHERE $where ORDER BY $order LIMIT " . ($page - 1) * $ppp . ",$ppp");
$num = $sql->resultq("SELECT COUNT(*) FROM users WHERE $where");

if ($num <= $ppp)
	$pagelist = '';
else {
	$pagelist = 'Pages:';
	for ($p = 1; $p <= 1 + floor(($num - 1) / $ppp); $p++)
		if ($p == $page)
			$pagelist.=" $p";
		else
			$pagelist.=' ' . mlink($sort, $pow, $p, $orderby) . "$p</a>";
}

$activegroups = $sql->query("SELECT * FROM groups WHERE id IN (SELECT `group_id` FROM users GROUP BY `group_id`) ORDER BY `sortorder` ASC ");

$groups = [];
$gc = 0;
while ($group = $sql->fetch($activegroups)) {
	$grouptitle = "<span style=\"color:#" . $group['nc'] . ";\">" . $group['title'] . "</span>";
	$groups[$gc++] = mlink($sort, $group['id'], $page, $orderby) . $grouptitle . "</a>";
}

?>
<table class="c1">
	<tr class="h"><td class="b h" colspan="2">Memberlist</td></tr>
	<tr>
		<td class="b n1" width="60">Sort by:</td>
		<td class="b n2 center">
			<?=mlink('', $pow, $page, $orderby) ?> Posts</a> |
			<?=mlink('name', $pow, $page, $orderby) ?> Username</a> |
			<?=mlink('reg', $pow, $page, $orderby) ?> Registration date</a> |
			<?=mlink($sort, $pow, $page, 'd') ?>&#x25BC;</a>
			<?=mlink($sort, $pow, $page, 'a') ?>&#x25B2;</a>
		</td>
	</tr><tr>
		<td class="b n1">Group:</td>
		<td class="b n2 center">
			<?php $c = 0;
			foreach ($groups as $k => $v) {
				$c++;
				echo $v . " | ";
			}
			echo mlink($sort, '', $page, $orderby) . "All</a>" ?>
		</td>
	</tr>
</table><br>
<?php

//[KAWA] Rebuilt this to use my new renderer. Not sure what to do about the part above though X3
$headers = [
	"id" => ["caption" => "#", "width" => "32px", "align" => "center"],
	"pic" => ["caption" => "Picture", "width" => "60px"],
	"name" => ["caption" => "Name"],
	"reg" => ["caption" => "Registered on", "width" => "130px"],
	"posts" => ["caption" => "Posts", "width" => "50px"],
];
$data = [];
for ($i = ($page - 1) * $ppp + 1; $user = $sql->fetch($users); $i++) {
	$picture = ($user['usepic'] ? "<img src=userpic/$user[id] width=60 height=60>" : '<img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" width=60 height=60>');

	$data[] = [
		"id" => $user['id'] . '.',
		"pic" => $picture,
		"name" => userlink($user),
		"reg" => date($dateformat, $user['regdate']),
		"posts" => $user['posts'],
	];
}

if_empty_query($users, "No users found.", 0, true);

if ($sql->numrows($users) > 0)
	RenderTable($data, $headers);

if ($pagelist)
	echo '<br>'.$pagelist.'<br>';
pagefooter();

function mlink($sort, $pow, $page = 1, $orderby) {
	return '<a href=memberlist.php?'
			. ($sort ? "sort=$sort" : '')
			. ($pow != '' ? "&pow=$pow" : '')
			. ($page != 1 ? "&page=$page" : '')
			. ($orderby != '' ? "&orderby=$orderby" : '')
			. '>';
}