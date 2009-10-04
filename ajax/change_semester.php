<?php
/*
 *      change_semester.php
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

dgr_startup();

if ( ! isset($_POST['id']) || ! isset($_POST['n']) || ! isset($_POST['semstart']) || ! isset($_POST['semend']) || ! isset($_POST['qid']) )
	exit;

try {
	$user = new DGradeUser();
} catch ( Exception $e ) {
	exit;
}

if ( $user->get_level() != 0 )
	exit;

$dblink = DGradeDB::instance();

if ( $_POST['id'] == 0 ) {
	$start = strtotime($_POST['semstart']);
	$end = strtotime($_POST['semend']);
	$dblink->add_semester(stripslashes($_POST['n']), $start, $end);
} else if( $_POST['id'] > 0 )
	$dblink->change_semester($_POST['id'], stripslashes($_POST['n']));

$semesters = dgr_get_semesters();

?>

<option value="0"><?php echo gettext('new semester'); ?></option>
<?php foreach ( $semesters as $sem ) { ?>
	<option value="<?php echo $sem['id']; ?>"><?php echo $sem['name']; ?></option>
<?php } ?>
