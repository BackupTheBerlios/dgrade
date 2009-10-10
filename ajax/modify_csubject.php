<?php
/*
 *      save_csubject.php
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

if ( ! isset($_GET['id']) || ! isset($_GET['classid']) || ! isset($_GET['semid']) || ! isset($_GET['subid']) || ! isset($_GET['teachid']) || ! isset($_GET['block']) || ! isset($_GET['desc']) || ! isset($_GET['qid']) )
	exit;

try {
	$user = new DGradeUser();
	$class = new DGradeClass($_GET['classid']);
} catch ( Exception $e ) {
	die($e->getMessage());
}

if ( $user->get_level() != 0 && $user->get_uid() != $class->get-tutorid() )
	die();

$dblink = DGradeDB::instance();

$block = ($_GET['block'] == 1);
$desc = ($_GET['desc'] == 1);

if ( $_GET['id'] > 0 )
	$dblink->set_csubject($_GET['id'], $_GET['subid'], $_GET['teachid'], $block, $desc);
else if ( $_GET['id'] == 0 ) {
	$id = $dblink->add_csubject($_GET['classid'], $_GET['semid'], $_GET['subid'], $_GET['teachid'], $block, $desc);
	foreach ( $class->students as $st )
		$dblink->add_grade($id, $st);
}

?>
