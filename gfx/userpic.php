<?php
$file = '../userpic/' . $_GET['id'];
if ($_GET['r']) $r = "?r=" . $_GET['r'];
if ((is_numeric($_GET['id']) || preg_match("/\d+_\d\d?/", $_GET['id'])) && file_exists($file))
	header("Location:$file$r");
else
	header("Location:../img/_.png");
?>