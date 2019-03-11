<?php
require('lib/common.php');

if (!has_perm('edit-attentions-box')) {
	error('Error', 'You have no permissions to do this!<br> <a href=./>Back to main</a>');
}

if (isset($_POST['action']) && $_POST['action'] == 'Submit') {
	$sql->prepare("UPDATE misc SET txtval=? WHERE field='attention'",array($_POST['txtval']));
}

$attndata = $sql->resultq("SELECT txtval FROM misc WHERE field='attention'");

pageheader("Edit news");
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
<?php pagefooter();