<?php
/*
 *      get_class_subjects.php
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

dgr_startup();

if ( ! isset($_GET['id']) || ! isset($_GET['semid']) || ! isset($_GET['qid']) )
	exit;

try {
	$user = new DGradeUser();
	$class = new DGradeClass($_GET['id']);
} catch ( Exception $e ) {
	exit;
}

if ( $user->get_level() != 0 && $user->get_uid() != $class->get_tutorid() )
	exit;

?>

<span id="chooseheader"><?php echo gettext('Subjects'); ?></span>
<span id="choosesel" style="display:none">1</span>
<span id="chooseid" style="display:none">0</span>

<div id="chooselist">
<table cellpadding="0" cellspacing="0" width="100%">

<?php
$i = 0;
$csubjects = $class->get_subjects($_GET['semid']);
foreach ( $csubjects as $sub ) {
	$tr = 'choose' . ++$i;
?>

<tr id="<?php echo $tr; ?>" onmouseover="make_pointer(this)" onclick="change_csubject_selection(<?php echo $i; ?>, <?php echo $sub['id'] ?>)">
	<td style="width:15%" class="choosetd"><?php echo $i; ?></td>
	<td class="choosetd"><?php echo $sub['name']; ?></td>
</tr>

<?php } ?>

<tr>
	<td class="choosetd" colspan="2">&nbsp;</td>
</tr>

<tr id="<?php echo 'choose' .  ++$i; ?>" onmouseover="make_pointer(this)" onclick="change_csubject_selection(<?php echo $i; ?>, 0)">
	<td class="choosetd" style="text-align:center" colspan="2"><?php echo gettext('new subject'); ?></td>
</tr>

</table>
</div>