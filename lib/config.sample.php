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
$boardlogo = "<img style='border: 0px' src='$defaultlogo' title=\"$boardtitle\">"; // This defines the logo. Recommended you leave this the default.
$favicon = "theme/fav.png"; // Replace with your favicon of choice
$defaulttheme = "new"; // Select the default theme to be used. This will also be showed to users who are not logged in.
$defaultfontsize = 90; // Overall font scale of the board. Default is 90%
$homepageurl = "http://something/"; // This defines the link for the header image.

/*
 * Registeration Bot Protection * Currently the default protection is a simple passphrase with question array used to pull random questions. The way it is coded you could easily replace it with something stronger, or even simpler. The register.php take $puzzleAnswer and $puzzle. Feel free to write something around it.
 */

$puzzleAnswer = 12; // This should be changed
                    // $puzzleAnswer = "Sekrit Key!";// This can also be a string
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
$config['sslbase'] = "https://" . $config['address']; // Replace if you need fine control of the address
$config['path'] = "/"; // If you run your copy in a specific path (ie: http://www.example.gov/board) than this would be 'board/''
$config['meta'] = "<meta name='description' content=\"Stuff goes here!\"><meta name='keywords' content=\"Acmlmboard, Your Stuff\">"; // This is used for search engine keywords.
$config['showssl'] = false; // Shows a link/icon to allow a user to switch to ssl. Enable if you are using on a https server.

/* -- Everything past this point is optional.  It is recommended to get the board up and running first before adjusting the following                  --
   -- The amount of options may be overwelming at first. AB 2.5+ was designed to allow for great flexiblity. As such there are many optional features. -- */

// User GFX limits
$avatardimx = 180; // Avatar X Scale
$avatardimy = 180; // Avatar Y Scale
$avatardimxs = 60; // Avatar X Scale (Scaled Down) **CURRENTLY DISABLED**
$avatardimys = 60; // Avatar Y Scale (Scaled Down) **CURRENTLY DISABLED**
$avatarsize = 2 * 30720; // The Avatar size in bytes. The default is to 60kb.

// The following settings allow a board owner to override a board's theme and logo for special events, etc.
$config['override_theme'] = ""; // If you want to lock everyone to a specific theme.
$config['override_logo'] = ""; // If you want to replace the logo on all themes.

$inactivedays = 30; // The number of days before a user is counted as "inactive"

// The following sectionis related to guests (mostly reflected on online.php)
$config['oldguest'] = 300; // Number of seconds before a guest is deleted due to being "old"

// This will create a delay between consecutive posts if you have the override perm. This is used exclusively to stop mobile double posting.
$config['secafterpost'] = 5; // (in seconds, 0 == off)
                             // This will allow you to set the goal limits for 'Projected date' in profile.php
$config['topposts'] = '5000'; // Number of posts to set the goal to.
$config['topthreads'] = '200'; // Number of threads created to set the goal to.

$config['registrationpuzzle'] = true;

$config['displayname'] = false; // Enable the use of the "Display Name" System. (allows a second name to be used instead of the User's)
$config['perusercolor'] = false; // Enable the use of per-user colors.
$config['nickcolorcss'] = false; // Enables use of CSS to define per theme colors via a span id. Note: You may need to customise CSS to fit your board groups.

$config['lockdown'] = false; // Put board in lockdown mode.

// The following are optional values you can change to personalize your board
$config['atnname'] = "News"; // Title of the attention box. It was 'News' on ABII and "Points of Required Attention™" on B2

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

$rankset_names = array('None', 'Mario'); // List of ranksets, add any new ones here. Don't remove the "None" rankset!
require('img/ranks/rankset.php'); // Default (Mario) rankset

$spatulas = array(
	"Value1",
	"Value2"
);
$spaturand = array_rand($spatulas);
?>