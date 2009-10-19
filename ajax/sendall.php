<?php
/*
 *      sendall.php
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

$email = $user->get_email();

if ( empty($email) ) {
	$err = 1;
	$msg = gettext('E-mail not set');
} else {
	$ret = true;
	foreach ( $class->students as $st ) {
		$student = new DGradeStudent($st);
		$ret = $ret && $student->send($_GET['semid'], $email);
	}
	if ( $ret ) {
		$err = 0;
		$msg = gettext('All messages sent');
	} else {
		$err = 1;
		$msg = gettext('Not all messages were sent :(');
	}
}

?>

{
"status": "<?php echo $err; ?>",
"msg": "<?php echo $msg; ?>"
}