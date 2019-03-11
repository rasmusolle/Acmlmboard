<?php

// ** Acmlmboard 2 - Configuration **
// Please look through the file and fill in the appropriate information.

$sqlhost = 'localhost';
$sqluser = 'sqlusername';

$pwdsalt = 'Please change me!';
$pwdsalt2 = 'Addtional Salt. Please Change me!';

$sqlpass = 'sqlpassword';
$sqldb = 'sqldatabase';

$trashid = 2; // Designates the id for your trash forum.

$boardtitle = "Insert title here"; // This is what will be displayed at the top of your browser window.
$defaultlogo = "theme/abII.png"; // Replace with the logo of your choice. Note: This is used if a theme doesn't have it's own logo. It is replaced per theme depending on the theme used.
$favicon = "theme/fav.png"; // Replace with your favicon of choice
$defaulttheme = "new"; // Select the default theme to be used. This will also be showed to users who are not logged in.
$homepageurl = "http://something/"; // This defines the link for the header image.
$boardlogo = "<a href='$homepageurl'><img src='$defaultlogo' title='$boardtitle'></a>"; // This defines the logo. Recommended you leave this the default.

// Registeration Bot Protection
$config['registrationpuzzle'] = true;
$puzzleAnswer = 12; // This should be changed
$puzzleVariations = array(
	"What is six times two?",
	"What is twenty four minus fourteen plus two?",
	"What is the square of 4 minus four?",
	"What is six plus six?",
	"What is ten thousand twenty four divided by sixteen minus fifty two?",
	"What is twelve times one?"
); // This also should be changed. It has to match $puzzleAnswer in this example.
$puzzle = $puzzleVariations[array_rand($puzzleVariations)];

$config['log'] = 0; // Enables logging to the database of moderator & administrative actions. 0=off; 1=profile; 2=thread & post; 5=access
$config['ckey'] = "configckey";
$config['address'] = "url"; // Hostname or IP address of your server (this will be public)
$config['base'] = "http://" . $config['address']; // Replace if you need fine control of the address
$config['path'] = "/"; // If you run your copy in a specific path (ie: http://www.example.gov/board) than this would be 'board/''
$config['meta'] = "<meta name='description' content=\"Stuff goes here!\"><meta name='keywords' content=\"Acmlmboard, Your Stuff\">"; // This is used for search engine keywords.

/* -- Everything past this point is optional.  It is recommended to get the board up and running first before adjusting the following                  --
   -- The amount of options may be overwelming at first. AB 2.5+ was designed to allow for great flexiblity. As such there are many optional features. -- */

// User GFX limits
$avatardimx = 180; // Avatar X Scale
$avatardimy = 180; // Avatar Y Scale
$avatarsize = 2 * 30720; // The Avatar size in bytes. The default is to 60kb.

// The following settings allow a board owner to override a board's theme and logo for special events, etc.
$config['override_theme'] = ""; // If you want to lock everyone to a specific theme.

// This will create a delay between consecutive posts if you have the override perm. This is used exclusively to stop mobile double posting.
$config['secafterpost'] = 5; // (in seconds, 0 == off)

$config['displayname'] = false; // Enable the use of the "Display Name" System. (allows a second name to be used instead of the User's)
$config['perusercolor'] = false; // Enable the use of per-user colors.
$config['nickcolorcss'] = false; // Enables use of CSS to define per theme colors via a span id. Note: You may need to customise CSS to fit your board groups.

$config['lockdown'] = false; // Put board in lockdown mode.

// irc stuff
$config['hasirc'] = false;
$config['channel'] = "#changeme";
$config['network'] = "irc.changeme.invalid";

// Sample post content
$config['samplepost'] = <<<HTML
[b]This[/b] is a [i]sample message.[/i] It shows how [u]your posts[/u] will look on the board.
[quote=Anonymous][spoiler]Hello![/spoiler][/quote]
[code]if (true) {
	print "The world isn't broken.";
} else {
	print "Something is very wrong.";
}[/code]
[irc]This is like code tags but without formatting.
<Anonymous> I said something![/irc]
[url=]Test Link. Ooh![/url]
HTML;

// List of bots (web crawlers)
$botlist = array(
	'ia_archiver','baidu','bingbot','duckduckbot','Exabot','Googlebot','msnbot/','Yahoo! Slurp','bot','spider'
);

// List of smilies
$smilies = array(
	array('text' => '-_-', 'url' => 'img/smilies/annoyed.gif'),
	array('text' => '~:o', 'url' => 'img/smilies/baby.gif'),
	array('text' => 'o_O', 'url' => 'img/smilies/bigeyes.gif'),
	array('text' => ':D', 'url' => 'img/smilies/biggrin.gif'),
	array('text' => 'o_o', 'url' => 'img/smilies/blank.gif'),
	array('text' => ';_;', 'url' => 'img/smilies/cry.gif'),
	array('text' => '^^;;;', 'url' => 'img/smilies/cute2.gif'),
	array('text' => '^_^', 'url' => 'img/smilies/cute.gif'),
	array('text' => '@_@', 'url' => 'img/smilies/dizzy.gif'),
	array('text' => 'O_O', 'url' => 'img/smilies/eek.gif'),
	array('text' => '>:]', 'url' => 'img/smilies/evil.gif'),
	array('text' => ':eyeshift:', 'url' => 'img/smilies/eyeshift.gif'),
	array('text' => ':(', 'url' => 'img/smilies/frown.gif'),
	array('text' => '8-)', 'url' => 'img/smilies/glasses.gif'),
	array('text' => ':LOL:', 'url' => 'img/smilies/lol.gif'),
	array('text' => '>:[', 'url' => 'img/smilies/mad.gif'),
	array('text' => '<_<', 'url' => 'img/smilies/shiftleft.gif'),
	array('text' => '>_>', 'url' => 'img/smilies/shiftright.gif'),
	array('text' => 'x_x', 'url' => 'img/smilies/sick.gif'),
	array('text' => ':)', 'url' => 'img/smilies/smile.gif'),
	array('text' => ':P', 'url' => 'img/smilies/tongue.gif'),
	array('text' => ':B', 'url' => 'img/smilies/vamp.gif'),
	array('text' => ';)', 'url' => 'img/smilies/wink.gif'),
	array('text' => ':S', 'url' => 'img/smilies/wobbly.gif'),
	array('text' => '>_<', 'url' => 'img/smilies/yuck.gif'),
	array('text' => ':box:', 'url' => 'img/smilies/box.png'),
	array('text' => ':yes:', 'url' => 'img/smilies/yes.png'),
	array('text' => ':no:', 'url' => 'img/smilies/no.png'),
	array('text' => ':heart:', 'url' => 'img/smilies/heart.gif'),
	array('text' => ':x', 'url' => 'img/smilies/crossmouth.gif'),
	array('text' => ':|', 'url' => 'img/smilies/slidemouth.gif'),
	array('text' => ':@', 'url' => 'img/smilies/dropsmile.gif'),
	array('text' => ':-3', 'url' => 'img/smilies/wobble.gif'),
	array('text' => 'X-P', 'url' => 'img/smilies/xp.gif'),
	array('text' => 'X-3', 'url' => 'img/smilies/x3.gif'),
	array('text' => 'X-D', 'url' => 'img/smilies/xd.gif'),
	array('text' => ':o', 'url' => 'img/smilies/dramatic.gif')
);

$rankset_names = array('None', 'Mario'); // List of ranksets, add any new ones here. Don't remove the "None" rankset!
require('img/ranks/rankset.php'); // Default (Mario) rankset

$spatulas = array(
	"Value1",
	"Value2"
);
$spaturand = array_rand($spatulas);
?>