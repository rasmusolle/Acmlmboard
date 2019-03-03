<?php
require('lib/common.php');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$attndata = '';

if (!has_perm('edit-attentions-box')) {
	pageheader('Nothing here.');
	noticemsg("Error", "You have no permissions to do this!<br> <a href=./>Back to main</a>");
} else {
	if ($action == "Submit") {
		$sql->query("UPDATE misc SET txtval='" . $_POST['txtval'] . "' WHERE field='attention'");
	}

	$attndata = $sql->resultq("SELECT txtval FROM misc WHERE field='attention'");

	$pageheadtxt = "Edit news";
	pageheader($pageheadtxt);
	?>
	<form action="editattn.php" method="post">
		<table class="c1">
			<tr class="h"><td class="b h">Edit news box</td></tr>
			<tr class="n1">
				<td class="b center">
					<textarea name="txtval" rows="8" cols="120"><?=$attndata ?></textarea>
					<br><input type="submit" class="submit" name="action" value="Submit">
				</td>
			</tr>
		</table>
	</form>
	<?php
}
pagefooter();
?>