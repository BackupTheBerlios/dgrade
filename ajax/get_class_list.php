<?php
/*
 *      get_class_list.php
 *
 *      Copyright 2009 fae <fae@onet.eu>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

require_once dirname(__FILE__) . '/../common.php';

dgr_require('/includes/user.php');
dgr_require('/includes/class.php');

if ( ! isset($_GET['id']) || ! isset($_GET['view']) || ! isset($_GET['semid']) || ! isset($_GET['qid']) )
	exit;

try {
	$user = new DGradeUser();
	$class = new DGradeClass($_GET['id']);
	$user->set_classid($_GET['id']);
} catch ( Exception $e ) {
	exit;
}

$ht = '';

if ( $_GET['view'] == 1 ) {
	$ht .= '<span id="chooseheader">' . gettext('Students') . '</span><span id="choosesel" style="display:none">1</span><span id="chooseid" style="display:none">0</span><table cellpadding="0" cellspacing="0" width="100%">';

	$i = 0;
	foreach ( $class->students as $st ) {
		$stinfo = $class->get_student_brief($st);
		$tr = 'choose' . ++$i;
	$ht .= '<tr id="' . $tr . '" onmouseover="make_pointer(this)" onclick="change_selection(' . $i . ',' . $stinfo['id'] . ')">'
		. '<td style="width:15%" class="choosetd">' . $i . '</td>'
		. '<td class="choosetd">' . $stinfo['name'] . '</td>'
		. '<td class="choosetd">' . $stinfo['surname'] . '</td>'
	. '</tr>';

	}
	$ht .= '</table>';

} else if ( $_GET['view'] == 2 ) {
	$ht .= '<span id="chooseheader">' . gettext('Subjects') . '</span><span id="choosesel"  style="display:none">1</span><span id="chooseid" style="display:none">0</span><table cellpadding="0" cellspacing="0" width="100%">';

	$i = 0;
	$accepted = ($user->get_level() == 0) || ($class->get_tutorid() == $user->get_uid());
	foreach ( $class->get_subjects($_GET['semid']) as $sub ) {
		if ( ! $accepted && $sub['uid'] != $user->get_uid() )
			continue;
		$tr = 'choose' . ++$i;
		$ht .= '<tr id="' . $tr . '" onmouseover="make_pointer(this)" onclick="change_selection(' . $i . ',' . $sub['id'] . ')">'
		. '<td class="choosetd" style="text-align:center">' . $sub['name'] . '</td></tr>';
	}
	$ht .= '</table>';
} else
	exit;


?>

{
"hrefvis": "<?php echo ($user->get_uid() == $class->get_tutorid()) ? 1 : 0; ?>",
"managehref": "<?php echo 'classes.php?id=' . $class->get_id(); ?>",
"subjecthref": "<?php echo 'class_subjects.php?id=' . $class->get_id(); ?>",
"choosebox": "<?php echo htmlspecialchars($ht); ?>"
}
