<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: member_stats.php 2734 2004-12-08 20:21:10Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ADMIN);


require(AT_INCLUDE_PATH.'header.inc.php');

// 3. Table displays all content pages with no. of hits by user
if (isset($_GET['submit'])) {

	$_GET['member_picker'] = intval($_GET['member_picker']);

	//Table displays all content pages with no. of hits by user
	echo '<table class="data static" rules="cols" summary="">';
	echo '<thead>';
	echo '<tr>';
		echo '<th scope="col">';
			echo _AT('page');
		echo '</th>';
		echo '<th scope="col">';
			echo _AT('visits');
		echo '</th>';
		echo '<th scope="col">';
			echo _AT('duration');
		echo '</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';

	$sql = "SELECT MT.counter, MT.content_id, SEC_TO_TIME(MT.duration) AS total, C.title 
			FROM ".TABLE_PREFIX."content C LEFT JOIN ".TABLE_PREFIX."member_track MT
			ON MT.content_id=C.content_id AND MT.member_id=$_GET[member_picker]
			WHERE C.course_id=$_SESSION[course_id] ORDER BY content_id ASC";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['total'] == '')
				$row['total'] = _AT('na');

			echo '<tr>';
				echo '<td><a href='.$_base_href.'content.php?cid='.$row['content_id']. '>' . AT_print($row['title'], 'content.title') . '</a></td>';
				echo '<td>' . intval($row['counter']) . '</td>';
				echo '<td>' . $row['total'] . '</td>';
			echo '</tr>';
		} //end while

		echo '</tbody>';

	} else {
		echo '<tr><td>' . _AT('tracker_data_empty') . '</td></tr>';
		echo '</tbody>';
	}
	echo '</table>';
}

if (!isset($_GET['submit'])) {
	// 1. Display drop down with list of users in the course
	$sql	= "SELECT M.member_id, M.login
				FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."course_enrollment C 
				WHERE M.member_id=C.member_id AND C.course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	echo '<form name="form" action="' . $_SERVER['PHP_SELF'] . '" method="get">';
	echo '<div class="input-form">';
		echo '<div class="row">';
			echo '<label for="member_picker">'._AT('select_member').'</label><br />';
			echo '<select name="member_picker" id="member_picker">';
				while ($row = mysql_fetch_assoc($result)) {
					echo '<option value="'.$row['member_id'].'">'.$row['login'].'</option>';
				}
			echo '</select>';
			// 2. On selecting user -> POST to same page with user_id

		echo '</div>';
		echo '<div class="row buttons">';
			echo '<input type="submit" name="submit" value="'._AT('view').'" />';
		echo '</div>';
	echo '</div>';
	echo '</form>';
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>