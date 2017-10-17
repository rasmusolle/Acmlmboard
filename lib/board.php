<?php

  function feedicon($icon,$para,$text="RSS feed"){
    return "<a href='$para'><img src='$icon' border='0' style='margin-right:5px' title='$text'></a>"
          ."<link rel='alternate' type='application/rss+xml' title='$text' href='$para'>";
  }

?>