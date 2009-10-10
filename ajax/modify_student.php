<?php
/*
 *      save_student_info.php
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

dgr_require('/includes/db.php');
dgr_require('/includes/user.php');
dgr_require('/includes/class.php');
dgr_require('/includes/student.php');

if ( ! isset($_POST['id']) || ! isset($_POST['classid']) || ! isset($_POST['name']) || ! isset($_POST['surname']) || ! isset($_POST['email']) || ! isset($_POST['paremail']) || ! isset($_POST['qid']) )
	exit;

try {
	$user = new DGradeUser();
	$student = new DGradeStudent($_POST['id']);
} catch ( Exception $e ) {
	exit;
}

$name = stripslashes($_POST['name']);
$surname = stripslashes($_POST['surname']);
$email = stripslashes($_POST['email']);
$paremail = stripslashes($_POST['paremail']);

if ( $_POST['id'] == 0 ) {
	$class = new DGradeClass($_POST['classid']);
	if ( $user->get_level() != 0 && $class->get_tutorid() != $user->get_uid() )
		exit;
	$dblink = DGradeDB::instance();
	$dblink->add_student($_POST['classid'], $name, $surname, $email, $paremail);
} else if ( $_POST['id'] > 0 ) {
	$student = new DGradeStudent($_POST['id']);
	if ( $user->get_level() != 0 && $student->get_tutorid() != $user->get_uid() )
		exit;
	$student->save_info($name, $surname, $email, $paremail);
}

?>

