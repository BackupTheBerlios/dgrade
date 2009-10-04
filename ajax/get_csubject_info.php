<?php
/*
 *      get_csubject_info.php
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

if ( $user->get_level() != 0 && $user->get_uid() != $class->get-tutorid() )
	die();

if ( $_GET['id'] > 0 ) {

$dblink = DGradeDB::instance();
$info = $dblink->get_subject_details($_GET['id']);

?>

{
"subid": "<?php echo $info['subject_id']; ?>",
"teachid": "<?php echo $info['uid']; ?>",
"block": "<?php echo $info['block_teacher']; ?>",
"desc": "<?php echo $info['descriptive_grade']; ?>"
}

<?php } else if ( $_GET['id'] == 0 ) { ?>

{
"subid": "0",
"teachid": "0",
"block": "t",
"desc": "f"
}

<?php } ?>
