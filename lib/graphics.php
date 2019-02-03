<?php

function img_upload($fname,$img_targ,$img_x,$img_y,$img_size) {
	$ftypes = array("png","jpeg","jpg","gif");
	$img_data = getimagesize($fname['tmp_name']);
	$err = 0; $oerr = "";
	if ($img_data[0] > $img_x) {
		$oerr .= "<br>Too wide.";
		$err = 1;
	}
	if ($img_data[1] > $img_y) {
		$oerr .= "<br>Too tall.";
		$err = 1;
	}
	if ($fname['size'] > $img_size) {
		$oerr .= "<br>Filesize limit of $img_size bytes exceeded.";
		$err=1;
	}
	if (!in_array(str_replace("image/","",$img_data['mime']),$ftypes)) {
		$oerr = "Invalid file type.";
		$err = 1;
	}
	if ($err) return $oerr;
	if (move_uploaded_file($fname['tmp_name'],$img_targ)) {
		return "OK!";
	} else {
		return "<br>Error creating file.";
	}
}
?>