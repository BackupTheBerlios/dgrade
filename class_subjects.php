<?php
/*
 *      class_subjects.php
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

require_once dirname(__FILE__) . '/common.php';

dgr_require('/includes/user.php');
dgr_require('/includes/class.php');

dgr_startup();

try {
	$user = new DGradeUser();
	$style = dgr_get_style('.', $user->get_styleid());
} catch ( Exception $e ) {
	/* user not logged in */
	dgr_redirect('index.php');
}

if ( $user->get_level() == 0 ) {
	$classes = dgr_get_classes();
	if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
		$classid = $_GET['id'];
	} else {
		$classid = (int)$classes[0]['class_id'];
	}
} else {
	$classid = $user->get_tutored();
	if ( $classid == 0 )
		die();
}


$semesters = dgr_get_semesters();
if ( $classid > 0 && count($semesters) > 0 ) {
	$semid = $semesters[0]['id'];
	$class = new DGradeClass($classid);
	$lc = true;
} else
	$lc = false;

$subjects = dgr_get_subjects();
$teachers = dgr_get_users(true);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>DGrade - <?php echo gettext('subjects'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.18" />
	<link type="text/css" rel="stylesheet" href="<?php echo $style; ?>" />
	<script type="text/javascript" src="scripts/common.js"></script>
	<script type="text/javascript" src="scripts/manage.js"></script>
</head>

<body onload="set_height()">
<div id="main">

<!-- topmenu -->
<div id="topmenu">

<div class="left">
<span><?php echo gettext('Logged in as') . ': ' . $user->get_name() . ' ' . $user->get_surname(); ?></span>
<br />
<span><a href="index.php?logout=1"><?php echo gettext('Logout'); ?></a></span>
</div>

<div class="right">

<span class="menuitem"><a href="main.php"><?php echo gettext('Main page'); ?></a></span>
<?php if ( $user->get_level() == 0 ) { ?>
<span class="menuitem"><a href="classes.php"><?php echo gettext('Classes'); ?></a></span>
<span class="menuitem"><a href="subjects.php"><?php echo gettext('Subjects & semesters'); ?></a></span>
<span class="menuitem"><a href="users.php"><?php echo gettext('Users'); ?></a></span>
<?php } ?>
<span class="menuitem"><a href="settings.php"><?php echo gettext('Settings'); ?></a></span>
</div>

</div>

<!-- menu -->
<div id="nav">

<?php if ( $user->get_level() == 0 ) { ?>
<span class="menuitem left doubleline"><?php echo gettext('Class'); ?>:</span>

<span class="menuitem left doubleline">
	<select id="selclass" name="selclass" onchange="change_csubjects(true)">
	<?php foreach ( $classes as $c ) { ?>
		<option value="<?php echo $c['class_id']; ?>"><?php echo $c['name']; ?></option>
	<?php } ?>
	</select>
</span>
<?php } ?>
<span style="display:none" id="classidspan"><?php echo $classid; ?></span>

<span class="menuitem right doubleline">
	<select id="selsemester" name="selsemester" onchange="change_csubjects(false)">
	<?php foreach ( $semesters as $sem ) { ?>
		<option value="<?php echo $sem['id']; ?>"><?php echo $sem['name']; ?></option>
	<?php } ?>
	</select>
</span>
<span class="menuitem right doubleline"><?php echo gettext('Semester'); ?>:</span>

</div>

<br />

<div id="maincontainer">
<div id="choosebox">


<span id="chooseheader"><?php echo gettext('Subjects'); ?></span>
<span id="choosesel" style="display:none">1</span>
<span id="chooseid" style="display:none">0</span>

<table cellpadding="0" cellspacing="0" width="100%">
<?php if ( $lc ) {
$i = 0;
$csubjects = $class->get_subjects($semid);
foreach ( $csubjects as $sub ) {
	$tr = 'choose' . ++$i;
?>

<tr id="<?php echo $tr; ?>" onmouseover="make_pointer(this)" onclick="change_csubject_selection(<?php echo $i; ?>, <?php echo $sub['id'] ?>)">
	<td style="width:15%" class="choosetd"><?php echo $i; ?></td>
	<td class="choosetd"><?php echo $sub['name']; ?></td>
</tr>

<?php } ?>

<?php } ?>

<tr>
	<td class="choosetd" colspan="2">&nbsp;</td>
</tr>

<tr id="<?php echo 'choose' .  ++$i; ?>" onmouseover="make_pointer(this)" onclick="change_csubject_selection(<?php echo $i; ?>, 0)">
	<td class="choosetd" style="text-align:center" colspan="2"><?php echo gettext('new subject'); ?></td>
</tr>

</table>

</div>

<div id="workarea">
<span id="inputdisabled" style="display:none">1</span>

<table style="padding-top:64px" cellpadding="8" width="50%" class="centered">
	<tr>
		<td><?php echo gettext('Subject name'); ?>:</td>
		<td>
		<select id="subsel" disabled="disabled">
		<?php foreach ( $subjects as $sub ) { ?>
		<option value="<?php echo $sub['subject_id']; ?>"><?php echo $sub['name']; ?></option>
		<?php } ?>
		</select>
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('Teacher'); ?>:</td>
		<td>
		<select id="teachsel" disabled="disabled">
		<?php foreach ( $teachers as $t ) { ?>
		<option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
		<?php } ?>
		</select>
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('Block teacher'); ?>:</td>
		<td>
		<input id="blockyes" type="radio" name="block" checked="checked" value="yes" disabled="disabled" />
		<span><?php echo gettext('yes'); ?></span>
		<input id="blockno" type="radio" name="block" value="no" disabled="disabled" />
		<span><?php echo gettext('no'); ?></span>
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('Descriptive grade'); ?>:</td>
		<td>
		<input id="descyes" type="radio" name="desc" value="yes" disabled="disabled" />
		<span><?php echo gettext('yes'); ?></span>
		<input id="descno" type="radio" name="desc" value="no" checked="checked" disabled="disabled" />
		<span><?php echo gettext('no'); ?></span>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="text-align:center">
			<input type="button" id="addbutton" value="<?php echo gettext('Save'); ?>" disabled="disabled" onclick="save_csubject()" />
		</td>
		<td style="text-align:center">
			<input type="button" id="delbutton" value="<?php echo gettext('Delete'); ?>" disabled="disabled" onclick="delete_csubject()" />
		</td>
	</tr>
</table>
</div>

</div>


</div>

</body>
</html>
