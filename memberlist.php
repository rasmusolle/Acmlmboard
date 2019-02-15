<?php

require('lib/common.php');
pageheader('Memberlist');

$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'posts';
$sex = isset($_REQUEST['sex']) ? $_REQUEST['sex'] : '';
$pow = isset($_REQUEST['pow']) ? $_REQUEST['pow'] : '';
$ppp = isset($_REQUEST['ppp']) ? (int)$_REQUEST['ppp'] : 50;
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
$orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '';
$customnc = isset($_REQUEST['customnc']) ? $_REQUEST['customnc'] : '';
$displayn = isset($_REQUEST['displayn']) ? $_REQUEST['displayn'] : '';

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
if (!$config['perusercolor'])
	$customnc = '0';
if ($customnc == '1')
	$where.=" AND `nick_color` !='' AND `enablecolor` > 0";

if (!$config['displayname'])
	$displayn = '0';
if ($displayn == '1')
	$where.=" AND `displayname` !=''";

$users = $sql->query("SELECT * FROM users "
		. "WHERE $where "
		. "ORDER BY $order "
		. "LIMIT " . ($page - 1) * $ppp . ",$ppp");
$num = $sql->resultq("SELECT COUNT(*) FROM users "
		. "WHERE $where");

if ($num <= $ppp)
	$pagelist = '';
else {
	$pagelist = 'Pages:';
	for ($p = 1; $p <= 1 + floor(($num - 1) / $ppp); $p++)
		if ($p == $page)
			$pagelist.=" $p";
		else
			$pagelist.=' ' . mlink($sort, $sex, $pow, $ppp, $p, $orderby, $customnc, $displayn) . "$p</a>";
}

$activegroups = $sql->query("SELECT * FROM `group` WHERE id IN (SELECT `group_id` FROM users GROUP BY `group_id`) ORDER BY `sortorder` ASC ");

$groups = array();
$gc = 0;
$unclass = '';
if ($config['useshadownccss'])
	$unclass = "class='needsshadow'";
while ($group = $sql->fetch($activegroups)) {
	if ($sex == 'f') $sexcolor = $group['nc1'];
	elseif ($sex == 'n') $sexcolor = $group['nc2'];
	else $sexcolor = $group['nc0'];
	$grouptitle = "<span $unclass style=\"color:#" . $sexcolor . ";\">" . $group['title'] . "</span>";
	$groups[$gc++] = mlink($sort, $sex, $group['id'], $ppp, $page, $orderby, $customnc, $displayn) . $grouptitle . "</a>";
}

?>
<table class="c1">
	<tr class="h"><td class="b h" colspan="2"><?php echo $num . ' user' . ($num > 1 ? 's' : ''); ?> found.</td></tr>
	<tr>
		<td class="b n1" width="105">Sort by:</td>
		<td class="b n2" align="center">
			<?php echo mlink('', $sex, $pow, $ppp, $page, $orderby, $customnc, $displayn); ?> Posts</a> |
			<?php echo mlink('name', $sex, $pow, $ppp, $page, $orderby, $customnc, $displayn); ?> Username</a> |
			<?php echo mlink('reg', $sex, $pow, $ppp, $page, $orderby, $customnc, $displayn); ?> Registration date</a>
		</td>
	</tr><tr>
		<td class="b n1">Order by:</td>
		<td class="b n2" align="center">
			<?php echo mlink($sort, $sex, $pow, $ppp, $page, 'd', $customnc, $displayn) . "Descending</a> |"; ?>
			<?php echo mlink($sort, $sex, $pow, $ppp, $page, 'a', $customnc, $displayn) . "Ascending</a>"; ?>
		</td>
	</tr><tr>
		<td class="b n1">Sex:</td>
		<td class="b n2" align="center">
			<?php echo mlink($sort, 'm', $pow, $ppp, $page, $orderby, $customnc, $displayn) . "Male</a> | " .
			mlink($sort, 'f', $pow, $ppp, $page, $orderby, $customnc, $displayn) . "Female</a> | " .
			mlink($sort, 'n', $pow, $ppp, $page, $orderby, $customnc, $displayn) . "N/A</a> | ";

			if ($config['perusercolor']) {
				if ($customnc == '1')
					echo mlink($sort, $sex, $pow, $ppp, $page, $orderby, '0', $displayn) . "Regular</a> |";
				else
					echo  mlink($sort, $sex, $pow, $ppp, $page, $orderby, '1', $displayn) . "Custom</a> |";
			}

			echo mlink($sort, '', $pow, $ppp, $page, $orderby, $customnc, $displayn) . "All</a>"; ?>
	<tr>
		<td class="b n1">Group:</td>
		<td class="b n2" align="center">
			<?php $c = 0;
			foreach ($groups as $k => $v) {
			$c++;
				echo $v . " | ";
			}
			echo mlink($sort, $sex, '-1', $ppp, $page, $orderby, $customnc, $displayn) . "All Staff</a> | " .
			mlink($sort, $sex, '', $ppp, $page, $orderby, $customnc, $displayn) . "All</a>"; ?>
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
		"reg" => cdate($dateformat, $user['regdate']),
		"posts" => $user['posts'],
	);
}

RenderTable($data, $headers);

echo '<br>'.$pagelist.'<br>';
pagefooter();

function mlink($sort, $sex, $pow, $ppp, $page = 1, $orderby, $customnc, $displayn) {
	return '<a href=memberlist.php?'
			. ($sort ? "sort=$sort" : '')
			. ($sex ? "&sex=$sex" : '')
			. ($pow != '' ? "&pow=$pow" : '')
			. ($ppp != 50 ? "&ppp=$ppp" : '')
			. ($page != 1 ? "&page=$page" : '')
			. ($orderby != '' ? "&orderby=$orderby" : '')
			. ($customnc != '' ? "&customnc=$customnc" : '')
			. ($displayn != '' ? "&displayn=$displayn" : '')
			. '>';
}

?>
