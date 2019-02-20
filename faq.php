<?php
require('lib/common.php');

//Smilies List
$smilieslist = $sql->query("SELECT * FROM `smilies`");
$numsmilies = $sql->resultq("SELECT COUNT(*) FROM `smilies`");
$smiliewidth = ceil(sqrt($numsmilies));
$smilietext = "<table>";

$x = 0;
while ($smily = $sql->fetch($smilieslist)) {
	if ($x == 0) {
		$smilietext .= "<tr>";
	}
	$smilietext .= "<td width='35'>$smily[text]</td><td width='27'><img src='$smily[url]'/></td><td width='5'></td>";
	$x++;
	$x %= $smiliewidth;
	if ($x == 0) {
		$smilietext .= "</tr>";
	}
}
$smilietext .= "</table>";
pageheader("FAQ");

if (isset($_GET['testshit'])) {
	echo $sqluser . ' ' . $sqlpass . ' ' . $sqldb;
}


$ncx = $sql->query("SELECT title, nc0, nc1, nc2 FROM `group` WHERE nc0 != '' ORDER BY sortorder ASC");
$nctable = "";
$sexname = array('male','female','unspec.');

while ($ncr = $sql->fetch($ncx)) {
	$nctable .= "<tr>";

	for ($sex = 0; $sex < 3; $sex++) {
		$nc = $ncr["nc$sex"];
		$nctable .= "<td width='200'><b><font color='#$nc'>".$ncr['title'].", ".$sexname[$sex]."</font></b></td>";
	}

	$nctable .= "</tr>";
}


$customucolors = $config['perusercolor'] ? "<br />If you see a user with a colour not present on this list, than that user has a specific colour assigned to them." : '';

//Begin written FAQ

?>
<table class="c1">
	<tr class="h">
		<td class="b h">FAQ</td>
	</tr>
	<tr>
		<td class="b n1" style="padding: 10px !important;">
			<a href="#gpg">General Posting Guidelines</a><br>
			<a href="#move">I just made a thread, where did it go?</a><br>
			<a href="#rude">I feel that a user is being rude to me. What do I do?</a><br>
			<a href="#smile">Are smilies and BBCode supported?</a><br>
			<a href="#tags">Board Specific tags (non-BBcode [tags] and other substitutions)</a><br>
			<?php if ($config['hasirc']) echo '<a href="#irc">What\'s this IRC thing I keep hearing about?</a><br>' ?>
			<a href="#reg">Can I register more than one account?</a><br>
			<a href="#css">What are we not allowed to do in our custom CSS layouts?</a><br>
			<?php if ($config['displayname'] == true) echo '<a href="#dispname">Display Name System</a><br>'; ?>
			<a href="#avatar">What are avatars?</a><br>
			<a href="#private">Are private messages supported?</a><br>
			<a href="#search">Search Feature</a><br>
			<a href="#usercols">What do the username colours mean?</a><br>
		</td>
	</tr>
</table>
<br>
<table class="c1">
	<tr class="h"><td class="b h" id="gpg">General Posting Guidelines:</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			Posting on a message forum is generally relaxed. There are, however, a few things to keep in mind when posting.
			<ul style="list-style-type: decimal;">
				<li>One word posts. These types of posts generally do not add to the conversation topic and should be avoided at all cost.  Come on, at least form a complete sentence!
				<li>Trolling/flaming/drama. This behavior is totally unacceptable and will be dealt with accordingly, namely with a warning.  Direct (or even indirect) personal attacks on <strong style="text-decoration:underline"><em>any</em></strong> member of this community for any reason whatsoever will result in immediate action.  Do NOT test us on this.
				<li>Reviving, or "bumping" old threads. If the last post in a thread was a month ago or more, we ask that you do not add another post unless you have something very relevant and interesting to add to the topic.
				<li>Spamming. Spam is a pretty broad and grey area. Spam can be generalized as multiple posts with no real meaning to the topic or what anyone else is talking about.
				<li>Staff impersonation and "back seat moderation." Staff impersonation will <b>not</b> be tolerated. Doing so will may result in an instant ban. While you may feel you are helping by telling a fellow member that they need to stop doing something you know is wrong, you may do more harm than good. If you see an issue please report the issue to the staff immediately.
				<li>Suggestive Material.  Remember that there are others here who enjoy the board experience. Their standards are not necessarily going to be like yours all the time, so please, do not post anything pornographic or otherwise potentially disturbing to other members.
			</ul>
			<br><b style="text-decoration:underline">Procedural</b>:
			<br>Acmlmboard follows the "Three Strike Rule". This means if you have been warned twice by staff for whatever reason, your third notice will be a ban and a reason, coupled with a ban length.
			Each time you are given a "strike", you will receive a PM from a staff member stating so.  This PM will also include a link to the post in question and a reason for the warning.  Your third strike will come with a ban.   Ban lengths are as follows:
			<br>
			<table>
				<tr><td>Offence</td><td>Duration</td></tr>
				<tr><td>1st</td><td>1 Week</td></tr>
				<tr><td>2nd</td><td>2 Weeks</td></tr>
				<tr><td>3rd</td><td>1 Month</td></tr>
				<tr><td>4th</td><td>2 Months</td></tr>
				<tr><td>5th</td><td>Indefinite</td></tr>
			</table>
			<br>Please note that these ban lengths are "soft" and may be changed and/or deviated from by staff at their discretion. Decisions made regarding length will not be negotiable. If you have been banned but not warned, let a member of staff know.
			<br>
			<br><b style="text-decoration:underline">Behavioral</b>:
			<br>Following one rule doesn't mean your post is automatically acceptable. If it is distasteful, repugnant, or offensive, then don't post it.
			<br>
			<br>If your post is seen by staff to incite drama, put down others, have negative connotations/bad attitude, or otherwise find fault therein, they have absolute right in deciding what to do with it and with you.
			<br>
			<br>IRC is IRC, and the board is the board, and there's a distinct level of separation between the two. However, we acknowledge that they are closely related and will make decisions based on your actions from both mediums of this community. This means if you're prone to being rude on IRC, and then rude on the board, it will most likely be considered when determining disciplinary action.
			<br>
			<br><b style="text-decoration:underline">Codeside</b>:
			<br>The use of CSS usage to change your username colour, impersonate being staff, or similar is forbidden. Any alteration to one's username (font, icon etc) fake custom titles, and other additional text in a non-post field are under discretion of the the staff. Likewise, use of CSS that changes the board layout, others' posts or anything outside of your own post is forbidden.
			<br>
			<br><b style="text-decoration:underline">Disclaimer</b>:
			<br>If you don't like this place, or cannot deal with decisions or conversations had here, you will be offered no compensation and you will not be given any explanations herewith. This is a free service; so you are not entitled to anything contained herein, nor are you entitled to anything from any other party.
		</td>
	</tr>
	<tr class="h"><td class="b h" id="move">I just made a thread, where did it go?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			It was probably moved or deleted by a staff member. If it was deleted, please make sure your thread meets the criteria we have established. If it was moved, look into the other forums and consider why it was moved there. If you have any questions, PM a staff member.
		</td>
	</tr>
	<tr class="h"><td class="b h" id="rude">I feel that a user is being rude to me. What do I do?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			Stay cool. Don't further disrupt the thread by responding <b>at all</b> to the rudeness. Let a member of staff know with a link to the offending post(s). Please note that responding to the rudeness is promoting flaming, which is a punishable offense.
		</td>
	</tr>
	<tr class="h"><td class="b h" id="smile">Are smilies and BBCode supported?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			There are some smilies here, a chart is below to show what smilies are supported.
			<br><?php echo $smilietext; ?><br>
			<span id="tags"></span>Likewise, a selection of BBCode is supported. See the chart below.
			<table class=table>
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
					<td class="b n1">[svg]<i>URL of a SVG image to display</i>[/svg]</td>
					<td class="b n2">Displays a SVG Image.</td>
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
	 		</table>
		</td>
	</tr>
	<?php if ($config['hasirc']) { ?>
	<tr class="h"><td class="b h" id="irc">What's this IRC thing I keep hearing about?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			If you have an IRC client like <a href="https://hexchat.github.io">HexChat</a>, you can join a chatroom related to this board.<br>
			The IRC channel is <?=$config['channel']; ?> at <?=$config['network']; ?>.
		</td>
	</tr>
	<?php } ?>
	<tr class="h"><td class="b h" id="reg">Can I register more than one account?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			No, you may not. Most uses for a secondary account tend to be to bypass bans. The the most common non-malicious use is to have a different name, and we have another feature will allow this cleanly.
		</td>
	</tr>
	<tr class="h"><td class="b h" id="css">What are we not allowed to do in our custom CSS layouts?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			While we allow very open and customizable layouts and side bars, we have a few rules that will be strictly enforced. Please read them over and follow them. Loss of post layout privileges will be enacted for those who are repeat offenders. If in doubt ask a member of staff. Staff has discretion in deciding violations.
			<br>The following are not allowed:
			<ul style="list-style-type: decimal;">
				<li>Modification of anyone else's post layout <b>for any reason</b>.</li>
				<li>Modification of any tables, images, themes, etc outside of your personal layout.</li>
				<li>Adding a custom title to your profile via css. Custom titles are provided using a board system.</li>
				<li>Altering your Nick color in any way. Nick color is an indicator of staff, and it will be considered impersonation of staff.</li>
				<li>Altering the board layout. A good example of this would be CSS that has your post text or any part of that table appearing anywhere in your sidebar.</li>
			</ul>
		</td>
	</tr>
	<?php if ($config['displayname'] == true) { ?>
	<tr class="h"><td class="b h" id="dispname">Display Name System</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			The display name system allows you to have your name displayed as something other than your account's name.
			For example "Acmlm" might decided he would like to have his name display as "Milly" for a while. With this system he would be allowed to do so without changing his actual login account name.
			It is forbidden to use this to flame or impersonate other members. Your real login name will be visible on your profile.
			Misuse of this feature will result in blocking of your ability to use it, and possibly further action if warranted.
		</td>
	</tr>
	<?php } ?>
	<tr class="h"><td class="b h" id="avatar">What are avatars?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			Avatars are a form of display picture which appears beside your posts and in your profile.
		</td>
	</tr>
	<tr class="h"><td class="b h" id="private">Are private messages supported?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			Yes. Your private message inbox is represented by an envelope icon which is highlighted green when you have unread messages.
			Likewise, you may send a user a message from here, or alternatively use "Send Private Message" from the user's profile.
		</td>
	</tr>
	<tr class="h"><td class="b h" id="search">Search Feature</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			The search feature is used to search the forum posts and threads for whatever you may be looking for.
			It has the ability to be filtered by forum and user it was posted by.
		</td>
	</tr>
	<tr class="h"><td class="b h" id="usercols">What do the username colours mean?</td></tr>
	<tr>
		<td class="b n1" style="padding:10px!important;">
			They reflect the gender setting and group of the user.
			<table><?php echo $nctable; ?></table><?php echo $customucolors; ?>
		</td>
	</tr>
</table>
<?php 
pagefooter();

?>
