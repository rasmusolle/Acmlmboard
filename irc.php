<?php
require 'lib/common.php';
pageheader();

$channel = "#changeme";
$network = "irc.changeme.invalid";

?>
<table cellspacing="0" class="c1">
	<tr class="h">
		<td class="b h">IRC</td>
	<tr>
		<td class="b n2" align="center">
			Please download an IRC client to join.<br>
			<a href="https://hexchat.github.io">How about HexChat?</a><br>
			When you've downloaded it, please connect to <?php print $channel; ?> at <?php echo $network; ?>
		</td>
	</tr>
</table>
<br>
<table cellspacing="0" class="c1">
	<tr class="h">
		<td class="b h">Quick Help - Commands</td>
	<tr>
		<td class="b n1">		
			<kbd>/nick [name]</kbd> - changes your name
			<br><kbd>/me [action]</kbd> - does an action (try it)
			<br><kbd>/msg [name] [message]</kbd> - send a private message to another user
			<br><kbd>/join [#channel]</kbd> - joins a channel
			<br><kbd>/part [#channel]</kbd> - leaves a channel
			<br><kbd>/quit [message]</kbd> - disconnects from the server
		</td>
	</tr>     
</table>
<?php pagefooter(); ?>