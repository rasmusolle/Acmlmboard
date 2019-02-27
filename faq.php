<?php
require('lib/common.php');

//Smilies List
$smiliewidth = ceil(sqrt(sizeof($smilies)));
$smilietext = "<table>";

$x = 0;
foreach ($smilies as $smily) {
	if ($x == 0) $smilietext .= "<tr>";
	$smilietext .= "<td width='35'>$smily[text]</td><td width='27'><img src='$smily[url]'/></td><td width='5'></td>";
	$x++;
	$x %= $smiliewidth;
	if ($x == 0) $smilietext .= "</tr>";
}
$smilietext .= "</table>";

pageheader("FAQ");

if (isset($_GET['testshit'])) {
	echo $sqluser . ' ' . $sqlpass . ' ' . $sqldb;
}

$ncx = $sql->query("SELECT title, nc FROM `group` WHERE nc != '' ORDER BY sortorder ASC");
$nctable = "";

while ($ncr = $sql->fetch($ncx)) {
	$nctable .= "<tr>";
	
	$nc = $ncr["nc"];
	$nctable .= "<td class='b n1' width='200'><b><font color='#$nc'>".$ncr['title']."</font></b></td>";

	$nctable .= "</tr>";
}

?>
<style>
p {
	margin-top: 0.5em;
}
.title {
	font-weight: bold;
	text-decoration: underline;
	margin-bottom: 0em;
}
</style>
<table class="c1">
	<tr class="h"><td class="b h">FAQ</td></tr>
	<tr>
		<td class="b n1" style="padding: 10px !important;">
			<a href="#gpg">General Posting Guidelines</a><br>
			<a href="#move">I just made a thread, where did it go?</a><br>
			<a href="#rude">I feel that a user is being rude to me. What do I do?</a><br>
			<a href="#smile">Are smilies and BBCode supported?</a><br>
			<?php if ($config['hasirc']) echo '<a href="#irc">What\'s this IRC thing I keep hearing about?</a><br>' ?>
			<a href="#reg">Can I register more than one account?</a><br>
			<a href="#css">What are we not allowed to do in our custom CSS layouts?</a><br>
			<a href="#private">Are private messages supported?</a><br>
			<a href="#usercols">What do the username colours mean?</a><br>
		</td>
	</tr>
</table>
<br>
<table class="c1">
	<tr class="h"><td class="b h" id="gpg">General Posting Guidelines:</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>Posting on a message forum is generally relaxed. There are, however, a few things to keep in mind when posting.</p>
			<ul style="list-style-type: decimal;">
				<li>One word posts. These types of posts generally do not add to the conversation topic and should be avoided at all cost. Come on, at least form a complete sentence!
				<li>Trolling/flaming/drama. This behavior is totally unacceptable and will be dealt with accordingly, namely with a warning. Direct (or even indirect) personal attacks on <strong style="text-decoration:underline"><em>any</em></strong> member of this community for any reason whatsoever will result in immediate action. Do NOT test us on this.
				<li>Reviving, or "bumping" old threads. If the last post in a thread was a month ago or more, we ask that you do not add another post unless you have something very relevant and interesting to add to the topic.
				<li>Spamming. Spam is a pretty broad and grey area. Spam can be generalized as multiple posts with no real meaning to the topic or what anyone else is talking about.
				<li>Staff impersonation and "back seat moderation." Staff impersonation will <b>not</b> be tolerated. Doing so will may result in an instant ban. While you may feel you are helping by telling a fellow member that they need to stop doing something you know is wrong, you may do more harm than good. If you see an issue please report the issue to the staff immediately.
				<li>Suggestive Material. Remember that there are others here who enjoy the board experience. Their standards are not necessarily going to be like yours all the time, so please, do not post anything pornographic or otherwise potentially disturbing to other members.
			</ul>
			<p class="title">Procedural</p>
			<p>Acmlmboard follows the "Three Strike Rule". This means if you have been warned twice by staff for whatever reason, your third notice will be a ban and a reason, coupled with a ban length. Each time you are given a "strike", you will receive a PM from a staff member stating so. This PM will also include a link to the post in question and a reason for the warning. Your third strike will come with a ban. Ban lengths are as follows:</p>
			<table>
				<tr><td>Offence</td><td>Duration</td></tr>
				<tr><td>1st</td><td>1 Week</td></tr>
				<tr><td>2nd</td><td>2 Weeks</td></tr>
				<tr><td>3rd</td><td>1 Month</td></tr>
				<tr><td>4th</td><td>2 Months</td></tr>
				<tr><td>5th</td><td>Indefinite</td></tr>
			</table>
			<p>Please note that these ban lengths are "soft" and may be changed and/or deviated from by staff at their discretion. Decisions made regarding length will not be negotiable. If you have been banned but not warned, let a member of staff know.</p>
			<p class="title">Behavioral</p>
			<p>Following one rule doesn't mean your post is automatically acceptable. If it is distasteful, repugnant, or offensive, then don't post it.</p>
			<p>If your post is seen by staff to incite drama, put down others, have negative connotations/bad attitude, or otherwise find fault therein, they have absolute right in deciding what to do with it and with you.</p>
			<p>IRC is IRC, and the board is the board, and there's a distinct level of separation between the two. However, we acknowledge that they are closely related and will make decisions based on your actions from both mediums of this community. This means if you're prone to being rude on IRC, and then rude on the board, it will most likely be considered when determining disciplinary action.</p>
			<p class="title">Code-side</p>
			<p>The use of CSS usage to change your username colour, impersonate being staff, or similar is forbidden. Any alteration to one's username (font, icon etc) fake custom titles, and other additional text in a non-post field are under discretion of the the staff. Likewise, use of CSS that changes the board layout, others' posts or anything outside of your own post is forbidden.</p>
			<p class="title">Disclaimer</p>
			<p>If you don't like this place, or cannot deal with decisions or conversations had here, you will be offered no compensation and you will not be given any explanations herewith. This is a free service; so you are not entitled to anything contained herein, nor are you entitled to anything from any other party.</p>
		</td>
	</tr>
	<tr class="h"><td class="b h" id="move">I just made a thread, where did it go?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>It was probably moved or deleted by a staff member. If it was deleted, please make sure your thread meets the criteria we have established. If it was moved, look into the other forums and consider why it was moved there. If you have any questions, PM a staff member.</p>
		</td>
	</tr>
	<tr class="h"><td class="b h" id="rude">I feel that a user is being rude to me. What do I do?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>Stay cool. Don't further disrupt the thread by responding <b>at all</b> to the rudeness. Let a member of staff know with a link to the offending post(s). Please note that responding to the rudeness is promoting flaming, which is a punishable offense.</p>
		</td>
	</tr>
	<tr class="h"><td class="b h" id="smile">Are smilies and BBCode supported?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>There are some smilies here, a chart is below to show what smilies are supported.</p>
			<?php echo $smilietext; ?>
			<p>Likewise, a selection of BBCode is supported. See the chart below.</p>
			<p><table>
				<tr>
					<td class="b h">Tag</td>
					<td class="b h">Effect</td>
				</tr><tr>
					<td class="b n1">[b]<i>text</i>[/b]</td>
					<td class="b n2"><b>Bold Text</b></td>
				</tr><tr>
					<td class="b n1">[i]<i>text</i>[/i]</td>
					<td class="b n2"><i>Italic Text</i></td>
				</tr><tr>
					<td class="b n1">[u]<i>text</i>[/u]</td>
					<td class="b n2" style="text-decoration:underline">Underlined Text</td>
				</tr><tr>
					<td class="b n1">[s]<i>text</i>[/s]</td>
					<td class="b n2"><?php echo '<s>Striked-out Text</s>'; ?></td>
				</tr><tr>
					<td class="b n1">[color=<b>hexcolor</b>]<i>text</i>[/color]</td>
					<td class="b n2"><span style="color: #BCDE9A">Custom color Text</span></td>
				</tr><tr>
					<td class="b n1">[img]<i>URL of image to display</i>[/img]</td>
					<td class="b n2">Displays an image.</td>
				</tr><tr>
					<td class="b n1">[spoiler]<i>text</i>[/spoiler]</td>
					<td class="b n2">Used for hiding spoiler text.</td>
				</tr><tr>
					<td class="b n1">[code]<i>code text</i>[/code]</td>
					<td class="b n2">Displays code in a formatted box.</td>
				</tr><tr>
					<td class="b n1">[url]<i>URL of site or page to link to</i>[/url]<br>[url=<i>URL</i>]<i>Link title</i>[/url]</td>
					<td class="b n2">Creates a link with or without a title.</td>
				</tr><tr>
					<td class="b n1">@"<i>User Name</i>"<br>[user=<i>id</i>]</td>
					<td class="b n2">Creates a link to a user's profile complete with name colour.</td>
				</tr><tr>
					<td class="b n1">[forum=<i>id</i>]</td>
					<td class="b n2">Creates a link to a forum by id.</td>
				</tr><tr>
					<td class="b n1">[thread=<i>id</i>]</td>
					<td class="b n2">Creates a link to a thread by id.</td>
				</tr><tr>
					<td class="b n1">[youtube]<i>video id</i>[/youtube]</td>
					<td class="b n2">Creates an embeded YouTube video.</td>
				</tr>
	 		</table></p>
			<p>Also, most HTML tags are able to be used in your posts.</p>
		</td>
	</tr>
	<?php if ($config['hasirc']) { ?>
	<tr class="h"><td class="b h" id="irc">What's this IRC thing I keep hearing about?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>If you have an IRC client like <a href="https://hexchat.github.io">HexChat</a>, you can join a chatroom related to this board.<br>
			The IRC channel is <?=$config['channel']; ?> at <?=$config['network']; ?>.</p>
		</td>
	</tr>
	<?php } ?>
	<tr class="h"><td class="b h" id="reg">Can I register more than one account?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>No, you may not. Most uses for a secondary account tend to be to bypass bans. The the most common non-malicious use is to have a different name, and we have another feature will allow this cleanly.</p>
		</td>
	</tr>
	<tr class="h"><td class="b h" id="css">What are we not allowed to do in our custom CSS layouts?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>While we allow very open and customizable layouts and side bars, we have a few rules that will be strictly enforced. Please read them over and follow them. Loss of post layout privileges will be enacted for those who are repeat offenders. If in doubt ask a staff member. Staff has discretion in deciding violations.</p>
			<p>The following are not allowed:</p>
			<ol>
				<li>Modification of anyone else's post layout <b>for any reason</b>.</li>
				<li>Modification of any tables, images, themes, etc outside of your personal layout.</li>
				<li>Altering your Nick color in any way. Nick color is an indicator of staff, and it will be considered impersonation of staff.</li>
			</ol>
		</td>
	</tr>
	<tr class="h"><td class="b h" id="private">Are private messages supported?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>Yes. Your private message inbox is represented by an envelope icon which is highlighted green when you have unread messages. Likewise, you may send a user a message from here, or use the "Send Private Message" link from the user's profile.</p>
		</td>
	</tr>
	<tr class="h"><td class="b h" id="usercols">What do the username colours mean?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			<p>They reflect the group of the user.</p>
			<p><table style="text-align:center"><?php echo $nctable; ?></table></p>
			<p><?=($config['perusercolor'] ? "Keep in mind that some users might have a specific colour assigned to them." : '') ?></p>
		</td>
	</tr>
</table>
<?php
pagefooter();

?>
