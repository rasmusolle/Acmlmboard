<?php

require('lib/common.php');
pageheader('Memberlist');

$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'posts';
$sex = isset($_REQUEST['sex']) ? $_REQUEST['sex'] : '';
$pow = isset($_REQUEST['pow']) ? $_REQUEST['pow'] : '';
$ppp = isset($_REQUEST['ppp']) ? (int)$_REQUEST['ppp'] : 50;
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
$orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';

if ($ppp < 1) $ppp = 50;
if ($page < 1) $page = 1;

if ($orderby == 'a') $sortby = " ASC";
else $sortby = " DESC";

$order = 'posts' . $sortby;
if ($sort == 'name') $order = 'name' . $sortby;
if ($sort == 'reg') $order = 'regdate' . $sortby;

$where = '1';
if ($sex == 'm') $where = 'sex=0';
if ($sex == 'f') $where = 'sex=1';
if ($sex == 'n') $where = 'sex=2';

if ($pow != '' && is_numeric($pow)) {
	if ($pow == '-1')
		$where.=" AND `group_id` =  ANY (SELECT `x_id` FROM `x_perm` WHERE `x_id`= ANY (SELECT `id` FROM `group` WHERE `perm_id` = \"show-as-staff\") AND `x_type` =\"group\")";
	else
		$where.=" AND group_id=$pow";
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
			$pagelist.=' ' . mlink($sort, $sex, $pow, $ppp, $p, $orderby, $displayn) . "$p</a>";
}

$activegroups = $sql->query("SELECT * FROM `group` WHERE id IN (SELECT `group_id` FROM users GROUP BY `group_id`) ORDER BY `sortorder` ASC ");

$groups = array();
$gc = 0;
while ($group = $sql->fetch($activegroups)) {
	$grouptitle = "<span style=\"color:#" . $group['nc'] . ";\">" . $group['title'] . "</span>";
	$groups[$gc++] = mlink($sort, $sex, $group['id'], $ppp, $page, $orderby) . $grouptitle . "</a>";
}

?>
<table class="c1">
	<tr class="h"><td class="b h" colspan="2"><?=$num . ' user' . ($num > 1 ? 's' : '') ?> found.</td></tr>
	<tr>
		<td class="b n1" width="105">Sort by:</td>
		<td class="b n2 center">
			<?=mlink('', $sex, $pow, $ppp, $page, $orderby) ?> Posts</a> |
			<?=mlink('name', $sex, $pow, $ppp, $page, $orderby) ?> Username</a> |
			<?=mlink('reg', $sex, $pow, $ppp, $page, $orderby) ?> Registration date</a>
		</td>
	</tr><tr>
		<td class="b n1">Order by:</td>
		<td class="b n2 center">
			<?=mlink($sort, $sex, $pow, $ppp, $page, 'd') . "Descending</a> |" ?>
			<?=mlink($sort, $sex, $pow, $ppp, $page, 'a') . "Ascending</a>" ?>
		</td>
	</tr><tr>
		<td class="b n1">Sex:</td>
		<td class="b n2 center">
			<?=mlink($sort, 'm', $pow, $ppp, $page, $orderby) . "Male</a> | " .
			mlink($sort, 'f', $pow, $ppp, $page, $orderby) . "Female</a> | " .
			mlink($sort, 'n', $pow, $ppp, $page, $orderby) . "N/A</a> | " .
			mlink($sort, '', $pow, $ppp, $page, $orderby) . "All</a>" ?>
	<tr>
		<td class="b n1">Group:</td>
		<td class="b n2 center">
			<?php $c = 0;
			foreach ($groups as $k => $v) {
			$c++;
				echo $v . " | ";
			}
			echo mlink($sort, $sex, '-1', $ppp, $page, $orderby) . "All Staff</a> | " .
			mlink($sort, $sex, '', $ppp, $page, $orderby) . "All</a>" ?>
		</td>
	</tr>
</table><br>
<?php

//[KAWA] Rebuilt this to use my new renderer. Not sure what to do about the part above though X3
$headers = array(
	"id" => array("caption" => "#", "width" => "32px", "align" => "center"),
	"pic" => array("caption" => "Picture", "width" => "60px"),
	"name" => array("caption" => "Name"),
	"reg" => array("caption" => "Registered on", "width" => "130px"),
	"posts" => array("caption" => "Posts", "width" => "50px"),
);
$data = array();
for ($i = ($page - 1) * $ppp + 1; $user = $sql->fetch($users); $i++) {
	$picture = ($user['usepic'] ? "<img src=userpic/$user[id] width=60 height=60>" : '<img src=img/_.png width=60 height=60>');

	$data[] = array(
		"id" => $user['id'] . '.',
		"pic" => $picture,
		"name" => userlink($user),
		"reg" => date($dateformat, $user['regdate']),
		"posts" => $user['posts'],
	);
}

RenderTable($data, $headers);

if ($pagelist)
	echo '<br>'.$pagelist.'<br>';
pagefooter();

function mlink($sort, $sex, $pow, $ppp, $page = 1, $orderby) {
	return '<a href=memberlist.php?'
			. ($sort ? "sort=$sort" : '')
			. ($sex ? "&sex=$sex" : '')
			. ($pow != '' ? "&pow=$pow" : '')
			. ($ppp != 50 ? "&ppp=$ppp" : '')
			. ($page != 1 ? "&page=$page" : '')
			. ($orderby != '' ? "&orderby=$orderby" : '')
			. '>';
}

?>
