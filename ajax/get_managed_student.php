<?php
/*
 *      get_managed_student.php
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
dgr_require('/includes/student.php');

if ( ! isset($_GET['id']) || ! isset($_GET['qid']) )
	exit;

try {
	$user = new DGradeUser();
} catch ( Exception $e ) {
	exit;
}

if ( $_GET['id'] > 0 ) {

try {
	$student = new DGradeStudent($_GET['id']);
	$tutorid = $student->get_tutorid();
} catch ( Exception $e ) {
	die($e->getMessage());
}

if ( $user->get_level() != 0 && $user->get_uid() != $tutorid )
	exit;

$info = $student->get_info();

?>

{
"nameheader": "<?php echo htmlspecialchars($info['name'] . ' ' . $info['surname']); ?>",
"name": "<?php echo htmlspecialchars($info['name']); ?>",
"surname": "<?php echo htmlspecialchars($info['surname']); ?>",
"email": "<?php echo htmlspecialchars($info['email']); ?>",
"paremail": "<?php echo htmlspecialchars($info['parent_email']); ?>"
}

<?php } else if ( $_GET['id'] == 0 ) { ?>

{
"nameheader": "<?php echo gettext('new student'); ?>",
"name": "",
"surname": "",
"email": "",
"paremail": ""
}

<?php } ?>