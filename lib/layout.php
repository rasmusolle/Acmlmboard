<?php

function urlcreate($url, $query) {
	return $url . '?' . http_build_query($query);
}

function redirect($url, $msg) {
	header("Set-Cookie: pstbon=" . $msg . "; Max-Age=60; Version=1");
	header("Location: " . $url);
	die();
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

	echo "<table class=\"c1\">\n";
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
	switch ($type) {
		case "n":
			$text = "NEW";
			$statusimg = "new.png";
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

	$status = "<div style='line-height:75%'><img src=\"img/status/$statusimg\" alt=\"$text\"><br/>";
	if ($newcount > '0') {
		for ($i = 0, $j = strlen($newcount); $i < $j; $i++) {
			$imgstrings = '';
			$image = $newcount[$i];
			$imgstrings .= "<img src='img/status/" . $image . ".png' alt='" . $newcount[$i] . "'/>";
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
		$table = "\t<table class=c1>\n%s\t</table>\n";
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
		$out .= sprintf('<a href=%s>%s</a>', HTMLAttribEncode($href), $action['title']);
	}
	if ($ret)
		return $out;
	else
		echo $out;
}

function RenderBreadcrumb($breadcrumb) {
	foreach ($breadcrumb as $action) {
		echo sprintf('<a href=%s>%s</a> - ', HTMLAttribEncode($action['href']), $action['title']);
	}
}

function RenderPageBar($pagebar) {
	echo "<table width=100%>";
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
		echo "<table width=100% class=c1><tr><td class='center'>";
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
}

function fieldinputprofile($avatarsize, $max, $field, $userprof) {
	global $user;
	return "<input type=\"text\" name=$field size=$avatarsize maxlength=$max value=\"" . str_replace("\"", "&quot;", $userprof[$field]) . "\">";
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
	global $rankset_names;
	foreach ($rankset_names as $rankset) {
		$rlist[] = $rankset;
	}
	return $rlist;
}

function announcement_row($aleftspan, $arightspan) {
	global $dateformat, $sql;

	$announcement = array();

	$ancs = $sql->fetchp("SELECT title,user,`lastdate` FROM threads
	WHERE forum=0 AND announce=1 ORDER BY `lastdate` DESC LIMIT 1", array());
	if ($ancs) {
		$announcement['title'] = $ancs['title'];
		$announcement['date'] = $ancs['lastdate'];
		$announcement['user'] = $sql->fetchp("SELECT " . userfields() . " FROM users WHERE id=?", array($ancs['user']));
	}

	if (isset($announcement['title']) || has_perm('create-forum-announcements')) {
		if (isset($announcement['title'])) {
			$anlink = "<a href=thread.php?announce>" . $announcement['title'] . "</a> -- Posted by " . userlink($announcement['user']) . " on " . cdate($dateformat, $announcement['date']);
		} else {
			$anlink = "No announcements";
		}
		?><tr class="h"><td class="b" colspan="<?=$aleftspan + $arightspan ?>">Announcements</td></tr>
		<tr class="n1" align="center"><td class="b" colspan=<?=(has_perm('create-forum-announcements') ? "$aleftspan" : ($aleftspan + $arightspan)) ?> align=left><?=$anlink ?></td>
		<?=(has_perm('create-forum-announcements') ? "<td class=\"b\" colspan=$arightspan align=right><a href=newthread.php?announce=1>New Announcement</a></td>" : "") ?>
		</tr><?php
	}
}

?>