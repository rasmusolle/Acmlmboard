<?php
require('lib/common.php');
require('lib/threadpost.php');

if (isset($_REQUEST['announce'])) { $announce = $_REQUEST['announce']; }
checknumeric($announce);

if (!isset($_POST['action'])) { $_POST['action'] = ''; }
if ($act = $_POST['action']) {
	$fid = $_POST['fid'];
	$userid = $loguser['id'];
	$user = $loguser;
} else {
	$user = $loguser;
	$fid = $_GET['id'];
}
checknumeric($fid);

if ($announce) {
	$type = "announcement";
	$typecap = "Announcement";
	$ispoll = 0;
} elseif (isset($_GET['ispoll']) && $_GET['ispoll'] == 1) {
	$type = "poll";
	$typecap = "Poll";
	$ispoll = 1;
} else {
	$type = "thread";
	$typecap = "Thread";
	$ispoll = 0;
}

if ($announce && $fid == 0)
	$forum = array(
		'id' => 0,
		'readonly' => 1
	);
else
	$forum = $sql->fetchq("SELECT * FROM forums WHERE id=$fid AND id IN " . forums_with_view_perm());

if ($act != "Submit") {
	$toolbar = posttoolbar();

	if ($ispoll) {
		$optfield = '<div><input type="text" name="opt[]" size=40 maxlength=40 value="%s"> - Color: <input class="jscolor" name="col[]" value="%02X%02X%02X"> - <button class="submit" onclick="removeOption(this.parentNode);return false;">Remove</button></div>';
	}
}

$forumlink = "<a href=forum.php?id=$fid>Back to forum</a>";

if (!$forum) {
	error("Error", "Forum does not exist. <br> <a href=./>Back to main</a>");
}
else if ($announce && !has_perm('create-forum-announcements'))
	$err = "You have no permissions to create announcements in this forum!<br>$forumLink";

else if (!can_create_forum_thread($forum)) {

	$err = "    You have no permissions to create threads in this forum!<br>$forumlink";
}
else if ($user['lastpost'] > ctime() - 30 && $act == 'Submit' && !has_perm('ignore-thread-time-limit'))
	$err = "    Don't post threads so fast, wait a little longer.<br>
" . "    $forumlink";

else if ($user['lastpost'] > ctime() - $config['secafterpost'] && $act == 'Submit' && has_perm('ignore-thread-time-limit'))
	$err = "    You must wait ".$config['secafterpost']." seconds before posting a thread.<br>
" . "    $forumlink";

// 2007-02-19 //blackhole89 - table breach protection
if ($act == 'Submit') {
	$title = $_POST['title'];
	$message = $sql->escape($_POST['message']);
	if (($tdepth = tvalidate($message)) != 0)
		$err = "    This post would disrupt the board's table layout! The calculated table depth is $tdepth.<br>
" . "    $forumlink";
	if (strlen(trim(str_replace(" ", "", $title))) < 4)
		$err = "    You need to enter a longer $type title.<br>
" . "    $forumlink";
	if ($ispoll && (! isset($_POST['opt']) || count($_POST['opt']) < 2))
		$err = "    You must add atleast two choices to your poll.<br>
" . "    $forumlink";
	else if ($ispoll) {
		foreach ($_POST['opt'] as $id => $text)
			if (trim($text) == '' || $_POST['col'][$id] == '')
				$err = "You must fill in all poll choices' fields.<br>
" . "$forumlink";
	}
}

$top = "<a href=./>Main</a> - <a href=forum.php?id=$fid>$forum[title]</a> - New $type";

if (isset($err)) {
	pageheader("New $type", $forum['id']);
	echo "$top - Error";
	noticemsg("Error", $err);
} elseif (!$act) {
	pageheader("New $type", $forum['id']);
	echo $top;
	?>
	<br><br>
	<form action="newthread.php<?=($ispoll ? "?ispoll=$ispoll" : "") ?>" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan="2"><?php echo $typecap; ?></td>
			</tr>
			<tr>
				<td class="b n1 center"><?php echo $typecap; ?> title:</td>
				<td class="b n2"><input type="text" name=title size=100 maxlength=100></td>
			</tr>
			<?php
			if ($ispoll) {
				?>
				<tr>
					<td class="b n1 center">Poll question:</td>
					<td class="b n2"><input type="text" name="question" size="100" maxlength="100" value="<?php echo htmlval(isset($_POST['question']) ? $_POST['question'] : ''); ?>"></td>
				</tr>
				<tr>
					<td class="b n1 center">Poll choices:</td>
					<td class="b n2">
						<div id="polloptions">
							<?php echo sprintf($optfield, '', rand(0, 255), rand(0, 255), rand(0, 255)); ?>
							<?php echo sprintf($optfield, '', rand(0, 255), rand(0, 255), rand(0, 255)); ?>
						</div>
						<button type="button" class="submit" id="addopt" onclick="addOption();return false;">Add choice</button>
					</td>
				</tr>
				<tr>
					<td class="b n1 center">Options:</td>
					<td class="b n2">
						<input type="checkbox" name="multivote" value="1" id="mv">
						<label for="mv">Allow multiple voting</label> |
						<input type="checkbox" name="changeable" checked value="1" id="ch">
						<label for="ch">Allow changing one's vote</label>
					</td>
				</tr>
				<script type="text/javascript" src="lib/js/jscolor.js"></script>
				<script type="text/javascript" src="lib/js/polleditor.js"></script>
			<?php } ?>
			<tr>
				<td class="b n1 center" width=120>Format:</td>
				<td class="b n2"><table><tr><?php echo $toolbar; ?></tr></table></td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Post:</td>
				<td class="b n2">
					<textarea name=message id='message' rows=20 cols=80></textarea>
				</td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b">
					<input type="hidden" name="fid" value="<?php echo $fid; ?>">
					<input type="hidden" name="announce" value="<?php echo $announce; ?>">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
					<input type="checkbox" name=nolayout id=nolayout value=1 <?php echo (isset($_POST['nolayout']) ? "checked" : ""); ?>><label for=nolayout>Disable post layout</label>
				</td>
			</tr>
		</table>
	</form>
	<script language="javascript" type="text/javascript" src="lib/js/tools.js"></script>
	<?php
} elseif ($act == 'Preview') {
	$_POST['title'] = stripslashes($_POST['title']);
	$_POST['message'] = stripslashes($_POST['message']);

	$post['date'] = ctime();
	$post['ip'] = $userip;
	$post['num'] = ++ $user['posts'];
	$post['text'] = $_POST['message'];
	$post['nolayout'] = isset($_POST['nolayout']);
	foreach ($user as $field => $val)
		$post['u' . $field] = $val;
	$post['ulastpost'] = ctime();

	if ($ispoll) {
		$_POST['question'] = stripslashes($_POST['question']);
		$numopts = $_POST['numopts'];
		checknumeric($numopts);
		$pollprev = "<br><table class=\"c1\">
" . "  <tr class=\"n1\">
" . "    <td class=\"b n1\" colspan=2>" . htmlval($_POST['question']) . "
";
		$pollin = "<tr>
" . "  <td class=\"b n1 center\">Poll question:</td>
" . "  <td class=\"b n2\"><input type=\"text\" name=question size=100 maxlength=100 value=\"" . htmlval($_POST[question]) . "\"></td>
" . "<tr>
" . "  <td class=\"b n1 center\">Poll choices:</td>
" . "  <td class=\"b n2\"><div id=\"polloptions\">
";

		if (isset($_POST['opt'])) {
			foreach ($_POST['opt'] as $id => $text) {
				$text = htmlval(stripslashes($text));

				$color = stripslashes($_POST['col'][$id]);
				list ($r, $g, $b) = sscanf(strtolower($color), '%02x%02x%02x');
				
				$colori = str_pad(dechex($r), 2, "0", STR_PAD_LEFT) . str_pad(dechex($g), 2, "0", STR_PAD_LEFT) . str_pad(dechex($b), 2, "0", STR_PAD_LEFT);

				$pollin .= "    " . sprintf($optfield, $text, $r, $g, $b) . "\n";
				$pollprev .= "<tr class=\"n2\"><td class=\"b n2\" width=200>{$text} $h<td class=\"b n3\"><div style=\"width:100%;background-color:#$colori;\"><span style=\"padding-right:10em;\"></span></div>";
			}
		}

		$pollin .= "  </div>
" . "  <button type=\"button\" class=\"submit\" id=addopt onclick=\"addOption();return false;\">Add choice</button></td>
" . "<tr>
" . "  <td class=\"b n1 center\">Options:</td>
" . "  <td class=\"b n2\"><input type=\"checkbox\" name=multivote " . ($_POST[multivote] ? "checked" : "") . " value=1 id=mv><label for=mv>Allow multiple voting</label> | <input type=\"checkbox\" name=changeable " . ($_POST[changeable] ? "checked" : "") . " value=1 id=ch><label for=ch>Allow changing one's vote</label>
";
		$pollprev .= "</table>";
	}

	pageheader("New $type", $forum['id']);
	echo "$top - Preview " . (isset($pollprev) ? $pollprev : '');
	?><br>
	<table class="c1"><tr class="h"><td class="b h" colspan=2>Post preview</td></tr>
	<?php echo threadpost($post); ?>
	<br>
	<form action="newthread.php?ispoll=<?php echo $ispoll; ?>" method="post">
		<table class="c1">
			<tr class="h">
				<td class="b h" colspan=2><?php echo $typecap; ?></td>
			</tr>
			<tr>
				<td class="b n1 center"><?php echo $typecap; ?> title:</td>
				<td class="b n2"><input type="text" name=title size=100 maxlength=100 value="<?=htmlval($_POST['title']) ?>"></td>
			</tr>
			<?php echo (isset($pollin) ? $pollin : ''); ?>
			<tr>
				<td class="b n1 center" width=120>Format:</td>
				<td class="b n2"><table><tr><?php echo $toolbar; ?></tr></table></td>
			</tr>
			<tr>
				<td class="b n1 center" width=120>Post:</td>
				<td class="b n2">
					<textarea name=message id='message' rows=20 cols=80><?=htmlval($_POST['message']) ?></textarea>
				</td>
			</tr>
			<tr class="n1">
				<td class="b">&nbsp;</td>
				<td class="b">
					<input type="hidden" name=fid value=<?php echo $fid; ?>>
					<input type="hidden" name="announce" value="<?php echo $announce; ?>">
					<input type="submit" class="submit" name="action" value="Submit">
					<input type="submit" class="submit" name="action" value="Preview">
					<input type="checkbox" name=nolayout id=nolayout value=1 <?php echo ($post['nolayout'] ? "checked" : ""); ?>><label for=nolayout>Disable post layout</label>
				</td>
			</tr>
		</table>
	</form><?php
} elseif ($act == 'Submit') {
	checknumeric($_POST['nolayout']);

	$modclose = "0";
	$modstick = "0";

	$user = $sql->fetchq("SELECT * FROM users WHERE id=$userid");
	$user['posts']++;

	if ($announce) {
		$modclose = $announce;
	}

	$sql->query("UPDATE users SET posts=posts+1,threads=threads+1,lastpost=" . ctime() . " " . "WHERE id=$userid");
	$sql->query("INSERT INTO threads (title,forum,user,lastdate,lastuser,announce,closed,sticky) " . "VALUES ('$_POST[title]',$fid,$userid," . ctime() . ",$userid,$announce,$modclose,$modstick)");
	$tid = $sql->insertid();
	$sql->query("INSERT INTO posts (user,thread,date,ip,num,nolayout,announce) " . "VALUES ($userid,$tid," . ctime() . ",'$userip',$user[posts],'$_POST[nolayout]',$announce)");
	$pid = $sql->insertid();
	$sql->query("INSERT INTO poststext (id,text) VALUES ($pid,'$message')");
	if (!$announce) {
		$sql->query("UPDATE forums SET threads=threads+1,posts=posts+1,lastdate=" . ctime() . ",lastuser=$userid,lastid=$pid " . "WHERE id=$fid");
	}
	$sql->query("UPDATE threads SET lastid=$pid WHERE id=$tid");

	if ($ispoll) {
		$sql->query("INSERT INTO polls (id,question,multivote,changeable) VALUES ($tid,'{$_POST['question']}','{$_POST['multivote']}','{$_POST['changeable']}')");

		foreach ($_POST['opt'] as $id => $_text) {
			$color = stripslashes($_POST['col'][$id]);
			list ($r, $g, $b) = sscanf(strtolower($color), '%02x%02x%02x');
			$text = $sql->escape($_text);

			$sql->query("INSERT INTO polloptions (`poll`,`option`,r,g,b) VALUES ($tid,'{$text}'," . (int) $r . "," . (int) $g . "," . (int) $b . ")");
		}
	}

	if ($announce) {
		$viewlink = "thread.php?announce";
		$shortlink = "a=" . $forum['id'];
	} else {
		$viewlink = "thread.php?id=$tid";
		$shortlink = "t=$tid";
	}

	redirect($viewlink, $c);
}

pagefooter();
?>
