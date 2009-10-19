<?php
/*
 *      modify_class.php
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

dgr_startup();

if ( ! isset($_POST['id']) || ! isset($_POST['name']) || ! isset($_POST['startyear'])
	|| ! isset($_POST['tutorid']) || ! isset($_POST['qid']) )
	exit;

try {
	$u = new DGradeUser();
} catch ( Exception $e ) {
	exit;
}

$added = 0;
$ht = '';

if ( $_POST['id'] == 0 && $u->get_level() == 0 ) {
	$dblink = DGradeDB::instance();
	$dblink->add_class(stripslashes($_POST['name']), stripslashes($_POST['startyear']), $_POST['tutorid']);
} else if ( $_POST['id'] > 0 ) {
	$class = new DGradeClass($_POST['id']);
	if ( $u->get_level() == 0 || $u->get_uid() == $class->get_tutorid() ) {
		$class->set_name(stripslashes($_POST['name']));
		$class->set_startyear(stripslashes($_POST['startyear']));
		$class->set_tutorid($_POST['tutorid']);
		$class->save();
	}
}
if ( $u->get_level() == 0 ) {
	$added = 1;
	$classes = dgr_get_classes();
	$ht = '<option value="0">' . gettext('new class') . '</option>';
	foreach ( $classes as $c )
		$ht .= '<option value="' . $c['class_id'] . '">' . $c['name'] . '</option>';
}
?>

{
"added": "<?php echo $added; ?>",
"selhtml": "<?php echo htmlspecialchars($ht); ?>"
}