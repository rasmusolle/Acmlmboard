<?php
require('lib/common.php');

needs_login(1);

$page = (isset($_GET['page']) ? $_GET['page'] : null);
if (!$page) $page = 1;

if (!isset($_GET['view'])) $_GET['view'] = 'read';
if (!isset($_GET['action'])) $_GET['action'] = 'read';

if ($_GET['view'] == 'sent') {
	$tablehead = 'To';
	$fieldn = 'to';
	$fieldn2 = 'from';
	$sent = true;
} else {
	$tablehead = 'From';
	$fieldn = 'from';
	$fieldn2 = 'to';
	$sent = false;
}

if (has_perm('view-user-pms')) $id = (isset($_GET['id']) ? $_GET['id'] : 0);
else $id = 0;
checknumeric($id);

if (!has_perm('view-own-pms') && $id == 0) {
	error("Error", "You are not allowed to do this!<br> <a href=./>Back to main</a>");
}

$showdel = isset($_GET['showdel']);
checknumeric($showdel);

if ($_GET['action'] == "del") {
	$owner = $sql->resultq("SELECT user$fieldn2 FROM pmsgs WHERE id=$id");
	if (has_perm('delete-user-pms') || ($owner == $loguser['id'] && has_perm('delete-own-pms'))) {
		$sql->query($q = "UPDATE pmsgs SET del_$fieldn2=" . ((int) ! $showdel) . " WHERE id=$id");
	} else {
		error("Error", "You are not allowed to (un)delete that message.<br> <a href=./>Back to main</a>");
	}
	$id = 0;
}

$ptitle = 'Private messages' . ($sent ? ' (sent)' : '');
if ($id && has_perm('view-user-pms')) {
	$user = $sql->fetchq("SELECT id,name,sex,group_id FROM users WHERE id=$id");
	pageheader("$user[name]'s " . strtolower($ptitle));
	$title = userlink($user) . "'s " . strtolower($ptitle);
} else {
	$id = $loguser['id'];
	pageheader($ptitle);
	$title = $ptitle;
}

$pmsgc = $sql->resultq("SELECT COUNT(*) FROM pmsgs WHERE user$fieldn2=$id AND del_$fieldn2=$showdel");
$pmsgs = $sql->query("SELECT " . userfields('u', 'u') . ", p.* "
					."FROM pmsgs p "
					."LEFT JOIN users u ON u.id=p.user$fieldn "
					."WHERE p.user$fieldn2=$id "
				."AND del_$fieldn2=$showdel "
					."ORDER BY p.unread DESC, p.date DESC "
					."LIMIT " . (($page - 1) * $loguser['tpp']) . ", " . $loguser['tpp']);

if ($sent)
	$link = '?' . ($id != $loguser['id'] ? "id=$id&" : '') . ">View received";
else
	$link = '?' . ($id != $loguser['id'] ? "id=$id&" : '') . "view=sent>View sent";

$topbot = "<table width=100%>
" . "  <td class=\"nb\"><a href=./>Main</a> - $title</td>
" . "  <td class=\"nb right\"><a href=private.php$link</a> | <a href=sendprivate.php>Send new</a></td>
" . "</table>
";

if ($pmsgc <= $loguser['tpp'])
	$fpagelist = '<br>';
else {
	if ($sent)
		$txt = "&view=sent";
	else
		$txt = '';
	$fpagelist = '<div style="margin-left: 3px; margin-top: 3px; margin-bottom: 3px; display:inline-block">Pages:';
	for ($p = 1; $p <= 1 + floor(($pmsgc - 1) / $loguser['tpp']); $p++)
		if ($p == $page)
			$fpagelist .= " $p";
		elseif ($id != $loguser['id'])
			$fpagelist .= " <a href=private.php?id=$id&page=$p$txt>$p</a>";
		else
			$fpagelist .= " <a href=private.php?page=$p$txt>$p</a>";
	$fpagelist .= '</div>';
}

echo $topbot.'<br>';
?>
<table class="c1">
	<tr class="h">
		<td class="b h" width="17">&nbsp;</td>
		<td class="b h" width="17">&nbsp;</td>
		<td class="b h">Title</td>
		<td class="b h" width="130"><?=$tablehead ?></td>
		<td class="b h" width="130">Sent on</td>
	</tr>
	<?php
	for ($i = 1; $pmsg = $sql->fetch($pmsgs); $i++) {
		$status = '&nbsp;';
		if ($pmsg['unread'])
			$status = rendernewstatus("n");
		if (! $pmsg['title'])
			$pmsg['title'] = '(untitled)';

		$tr = ($i % 2 ? 'n2' : 'n3');
		?>
		<tr class="<?=$tr ?> center">
			<td class="b n2">
				<a href="private.php?action=del&id=<?=$pmsg['id'] ?>&view=<?=$_GET['view'] ?>"><img src="img/smilies/no.png" align=absmiddle></a>
			</td>
			<td class="b n1"><?=$status ?></td>
			<td class="b left"><a href=showprivate.php?id=<?=$pmsg['id'] ?>><?=forcewrap(htmlval($pmsg['title'])) ?></a></td>
			<td class="b"><?=userlink($pmsg, 'u') ?></td>
			<td class="b"><nobr><?=date($dateformat, $pmsg['date']) ?></nobr></td>
		</tr>
		<?php
	}
	?>
</table>
<?php
echo "$fpagelist $topbot";
pagefooter();