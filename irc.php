<?php
require 'lib/common.php';
pageheader();
?>
<table class="c1">
	<tr class="h">
		<td class="b h">IRC</td>
	<tr>
		<td class="b n2" align="center">
			Please download an IRC client to join.<br>
			<a href="https://hexchat.github.io">How about HexChat?</a><br>
			When you've downloaded it, connect to <?php echo $config['channel']; ?> at <?php echo $config['network']; ?>
		</td>
	</tr>
</table>
<?php pagefooter(); ?>
