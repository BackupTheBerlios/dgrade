<?php
/*
 *      save_attendance.php
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

if ( ! isset($_GET['id']) || ! isset($_GET['a']) || ! isset($_GET['e']) || ! isset($_GET['l'])
	|| ! isset($_GET['qid']) )
	exit;

try {
	$user = new DGradeUser();
} catch ( Exception $e ) {
	exit;
}

$dblink = DGradeDB::instance();

if ( $user->get_level() != 0 && ! $dblink->can_modify_attendance($_GET['id'], $user->get_uid()) )
	exit;

$dblink->modify_attendance($_GET['id'], $_GET['a'], $_GET['e'], $_GET['l']);

?>
