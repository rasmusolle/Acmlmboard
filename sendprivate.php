<?php
require 'lib/common.php';
require 'lib/threadpost.php';
loadsmilies();

needs_login(1);

if (!isset($act)) $act = 'needle';
if (!isset($_POST['action'])) $_POST['action'] = null;
if (!isset($_GET['pid'])) $_GET['pid'] = -1;
if (!isset($_GET['uid'])) $_GET['uid'] = -1;

if ($act != 'Submit') {
	echo '<script language="javascript" type="text/javascript" src="tools.js"></script>';
}

$top = '<a href=./>Main</a> - <a href=private.php>Private messages</a> - Send';

$toolbar = posttoolbar();

if (!has_perm('create-pms'))
	error("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");

if (!$act = $_POST['action']) {
	if ($pid = $_GET['pid']) {
		checknumeric($pid);
		$post = $sql->fetchq("SELECT IF(u.displayname='',u.name,u.displayname) name, p.title, pt.text "
			."FROM pmsgs p "
			."LEFT JOIN pmsgstext pt ON p.id=pt.id "
			."LEFT JOIN users u ON p.userfrom=u.id "
			."WHERE p.id=$pid" . (!has_perm('view-user-pms') ? " AND (p.userfrom=".$loguser['id']." OR p.userto=".$loguser['id'].")" : ''));
		if ($post) {
			$quotetext = '[reply="'.$post['name'].'" id="'.$pid.'"]'.$post['text'].'[/quote]\n';
			$title = 'Re:' . $post['title'];
			$userto = $post['name'];
		}
	}
	
	if ($uid = $_GET['uid']) {
		checknumeric($uid);
		$userto = $sql->resultq("SELECT IF(displayname='',name,displayname) name FROM users WHERE id=$uid");
	} elseif (!isset($userto))
		$userto = $_POST['userto'];
	
	pageheader('Send private message');
	echo $top;
	?>
	<br><br>
	<form action="sendprivate.php" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan="2">Send message</td>
			</tr>
			<tr>
				<td class="b n1" align="center" width="120">Send to:</td>
				<td class="b n2"><input type="text" name="userto" size="25" maxlength=25 value="<?php echo htmlval($userto); ?>"></td>
			</tr>
			<tr>
				<td class="b n1" align="center">Title:</td>
				<td class="b n2"><input type="text" name="title" size="80" maxlength="255" value="<?php echo htmlval((isset($title) ? $title : '')); ?>"></td>
			</tr>
			<tr>
				<td class="b n1" align="center" width="120">Format:</td>
				<td class="b n2"><table><tr><?php echo $toolbar; ?></tr></table></td>
			</tr>
			<tr>
				<td class="b n1" align="center">&nbsp;</td>
				<td class="b n2"><textarea name="message" id="message" rows="20" cols="80"><?php echo htmlval((isset($quotetext) ? $quotetext : '')); ?></textarea></td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
					<select name="mid"><?php echo moodlist() ?></select>
					<input type="checkbox" name="nolayout" id="nolayout" value="1" <?php echo (isset($_POST['nolayout']) ? "checked" : "") ?>>Disable post layout
					<input type="checkbox" name="nosmilies" id="nosmilies" value="1" <?php echo (isset($_POST['nosmilies']) ? "checked" : "") ?>>Disable smilies
				</td>
			</tr>
		</table>
	</form>
	<?php
} elseif ($act == 'Preview') {
	$_POST['title'] = stripslashes($_POST['title']);
	$_POST['message'] = stripslashes($_POST['message']);
	
	$post['date'] = ctime();
	$post['ip'] = $userip;
	$post['num'] = 0;
	$post['text'] = $_POST['message'];
	$post['mood'] = (isset($_POST['mid']) ? (int) $_POST['mid'] : - 1);
	$post['nolayout'] = $_POST['nolayout'];
	$post['nosmilies'] = $_POST['nosmilies'];
	foreach ($loguser as $field => $val)
		$post['u' . $field] = $val;
	$post['ulastpost'] = ctime();
	
	
	pageheader('Send private message');
	?>
	$top - Preview
<br><br>
	<table class="c1">
  		<tr class="h">
    		<td class="b h" colspan=2>Message preview</td>
    	</tr>
	</table>
	<?php echo threadpost($post, 0); ?>
	<br>
	<form action="sendprivate.php" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan="2">Send message</td>
			</tr>
			<tr>
				<td class="b n1" align="center" width="120">Send to:</td>
				<td class="b n2"><input type="text" name="userto" size=25 maxlength=25 value="<?php echo htmlval((isset($_POST['userto']) ? $_POST['userto'] : '')); ?>"></td>
			</tr>
			<tr>
				<td class="b n1" align="center">Title:</td>
				<td class="b n2"><input type="text" name="title" size="80" maxlength="255" value="<?php echo htmlval($_POST['title']); ?>"></td>
			</tr>
			<tr>
				<td class="b n1" align="center" width="120">Format:</td>
				<td class="b n2"><table><tr><?php echo $toolbar; ?></tr></table></td>
			</tr>
			<tr>
				<td class="b n1" align="center">&nbsp;</td>
				<td class="b n2"><textarea name="message" id="message" rows="20" cols="80"><?php echo htmlval($_POST['message']); ?></textarea></td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
					<select name="mid"><?php echo moodlist($post['mood']) ?></select>
					<input type="checkbox" name="nolayout" id="nolayout" value="1" <?php echo (isset($_POST['nolayout']) ? "checked" : "") ?> >Disable post layout
					<input type="checkbox" name="nosmilies" id="nosmilies" value="1" <?php echo (isset($_POST['nosmilies']) ? "checked" : "") ?>>Disable smilies
				</td>
			</tr>
		</table>
	</form>
	<?php
} elseif ($act == 'Submit') {
	$userto = $sql->resultq("SELECT id FROM users WHERE name LIKE '".$_POST['userto']."' OR displayname LIKE '".$_POST['userto']."'");
	
	if ($userto && $_POST['message']) {
		// [blackhole89] 2007-07-26
		$recentpms = $sql->query("SELECT date FROM pmsgs WHERE date>=(UNIX_TIMESTAMP()-30) AND userfrom='$loguser[id]'");
		$secafterpm = $sql->query("SELECT date FROM pmsgs WHERE date>=(UNIX_TIMESTAMP()-$config[secafterpost]) AND userfrom='$loguser[id]'");
		if (($sql->numrows($recentpms) > 0) && (!has_perm('consecutive-posts'))) {
			$msg = "You can't send more than one PM within 30 seconds!";
		} else if (($sql->numrows($secafterpm) > 0) && (has_perm('consecutive-posts'))) {
			$msg = "You can't send more than one PM within ".$config['secafterpost']." seconds!";
		} else {
			checknumeric($_POST['nolayout']);
			checknumeric($_POST['nosmilies']);
			checknumeric($_POST['mid']);
			$sql->query("INSERT INTO pmsgs (date,ip,userto,userfrom,unread,title,mood,nolayout,nosmilies) "
				."VALUES ('" . ctime() . "','$userip','$userto','" . $loguser['id'] . "',1,'" . $_POST['title'] . "'," . $_POST['mid'] . ",$_POST[nolayout],$_POST[nosmilies])");
			$pid = $sql->insertid();
			$sql->query("INSERT INTO pmsgstext (id,text) VALUES ($pid,'$_POST[message]')");
			
			redirect("private.php", - 1);
		}
	} elseif (!$userto) {
		$msg = "That user doesn't exist!<br>Go back or <a href=sendprivate.php>try again</a>";
	} elseif (!$_POST['message']) {
		$msg = "You can't send a blank message!<br>Go back or <a href=sendprivate.php>try again</a>";
	}
	
	pageheader('Send private message');
	echo "$top - Error";
	noticemsg("Error", $msg);
}

pagefooter();
?>
