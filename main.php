<?php
/*
 *      main.php
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

$error = false;
$errmsg = '';

try {
	$user = new DGradeUser();
	$style = dgr_get_style('.', $user->get_styleid());
	$semesters = dgr_get_semesters();
	$classes = $user->get_classes($semesters[0]['id']);
	$class = new DGradeClass($classes[0]['class_id']);
	$classload = true;
} catch ( Exception $e ) {
	$error = true;
	$classload = false;
	$errmsg = $e->getMessage();
	$style = dgr_get_style('.');
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>DGrade - <?php echo gettext('main page'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.16" />
	<link type="text/css" rel="stylesheet" href="<?php echo $style; ?>" />
	<script type="text/javascript" src="scripts/common.js"></script>
	<script type="text/javascript" src="scripts/main.js"></script>
</head>

<body onload="set_page()">
<div id="main">

<?php

if ( $error )
	dgr_error(array(gettext('Error'), $errmsg));

?>

<!-- topmenu -->
<div id="topmenu">

<div class="left">
<span><?php echo gettext('Logged in as:') . ' ' . $user->get_name() . ' ' . $user->get_surname(); ?></span>
<br />
<span><a href="index.php?logout=1"><?php echo gettext('Logout'); ?></a></span>
</div>

<div class="right">

<?php if ( $user->get_level() == 0 ) { ?>
<span class="menuitem"><a href="classes.php"><?php echo gettext('Classes'); ?></a></span>
<span class="menuitem"><a href="subjects.php"><?php echo gettext('Subjects & semesters'); ?></a></span>
<span class="menuitem"><a href="class_subjects.php"><?php echo gettext('Class subjects'); ?></a></span>
<span class="menuitem"><a href="users.php"><?php echo gettext('Users'); ?></a></span>
<?php } ?>
<span class="menuitem"><a href="settings.php"><?php echo gettext('Settings'); ?></a></span>
</div>

</div>


<!-- menu -->
<div id="nav">

<span class="menuitem left doubleline"><?php echo gettext('View:'); ?></span>
<div class="menuitem left" style="display:inline-block">
	<span>
		<input id="view1" type="radio" name="view" value="students" checked="checked" onclick="set_view()" />
		<?php echo gettext('students'); ?>
	</span>
	<br />
	<span>
		<input id="view2" type="radio" name="view" value="subjects" onclick="set_view()" />
		<?php echo gettext('subjects'); ?>
	</span>
</div>

<!-- from here in reverse order -->

<div class="menuitem right">
<span id="hrefvis" style="display:none"><?php echo ($loadclass && ($user->get_uid() == $class->get_tutorid())) ? 1 : 0; ?></span>
<span id="lvl" style="display:none"><?php echo $user->get_level(); ?></span>
<span id="managebox" style="font-size:10pt; text-align:center">
<a id="subjecthref" href="class_subjects.php?id=<?php echo $loadclass ? $class->get_id() : 0; ?>">
	<?php echo gettext('subjects'); ?>
</a>
<br />
<a id="managehref" href="classes.php?id=<?php echo $loadclass ? $class->get_id() : 0; ?>">
	<?php echo gettext('manage'); ?>
</a>
</span>
</div>

<span class="menuitem right doubleline">
	<select id="selclass" name="selclass" onchange="change_class()">
	<?php foreach ( $classes as $c ) { ?>
		<option value="<?php echo $c['class_id']; ?>"><?php echo $c['name']; ?></option>
	<?php } ?>
	</select>
</span>
<span class="menuitem right doubleline"><?php echo gettext('Class:'); ?></span>

<span class="menuitem right doubleline">
	<select id="selsemester" name="selsemester" onchange="change_semester()">
	<?php foreach ( $semesters as $sem ) { ?>
		<option value="<?php echo $sem['id']; ?>"><?php echo $sem['name']; ?></option>
	<?php } ?>
	</select>
</span>
<span class="menuitem right doubleline"><?php echo gettext('Semester:'); ?></span>

</div>

<br />

<div id="maincontainer">
<div id="choosebox">
</div>

<div id="workarea">
</div>

</div>

</div>
</body>
</html>
