<?php
/*
 *      get_grades.php
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
dgr_require('/includes/student.php');

dgr_startup();

if ( ! isset($_GET['id']) || ! isset($_GET['semid']) || ! isset($_GET['view']) || ! isset($_GET['qid']) )
	exit;

try {
	$user = new DGradeUser();
} catch ( Exception $e ) {
	exit;
}

if ( $_GET['view'] == 1 ) {
	$student = new DGradeStudent($_GET['id']);
	$grades = $student->get_semester_grades($_GET['semid']);
	$longest = 0;
	foreach ( $grades as &$g ) {
		$g['grades'] = explode(',', $g['grades']);
		reset($g['grades']); /* foreach requires that */
		$cnt = count($g['grades']);
		if ( $cnt > $longest )
			$longest = $cnt;
	}
	unset($g); /* break the reference to make $g usable later */

	$attendance = array();
	$total = $student->get_attendance($_GET['semid'], $attendance);

	$canwritebase = ($user->get_level() == 0) || ($student->get_tutorid() == $user->get_uid());
	$disabled = $canwritebase ? '' : 'disabled="disabled"';
?>

<table id="gradetable" cellpadding="2" cellspacing="0" width="100%" class="centered">
<thead>
<tr>
	<th class="rowheader showcell"><?php echo gettext('Subjects'); ?></th>
	<th class="showcell" colspan="<?php echo $longest; ?>"><?php echo gettext('Grades'); ?></th>
	<th class="rowfooter showcell"><?php echo gettext('Semestral'); ?></th>
</tr>
</thead>
<?php
foreach ( $grades as $g ) {
	if ( ! $canwritebase && $user->get_level() == 2 && $user->get_uid() != $g['uid'] )
		continue;
	$canwrite = $canwritebase || ($user->get_uid() == $g['uid'] && $g['block_teacher'] == 'f');
?>
<?php if ( ! $canwrite ) { ?>
<tbody>
<?php } else { ?>
<tbody onmouseover="make_pointer(this)" onclick="toggle_row(<?php echo $g['id']; ?>)">
<?php } ?>
<tr class="graderow">
	<td class="rowheader showcell"><?php echo $g['name']; ?>: </td>
	<?php foreach ( $g['grades'] as $p ) { ?>
		<td class="showcell gradecell"><?php echo $p; ?></td>
	<?php } ?>
	<?php for ($i=count($g['grades']); $i<$longest; $i++) { ?>
		<td class="showcell gradecell">&nbsp;</td>
	<?php } ?>
	<td class="rowfooter showcell"><?php echo empty($g['semestral']) ? '&nbsp;' : $g['semestral']; ?></td>
</tr>
<?php if ( ! empty($g['notes']) ) { ?>
<tr>
	<td class="rowheader hidcell" style="text-align:right; border-bottom:1px double lightgrey"><?php echo gettext('Notes'); ?>:</td>
	<td class="hidcell" style="border-bottom:1px double lightgrey" colspan="<?php echo $longest + 1; ?>">
		<?php echo $g['notes']; ?>
	</td>
</tr>
</tbody>
<?php } ?>
<?php if ( $canwrite ) { ?>
<tbody class="hidgroup" id="toggle<?php echo $g['id']; ?>">
<tr class="detailrow">
	<td class="rowheader hidcell" style="text-align:right"><?php echo gettext('Grades'); ?>:</td>
	<td class="hidcell" style="text-align:left" colspan="<?php echo $longest + 1; ?>">
	<input style="width:99%" type="text" id="grades<?php echo $g['id']; ?>" value="<?php echo implode(', ', $g['grades']); ?>" />
	</td>
</tr>
<tr class="detailrow">
	<td class="rowheader hidcell" style="text-align:right"><?php echo gettext('Notes'); ?>:</td>
	<td class="hidcell" style="text-align:left" colspan="<?php echo $longest + 1; ?>">
	<input style="width:99%" type="text" id="notes<?php echo $g['id']; ?>" value="<?php echo $g['notes']; ?>" />
	</td>
</tr>
<tr class="detailrow">
	<td class="rowheader hidcell" style="text-align:right"><?php echo gettext('Semestral'); ?>:</td>
	<td class="hidcell" style="text-align:left" colspan=<?php echo $longest + 1; ?>">
<?php if ( $g['descriptive_grade'] == 'f' ) { ?>
	<input style="width:99%" type="text" id="semestral<?php echo $g['id']; ?>" value="<?php echo $g['semestral']; ?>" />
<?php } else { ?>
	<textarea style="width:99%" rows="4" id="semestral<?php echo $g['id']; ?>"><?php echo $g['semestral']; ?></textarea>
<?php } ?>
	</td>
</tr>
<tr class="detailrow">
	<td class="rowheader hidcell">&nbsp;</td>
	<td class="hidcell" colspan="<?php echo $longest + 1; ?>">
	<a href="#" onclick="save_grades(<?php echo $g['id']; ?>)"><?php echo gettext('Save'); ?></a>
	</td>
</tr>
</tbody>
<?php } ?>
<?php } ?>

</table>

<br style="line-height:400%" />

<table style="width:100%" cellpadding="2" cellspacing="0" class="centered">
<?php if ( $canwritebase ) { ?>
<thead onmouseover="make_pointer(this)" onclick="toggle_attendance()">
<?php } else { ?>
<thead>
<?php } ?>
<tr>
	<th class="showcell" colspan="3"><?php echo gettext('Attendance'); ?></th>
</tr>
<tr>
	<th class="showcell"><?php echo gettext('Absent'); ?></th>
	<th class="showcell"><?php echo gettext('Explained'); ?></th>
	<th class="showcell"><?php echo gettext('Late'); ?></th>
</tr>
<tr class="graderow">
	<td class="showcell"><?php echo $total['absent']; ?></td>
	<td class="showcell"><?php echo $total['explained']; ?></td>
	<td class="showcell"><?php echo $total['late']; ?></td>
</tr>
</thead>
<?php if ( $canwritebase ) { ?>
<tbody style="visibility:hidden" id="attendance">
<tr class="detailrow">
	<td class="hidcell" colspan="3">
	<select id="attsel" onchange="change_attendance()">
	<?php foreach ( $attendance as $a ) { ?>
		<option value="<?php echo $a['id']; ?>"><?php echo $a['day_start'] . ' - ' . $a['day_end']; ?></option>
	<?php } ?>
	</select>
	</td>
</tr>
<tr class="detailrow">
	<td class="hidcell">
	<input size="5" type="text" id="absn" <?php echo $disabled; ?> value="<?php echo $attendance[0]['absent']; ?>" />
	</td>
	<td class="hidcell">
	<input size="5" type="text" id="expl" <?php echo $disabled; ?> value="<?php echo $attendance[0]['explained']; ?>" />
	</td>
	<td class="hidcell">
	<input size="5" type="text" id="late" <?php echo $disabled; ?> value="<?php echo $attendance[0]['late']; ?>" />
	</td>
</tr>
<tr class="detailrow">
	<td class="hidcell" colspan="3">
		<a href="#" id="atthref" onclick="save_attendance(<?php echo $attendance[0]['id']; ?>)">
			<?php echo gettext('Save'); ?>
		</a>
	</td>
</tr>
</tbody>
</table>

<br style="line-height:400%" />

<table style="width:100%; table-layout:fixed" cellpadding="2" cellspacing="0" class="centered">
<tr>
	<td class="sendhref">
	<a href="#" onclick="sendone(<?php echo $student->get_id(); ?>, <?php echo $_GET['semid']; ?>)">
		<?php echo gettext('Send'); ?>
	</a>
	</td>
	<td class="sendhref">
	<a href="#" onclick="sendall(<?php echo $_GET['semid']; ?>)">
		<?php echo gettext('Send to all'); ?>
	</a>
	</td>
<!--
	<td class="sendhref">
	<a href="#" onclick="print()">
		<?php echo gettext('Export'); ?>
	</a>
	</td>
-->
</tr>
<tr>
	<td id="mailmsg" class="msgbox green" colspan="2">&nbsp;</td>
</tr>
</table>
<?php } else { ?>
</table>
<?php } ?>

<?php } else if ( $_GET['view'] == 2 ) {
	$class = new DGradeClass($user->get_classid());
	$data = array();
	$longest = 0;
	$userid = $user->get_uid();
	$canwritebase = ($user->get_level() == 0) || ($class->get_tutorid() == $user->get_uid());
	foreach ( $class->students as $st ) {
		$info = $class->get_student_brief($st);
		$student = new DGradeStudent($st);
		$rec = $student->get_subject_grades($_GET['id'], $_GET['semid']);
		$rec['grades'] = explode(',', $rec['grades']);
		$cnt = count($rec['grades']);
		if ( $cnt > $longest )
			$longest = $cnt;
		$rec['student_id'] = $info['id'];
		$rec['info'] = $info['name'] . ' ' . $info['surname'];
		$data[] = $rec;
	}
?>

<table id="gradetable" cellpadding="2" cellspacing="0" width="100%" class="centered">
<thead>
<tr>
	<th class="studentheader showcell" style="font-weight:bold"><?php echo gettext('Students'); ?></th>
	<th class="showcell" colspan="<?php echo $longest; ?>"><?php echo gettext('Grades'); ?></th>
	<th class="rowfooter showcell"><?php echo gettext('Semestral'); ?></th>
</tr>
</thead>
<?php
foreach ( $data as $d ) {
	$canwrite = $canwritebase || ($d['block_teacher'] == 'f');
?>
<?php if ( ! $canwrite ) { ?>
<tbody>
<?php } else { ?>
<tbody onmouseover="make_pointer(this)" onclick="toggle_row(<?php echo $d['id']; ?>)">
<?php } ?>
<tr class="graderow">
	<td class="studentheader showcell"><?php echo $d['info']; ?></td>
	<?php foreach ( $d['grades'] as $p ) { ?>
		<td class="showcell gradecell"><?php echo $p; ?></td>
	<?php } ?>
	<?php for ($i=count($d['grades']); $i<$longest; $i++) { ?>
		<td class="showcell gradecell">&nbsp;</td>
	<?php } ?>
	<td class="rowfooter showcell"><?php echo empty($d['semestral']) ? '&nbsp;' : $d['semestral']; ?></td>
</tr>
<?php if ( ! empty($d['notes']) ) { ?>
<tr>
	<td class="rowheader hidcell" style="text-align:right; border-bottom:1px double lightgrey"><?php echo gettext('Notes'); ?>:</td>
	<td class="hidcell" style="border-bottom:1px double lightgrey" colspan="<?php echo $longest + 1; ?>">
		<?php echo $d['notes']; ?>
	</td>
</tr>
</tbody>
<?php } ?>
<?php if ( $canwrite ) { ?>
<tbody class="hidgroup" id="toggle<?php echo $d['id']; ?>">
<tr class="detailrow">
	<td class="rowheader hidcell" style="text-align:right"><?php echo gettext('Grades'); ?>:</td>
	<td class="hidcell" style="text-align:left" colspan="<?php echo $longest + 1; ?>">
	<input style="width:99%" type="text" id="grades<?php echo $d['id']; ?>" value="<?php echo implode(', ', $d['grades']); ?>" />
	</td>
</tr>
<tr class="detailrow">
	<td class="rowheader hidcell" style="text-align:right"><?php echo gettext('Notes'); ?>:</td>
	<td class="hidcell" style="text-align:left" colspan="<?php echo $longest + 1; ?>">
	<input style="width:99%" type="text" id="notes<?php echo $d['id']; ?>" value="<?php echo $d['notes']; ?>" />
	</td>
</tr>
<tr class="detailrow">
	<td class="rowheader hidcell" style="text-align:right"><?php echo gettext('Semestral'); ?>:</td>
	<td class="hidcell" style="text-align:left" colspan=<?php echo $longest + 1; ?>">
<?php if ( $d['descriptive_grade'] == 'f' ) { ?>
	<input style="width:99%" type="text" id="semestral<?php echo $d['id']; ?>" value="<?php echo $d['semestral']; ?>" />
<?php } else { ?>
	<textarea style="width:99%" rows="4"><?php echo $d['semestral']; ?></textarea>
<?php } ?>
	</td>
</tr>
<tr class="detailrow">
	<td class="rowheader hidcell">&nbsp;</td>
	<td class="hidcell" colspan="<?php echo $longest + 1; ?>">
	<a href="#" onclick="save_grades(<?php echo $d['id']; ?>)"><?php echo gettext('Save'); ?></a>
	</td>
</tr>
</tbody>
<?php } ?>
<?php } ?>

</table>

<?php } ?>
