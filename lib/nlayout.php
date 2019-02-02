<?php

function urlcreate($url, $query) {
	return $url . '?' . http_build_query($query);
}

/** Our first step to sanity, brought to us by Kawa **
 *
 * function RenderTable(data, headers) 
 *
 * Renders (outputs) a table in HTML using `headers` for column definition
 * and `data` to fill cells with data.
 *
 * Return value: none
 *
 * Parameters:
 * `headers`
 * An associative array of column definitions:
 *    key                -> column key
 *    value['caption']   -> display text for the column header
 *    value['width']     -> (optional) specify a fixed width size (CSS width:)
 *    value['color']     -> (optional) color for the column data cells 
 *                          which corresponds to CSS '.n' classes)
 *    value['align']     -> (optional) CSS text-align: for the data cells
 *    value['hidden']    -> (optional) 
 *
 * `data`
 * An associative array of cell data values:
 *    key                -> column key (must match the header column key)
 *    value              -> cell value
 *
 */
function RenderTable($data, $headers) {
	$zebra = 0;

	echo "<table cellspacing=\"0\" class=\"c1\">\n";
	echo "\t<tr class=\"h\">\n";
	foreach ($headers as $headerID => $headerCell) {
		if (isset($headerCell['hidden']) && $headerCell['hidden'])
			continue;

		if (isset($headerCell['width']))
			$width = " style=\"width: " . $headerCell['width'] . "\"";
		else
			$width = "";

		echo "\t\t<td class=\"b h\"" . $width . ">" . $headerCell['caption'] . "</td>\n";
	}
	echo "\t</tr>\n";
	foreach ($data as $dataCell) {
		echo "\t<tr>\n";
		foreach ($dataCell as $id => $value) {
			if (isset($headers[$id]['hidden']) && $headers[$id]['hidden'])
				continue;

			$color = $zebra + 1;
			$align = "";
			if (isset($headers[$id]['color']))
				$color = $headers[$id]['color'];
			if (isset($headers[$id]['align']))
				$align = " style=\"text-align: " . $headers[$id]['align'] . "\"";
			echo "\t\t<td class=\"b n" . $color . "\"" . $align . ">" . $value . "</td>\n";
		}
		echo "\t</tr>\n";
		$zebra = ($zebra + 1) % 2;
	}
	echo "</table>\n";
}

//[KAWA] i LoVe AlL oF yOu MoThErFuCkErS :o)
// insanity ensues

function HTMLAttribEncode($string) {
	$pass1 = htmlentities($string, ENT_QUOTES);
	return "'$pass1'";
}

function EmailObscurer($emailin) {
	$email = htmlval($email);
	$email = str_replace("@", "<b>&#64;</b>", $emailin);
	$email = str_replace(".", "<b>&#46;</b>", $email);
	return $email;
}

function rendernewstatus($type, $newcount = '0') {
	global $statusimageset;

	if ($statusimageset != '')
		$imagepath = $statusimageset;
	else
		$imagepath = "img/status/";

	switch ($type) {
		case "n":
			$text = "NEW";
			$statusimg = "new.png";
			break;

		case "e":
			$text = "EDT";
			break;

		case "E":
		case "!e":
			$text = "EDT";
			break;

		case "o":
			$text = "OFF";
			$statusimg = "off.png";
			break;

		case "on":
			$text = "OFF";
			$statusimg = "offnew.png";
			break;

	}
	
	if (!isset($text)) {
		$text = "";
	}
	
	$status = "<div style='line-height:75%;padding: 0;'><img src=\"$imagepath$statusimg\" alt=\"$text\"><br/>";
	if ($newcount > '0') {
		for ($i = 0, $j = strlen($newcount); $i < $j; $i++) {
			$imgstrings = '';
			$image = $newcount[$i];
			$imgstrings .= "<img src='$imagepath" . $image . ".png' alt='" . $newcount[$i] . "'/>";
		}
	}
	$status .= (isset($imgstrings) ? $imgstrings : '') . "</div>";

	return $status;
}

// TO CONSIDER: make this function take raw contents for fields rather than arrays
// and raw content would be generated by FormTextInput()/FormSelectInput()/etc functions
// (perhaps named differently, or perhaps we could reuse the functions editprofile uses)
function RenderForm($form) {
	if ($form) {
		$formp = "<form action=%s method=%s>\n%s</form>\n";
		$table = "\t<table cellspacing=0 class=c1>\n%s\t</table>\n";
		$row = "\t\t<tr>\n%s\t\t</tr>\n";
		$rowaction = "\t\t<tr class=\"n1\">\n%s\t\t</tr>\n";
		$rowhead = "\t\t<tr class=\"h\">\n%s\t\t</tr>\n";
		$cell = "\t\t\t<td class=\"b n2\">\n%s\t\t\t</td>\n";
		$cellhead = "\t\t\t<td class=\"b h\" colspan=\"2\">%s</td>\n";
		$celltitle = "\t\t\t<td align=\"center\" class=\"b n1\">%s</td>\n";
		$cellaction = "\t\t\t<td class=\"b\">\n%s\t\t\t</td>\n";
		$select = "\t\t\t\t<select id=%s name=%s>\n%s\t\t\t\t</select>\n";
		$option = "\t\t\t\t\t<option value=%s %s>%s</option>\n";
		$input = "\t\t\t\t<input id=%s name=%s type=%s %s />\n";
		$radio = "\t\t\t\t<label><input type=\"radio\" id=%s name=%s value=%s %s /> %s</label> \n";
		$formout = '';

		if (isset($form['categories'])) {
			foreach ($form['categories'] as $catid => $cat) {

				$title = (isset($cat['title'])) ? $cat['title'] : '&nbsp;';
				$catout = sprintf($rowhead, sprintf($cellhead, $title));
				foreach ($cat['fields'] as $fieldid => $field) {
					$type = $field['type'];
					if ($type != 'submit') {
						$title = (isset($field['title'])) ? $field['title'] . ':' : '&nbsp;';
						$fieldout = sprintf($celltitle, $title);
					} else {
						$fieldout = sprintf($celltitle, '&nbsp;');
					}
					switch ($type) {
						case 'color':
							$size = 6;
							$length = 6;
							$valuestring = (isset($field['value'])) ? ' value=' . HTMLAttribEncode($field['value']) . ' ' : '';
							$fieldout .= sprintf($cell, sprintf($input, $fieldid, $fieldid, 'text', "size=$size maxlength=$length $valuestring"));
							break;

						case 'imgref':
							$size = 40;
							$length = 60;
							$valuestring = (isset($field['value'])) ? ' value=' . HTMLAttribEncode($field['value']) . ' ' : '';
							$fieldout .= sprintf($cell, sprintf($input, $fieldid, $fieldid, 'text', "size=$size maxlength=$length $valuestring"));
							break;

						case 'numeric':
						case 'text':
							$length = (isset($field['length'])) ? $field['length'] : 60;
							if (!isset($field['size']) && !isset($field['length'])) {
								$size = 40;
							} elseif (!isset($field['size'])) {
								$size = $length;
							} else {
								$size = $field['size'];
							}
							$valuestring = (isset($field['value'])) ? ' value=' . HTMLAttribEncode($field['value']) . ' ' : '';
							$fieldout .= sprintf($cell, sprintf($input, $fieldid, $fieldid, 'text', "size=$size maxlength=$length $valuestring"));

							break;

						case 'dropdown':
							$optout = '';
							foreach ($field['choices'] as $choiceid => $choice) {
								$selected = ($field['value'] == $choiceid) ? ' selected="selected" ' : '';
								$optout .= sprintf($option, HTMLAttribEncode($choiceid), $selected, $choice);
							}
							$fieldout .= sprintf($cell, sprintf($select, $fieldid, $fieldid, $optout));
							break;

						case 'radio':
							$optout = '';
							foreach ($field['choices'] as $choiceid => $choice) {
								$selected = ($field['value'] == $choiceid) ? ' checked="checked" ' : '';
								$optout .= sprintf($radio, HTMLAttribEncode($fieldid . '_' . $choiceid), $fieldid, HTMLAttribEncode($choiceid), $selected, $choice);
							}
							$fieldout .= sprintf($cell, $optout);
							break;

						case 'submit':
							$title = (isset($field['title'])) ? $field['title'] : 'Submit';
							$fieldout .= sprintf($cellaction, sprintf($input, $fieldid, $fieldid, 'submit', 'class=submit value=' . HTMLAttribEncode($title)));
							break;

						default:
							$fieldout .= sprintf($cell, '&nbsp;');
							break;
					}
					$catout .= sprintf(($type == 'submit') ? $rowaction : $row, $fieldout);
				}
				$formout .= $catout;
			}
		}

		$method = (isset($form['method'])) ? $form['method'] : 'POST';
		$action = (isset($form['action'])) ? $form['action'] : '#';
		$out = sprintf($formp, '"' . $action . '"', '"' . $method . '"', sprintf($table, $formout));
		echo $out;
	}
}

function RenderActions($actions, $ret = false) {
	$out = '';
	$i = 0;
	foreach ($actions as $action) {
		if (isset($action['confirm'])) {
			if ($action['confirm'] === true)
				$confirmmsg = 'Are you sure you want to ' . $action['title'] . '?';
			else
				$confirmmsg = str_replace("'", "\\'", $action['confirm']);

			$href = "javascript:if(confirm('" . $confirmmsg . "')){window.location.href='" . $action['href'] . "';} else {void('');};";
		}
		else {
			$href = $action['href'];
		}
		if ($i++)
			$out.= ' | ';
		$out .= sprintf('<a 
href=%s>%s</a>', HTMLAttribEncode($href), $action['title']);
	}
	if ($ret)
		return $out;
	else
		echo $out;
}

function RenderBreadcrumb($breadcrumb) {
	foreach ($breadcrumb as $action) {
		echo sprintf('<a href=%s>%s</a> - 
', HTMLAttribEncode($action['href']), $action['title']);
	}
}

function RenderPageBar($pagebar) {
	echo "<table cellspacing=0 width=100%>";
	echo "<td class=nb>";
	if (!empty($pagebar['breadcrumb']))
		RenderBreadcrumb($pagebar['breadcrumb']);
	echo $pagebar['title'];
	echo "</td><td align=right class=nb>";
	if (!empty($pagebar['actions']))
		RenderActions($pagebar['actions']);
	else
		echo "&nbsp;";
	echo "</td></table><br/>";
	if (!empty($pagebar['message'])) {
		echo "<table cellspacing=0 width=100% class=c1><tr><td class='center'>";
		echo $pagebar['message'];
		echo "</td></tr></table><br/>";
	}
}

function setfield($field) {
	return "$field='$_POST[$field]'";
}

function catheader($title) {
	return "  <tr class=\"h\">
" . "    <td class=\"b h\" colspan=2>$title</td>";
}

function fieldrow($title, $input) {
	return "  <tr>
" . "    <td class=\"b n1\" align=\"center\">$title:</td>
" . "    <td class=\"b n2\">" . stripslashes($input) . "</td>";
}

function fieldinput($avatarsize, $max, $field) {
	global $user;
	return "<input type=\"text\" name=$field size=$avatarsize maxlength=$max value=\"" . str_replace("\"", "&quot;", $user[$field]) . "\">";
//  return "<input type=\"text\" name=$field size=$avatarsize maxlength=$max value=\"".htmlval($loguser[$field])."\">";
}

function fieldinputprofile($avatarsize, $max, $field, $userprof) {
	global $user;
	return "<input type=\"text\" name=$field size=$avatarsize maxlength=$max value=\"" . str_replace("\"", "&quot;", $userprof[$field]) . "\">";
//  return "<input type=\"text\" name=$field size=$avatarsize maxlength=$max value=\"".htmlval($loguser[$field])."\">";
}

function fieldtext($rows, $cols, $field) {
	global $user;
	return "<textarea wrap=\"virtual\" name=$field rows=$rows cols=$cols>" . stripslashes(htmlval($user[$field])) . '</textarea>';
}

function fieldoption($field, $checked, $choices) {
	$text = '';
	//[KAWA] Added <label> so the text is clickable.
	foreach ($choices as $key => $val)
		$text.="<label><input type=\"radio\" class=\"radio\" name=$field value=$key" . ($key == $checked ? ' checked=1' : '') . ">$val &nbsp;</label>\n";
	return $text;
}

// 2/22/2007 xkeeper - takes $choices (array with "value" and "name")
function fieldselect($field, $checked, $choices) {
	$text = "<select name=$field>\n";
	foreach ($choices as $key => $val) {
		$text .= "\t<option value=\"$key\"" . ($key == $checked ? ' selected' : '') . ">$val</option>\n";
	}
	$text .= "</select>\n";
	return $text;
}

function themelist() {
	global $sql, $loguser;

	$t = $sql->query("SELECT `theme`, COUNT(*) AS 'count' FROM `users` GROUP BY `theme`");
	while ($x = $sql->fetch($t))
		$themeuser[$x['theme']] = intval($x['count']);

	$themes = unserialize(file_get_contents("themes_serial.txt"));
	$themelist = array();
	foreach ($themes as $t) {
		$themeusers = isset($themeuser[$t[1]]) ? $themeuser[$t[1]] : 0;
		$themelist[$t[1]] = $t[0] . ($themeusers ? (" [$themeusers user" . ($themeusers == 1 ? "" : "s") . "]") : "");
	}

	return $themelist;
}

function ranklist() {
	global $sql, $loguser;
	$r = $sql->query("SELECT * FROM ranksets ORDER BY id ASC");
	while ($d = $sql->fetch($r)) {
		$rlist[$d['id']] = $d['name'];
	}
	return $rlist;
}

function announcement_row($announcefid, $aleftspan, $arightspan) {
	global $dateformat, $sql;

	$announcement = array();

	$ancs = $sql->fetchp("SELECT title,user,`lastdate` FROM threads 
    WHERE forum=?  AND announce=1 ORDER BY `lastdate` DESC LIMIT 1", array($announcefid));
	if ($ancs) {
		$announcement['title'] = $ancs['title'];
		$announcement['date'] = $ancs['lastdate'];
		$announcement['user'] = $sql->fetchp("SELECT " . userfields() . " FROM users WHERE id=?", array($ancs['user']));
	}

	if (isset($announcement['title']) || can_create_forum_announcements($announcefid)) {

		if (isset($announcement['title'])) {
			$anlink = "<a href=thread.php?announce=$announcefid>" . $announcement['title'] . "</a> -- Posted by " . userlink($announcement['user']) . " on " . cdate($dateformat, $announcement['date']);
		} else {
			$anlink = "No announcements";
		}
		if ($announcefid)
			$a = "Forum ";
		else
			$a = "";
		echo "
    " . "  <tr class=\"h\">
    " . "    <td class=\"b\" colspan=" . ($aleftspan + $arightspan) . ">" . $a . "Announcements
    " . "    </td>
    " . "  </tr>
    " . "  <tr class=\"n1\" align=\"center\">
    " . "    <td class=\"b\" colspan=" . ((can_create_forum_announcements($announcefid)) ? "$aleftspan" : ($aleftspan + $arightspan)) . " align=left>$anlink
    " . "    </td>
    " . (can_create_forum_announcements($announcefid) ? "<td class=\"b\" colspan=$arightspan align=right><a href=newthread.php?id=$announcefid&announce=1>New Announcement</a></td>" : "") . "
    " . "  </tr>";
	}
}

/*
 * New template functions
 */
function tpl_display($file, $tpl_vars = array()) {

	global  $dateformat, $sql, $log, $loguser, $sqlpass, $views, $botviews, $sqluser, $boardtitle, 
			$extratitle, $boardlogo, $homepageurl, $themefile, $logofile, $url, $config, 
			$favicon, $showonusers, $count, $lastannounce, $lastforumannounce, $inactivedays, 
			$pwdsalt, $pwdsalt2, $abversion, $abdate, $boardprog;

	// TODO: possibly sandbox the template file.
	
	// these are required for page-header
	$g_tpl_vars = array();
	$g_tpl_vars['page-title'] = isset($tpl_vars['page-title']) ? htmlentities($tpl_vars['page-title']) : 'Default Page Title';
	$g_tpl_vars['board-title'] = htmlentities($boardtitle);
	$g_tpl_vars['meta'] = $config['meta'];
	$g_tpl_vars['theme'] = $themefile;
	$g_tpl_vars['font-size'] = $loguser['fontsize'];
	
	// required for page footer.
	$g_tpl_vars['ab-version'] = $abversion;
	$g_tpl_vars['ab-date'] = $abdate;
	$g_tpl_vars['ab-credits'] = $boardprog;
	
	$path = sprintf('%s/templates/%s.html.php', dirname(__DIR__), $file);
	if(!file_exists($path)) {
		echo sprintf('<strong>Error: Template file `%s` is missing!</strong>', $file);
	}
	
	include_once($path);
}

function tpl_input_text($name, $value = '', $size = 0, $maxlength = 0) {
	printf("<input type=\"text\" name=\"%s\" id=\"%s\"%s%s%s />\n",
		htmlentities($name),
			htmlentities($name),
		!empty($value) ? sprintf(' value="%s"', htmlentities($value)) : '',
		$size > 0 ? sprintf(' size="%d"', $size) : '',
		$maxlength > 0 ? sprintf(' maxlength="%d"', $maxlength) : '');
}

function tpl_input_textarea($name, $value = '', $rows = 0, $cols = 0) {
	printf("<textarea wrap=\"virtual\" name=\"%s\" id=\"%s\"%s%s>%s</textarea>\n",
		htmlentities($name),
		htmlentities($name),
		$rows > 0 ? sprintf(' rows="%d"', $rows) : '',
		$cols > 0 ? sprintf(' cols="%d"', $cols) : '',
		!empty($value) ? htmlentities($value) : '');
}

function tpl_input_checkbox($name, $label, $checked = false) {
	printf("<label><input type=\"checkbox\" name=\"%s\" id=\"%s\" value=\"1\"%s /> %s</label>\n",
		htmlentities($name), htmlentities($name), $checked ? ' checked' : '', htmlentities($label));
}

function tpl_table_alternate($rows, $colors = array('n1','n2'), $highlight_index = -1, $highlight_color = 'n3') {
	
}
?>