<?php

function redirect($url) {
	header("Location: ".$url);
	die();
}

/**
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
 *		key				-> column key
 *	value['caption']	-> display text for the column header
 *	value['width']		-> (optional) specify a fixed width size (CSS width:)
 *	value['color']		-> (optional) color for the column data cells
 *							which corresponds to CSS '.n' classes)
 *	value['align']		-> (optional) CSS text-align: for the data cells
 *	value['hidden']		-> (optional)
 *
 * `data`
 * An associative array of cell data values:
 *	key				-> column key (must match the header column key)
 *	value				-> cell value
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
	return "'".htmlentities($string, ENT_QUOTES)."'";
}

function rendernewstatus($type) {
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

	return "<img src=\"img/status/$statusimg\" alt=\"$text\">";
}

function RenderForm($form) {
	$formp = "<form action=%s method=%s>\n%s</form>\n";
	$table = "\t<table class=c1>\n%s\t</table>\n";
	$row = "\t\t<tr>\n%s\t\t</tr>\n";
	$rowaction = "\t\t<tr class=\"n1\">\n%s\t\t</tr>\n";
	$rowhead = "\t\t<tr class=\"h\">\n%s\t\t</tr>\n";
	$cell = "\t\t\t<td class=\"b n2\">\n%s\t\t\t</td>\n";
	$cellhead = "\t\t\t<td class=\"b h\" colspan=\"2\">%s</td>\n";
	$celltitle = "\t\t\t<td class=\"b n1 center\">%s</td>\n";
	$cellaction = "\t\t\t<td class=\"b\">\n%s\t\t\t</td>\n";
	$select = "\t\t\t\t<select id=%s name=%s>\n%s\t\t\t\t</select>\n";
	$option = "\t\t\t\t\t<option value=%s %s>%s</option>\n";
	$input = "\t\t\t\t<input id=%s name=%s type=%s %s />\n";
	$radio = "\t\t\t\t<label><input type=\"radio\" id=%s name=%s value=%s %s /> %s</label> \n";

	$title = (isset($form['title'])) ? $form['title'] : '&nbsp;';
	$formout = sprintf($rowhead, sprintf($cellhead, $title));
	foreach ($form['fields'] as $fieldid => $field) {
		$type = $field['type'];
		if ($type != 'submit') {
			$title = (isset($field['title'])) ? $field['title'] . ':' : '&nbsp;';
			$fieldout = sprintf($celltitle, $title);
		} else {
			$fieldout = sprintf($celltitle, '&nbsp;');
		}
		switch ($type) {
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
		$formout .= sprintf(($type == 'submit') ? $rowaction : $row, $fieldout);
	}

	$method = (isset($form['method'])) ? $form['method'] : 'POST';
	$action = (isset($form['action'])) ? $form['action'] : '#';
	$out = sprintf($formp, '"' . $action . '"', '"' . $method . '"', sprintf($table, $formout));
	echo $out;
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
		if (isset($action['href'])) {
			$out .= sprintf('<a href=%s>%s</a>', HTMLAttribEncode($href), $action['title']);
		} else {
			$out .= $action['title'];
		}
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
	if (!empty($pagebar)) {
		echo "<table width=100%><td class=nb>";
		if (!empty($pagebar['breadcrumb']))
			RenderBreadcrumb($pagebar['breadcrumb']);
		echo $pagebar['title']."</td><td class='nb right'>";
		if (!empty($pagebar['actions']))
			RenderActions($pagebar['actions']);
		else
			echo "&nbsp;";
		echo "</td></table>";
		if (!empty($pagebar['message'])) {
			echo "<table width=100% class=c1><tr><td class='center'>".$pagebar['message']."</td></tr></table><br/>";
		}
	}
}

function catheader($title) {
	return "<tr class=\"h\"><td class=\"b h\" colspan=2>$title</td>";
}

function fieldrow($title, $input) {
	return "<tr><td class=\"b n1 center\">$title:</td><td class=\"b n2\">" . stripslashes($input) . "</td>";
}

function fieldinput($avatarsize, $max, $field) {
	global $user;
	return "<input type=\"text\" name=$field size=$avatarsize maxlength=$max value=\"" . str_replace("\"", "&quot;", $user[$field]) . "\">";
}

function fieldtext($rows, $cols, $field) {
	global $user;
	return "<textarea wrap=\"virtual\" name=$field rows=$rows cols=$cols>" . stripslashes(htmlval($user[$field])) . '</textarea>';
}

function fieldoption($field, $checked, $choices) {
	$text = '';
	foreach ($choices as $key => $val)
		$text.="<label><input type=\"radio\" class=\"radio\" name=$field value=$key" . ($key == $checked ? ' checked=1' : '') . ">$val &nbsp;</label>\n";
	return $text;
}

// takes $choices (array with "value" and "name")
function fieldselect($field, $checked, $choices) {
	$text = "<select name=$field>\n";
	foreach ($choices as $key => $val) {
		$text .= "\t<option value=\"$key\"" . ($key == $checked ? ' selected' : '') . ">$val</option>\n";
	}
	$text .= "</select>\n";
	return $text;
}

function themelist() {
	$themes = glob('theme/*', GLOB_ONLYDIR);
	sort($themes);
	foreach ($themes as $f) {
		$themename = explode("/",$f);
		if (file_exists("theme/$themename[1]/$themename[1].css")) {
			if (preg_match("~/* META\n(.*?)\n~s", str_replace("\r\n", "\n", file_get_contents("theme/$themename[1]/$themename[1].css")), $matches)) {
				$themelist[str_replace(".css", "", str_replace(".php", "", $themename[1]))] = $matches[1];
			}
		}
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

function announcement_row($tblspan) {
	global $dateformat, $sql;

	$announcement = [];

	$ancs = $sql->fetchq("SELECT title,user,lastdate FROM threads WHERE announce = 1 ORDER BY lastdate DESC LIMIT 1");
	if ($ancs) {
		$announcement['title'] = $ancs['title'];
		$announcement['date'] = $ancs['lastdate'];
		$announcement['user'] = $sql->fetchp("SELECT ".userfields()." FROM users WHERE id = ?", [$ancs['user']]);
	}

	if (isset($announcement['title']) || has_perm('create-forum-announcements')) {
		if (isset($announcement['title'])) {
			$anlink = "<a href=thread.php?announce>".$announcement['title']."</a> - by " . userlink($announcement['user']) . " on " . date($dateformat, $announcement['date']);
		} else {
			$anlink = "No announcements";
		}
		?><tr class="h"><td class="b" colspan="<?=$tblspan ?>">Announcements</td></tr>
		<tr class="n1 center"><td class="b left" colspan=<?=$tblspan ?>><?=$anlink ?>
			<?=(has_perm('create-forum-announcements') ? "<span class=\"right\" style=\"float:right\"><a href=newthread.php?announce>New Announcement</a></span>" : "") ?>
		</td></tr><?php
	}
}

/**
 * Display $message if $result (the result of a SQL query) is empty (has no lines).
 */
function if_empty_query($result, $message, $colspan = 0, $table = false) {
	global $sql;
	if ($sql->numrows($result) < 1) {
		if ($table) echo '<table class="c1">';
		echo "<tr><td class=\"b n1 center\" ".($colspan != 0 ? "colspan=$colspan" : "")."><p>$message</p></td></tr>";
		if ($table) echo '</table>';
	}
}
