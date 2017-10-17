<?php
  require 'lib/datetime.php';
  require 'lib/request.php';
  require 'lib/mysql.php';
  require 'lib/layout.php';
  require 'lib/config.php';
  require 'lib/database.php';
  require 'lib/perm.php';  
  require 'lib/helpers.php';
  require 'lib/thread.php'; 
  require 'lib/auth.php';
  require 'lib/user.php';
  require 'lib/smilies.php';
  require 'lib/post.php';
  require 'lib/syndrome.php';
  require 'lib/rpg.php';
  require 'lib/graphics.php';
  require 'lib/badges.php';
  require 'lib/irc.php';
  
  // lib/board.php
  function feedicon($icon,$para,$text="RSS feed"){
  	return "<a href='$para'><img src='$icon' border='0' style='margin-right:5px' title='$text'></a>"
  	."<link rel='alternate' type='application/rss+xml' title='$text' href='$para'>";
  }
?>