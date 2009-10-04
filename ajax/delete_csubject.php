<?php
/*
 *      delete_csubject.php
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

if ( ! isset($_GET['id']) || ! isset($_GET['classid']) || ! isset($_GET['qid']) )
	exit;

try {
	$user = new DGradeUser();
	$class = new DGradeClass($_GET['classid']);
} catch ( Exception $e ) {
	exit;
}

print_r($_GET);

if ( $user->get_level() != 0 && $user->get_uid() != $class->get-tutorid() )
	die();

$dblink = DGradeDB::instance();
$dblink->delete_csubject($_GET['id']);

?>
