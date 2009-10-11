<?php
/*
 *      classes.php
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
	$users = dgr_get_users(true);
	if ( isset($_GET['id']) && $_GET['id'] > 0 ) {
		$classid = $_GET['id'];
		$loadclass = 'true';
	} else {
		$classid = 0;
		$loadclass = 'false';
	}
} else {
	$classid = $user->get_tutored();
	if ( $classid == 0 )
		die();
	$loadclass = 'true';
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>DGrade - <?php echo gettext('classes'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.18" />
	<link type="text/css" rel="stylesheet" href="<?php echo $style; ?>" />
	<script type="text/javascript" src="scripts/common.js"></script>
	<script type="text/javascript" src="scripts/manage.js"></script>
	<script type="text/javascript" src="scripts/validate.js"></script>
</head>

<body onload="set_page(<?php echo $loadclass; ?>)">
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
<span class="menuitem"><a href="subjects.php"><?php echo gettext('Subjects & semesters'); ?></a></span>
<span class="menuitem"><a href="class_subjects.php"><?php echo gettext('Class subjects'); ?></a></span>
<span class="menuitem"><a href="users.php"><?php echo gettext('Users'); ?></a></span>
<?php } ?>

<span class="menuitem"><a href="settings.php"><?php echo gettext('Settings'); ?></a></span>
</div>

</div>


<!-- menu -->
<div id="nav">

<?php
if ( $user->get_level() == 0 ) { ?>

<span class="menuitem left doubleline"><?php echo gettext('Class'); ?>:</span>

<span class="menuitem left doubleline">
	<select id="selclass" name="selclass" onchange="change_managed_class(this.options[this.selectedIndex].value)">
	<option value="0" <?php if ( $classid == 0 ) echo 'selected="selected"'; ?>>
		<?php echo gettext('new class'); ?>
	</option>
	<?php foreach ( $classes as $c ) { ?>
		<option value="<?php echo $c['class_id']; ?>" <?php if ( $classid == $c['class_id'] ) echo 'selected="selected"'; ?>>
			<?php echo $c['name']; ?>
		</option>
	<?php } ?>
	</select>
	<a style="font-size:10pt" href="#" onclick="delete_class()"><?php echo gettext('delete'); ?></a>
</span>

<?php } ?>

<span id="selclassid" style="display:none"><?php echo $classid; ?></span>

<div class="menuitem right">

<span class="doubleline"><?php echo gettext('Class name'); ?>:</span>
<span class="doubleline">
	<input id="classname" type="text" maxlength="30" size="15" name="name" />
</span>

<span class="doubleline"><?php echo gettext('Starting year'); ?>:</span>
<span class="doubleline">
	<input id="classyear" type="text" maxlength="4" size="4" name="year" value="<?php echo date('Y'); ?>" />
</span>

<?php if ( $user->get_level() == 0 ) { ?>
<span class="doubleline"><?php echo gettext('Tutor'); ?>:</span>
<span class="doubleline">
	<select id="classtutor" name="tutor" onchange="change_tutor(this.options[this.selectedIndex].value)">
	<?php foreach ( $users as $u ) { ?>
		<option value="<?php echo $u['id']; ?>"><?php echo $u['name']; ?></option>
	<?php } ?>
	</select>
</span>
<?php } ?>

<span class="doubleline">
	<a style="font-size:10pt" href="#" onclick="save_class()"><?php echo gettext('save'); ?></a>
</span>

</div>

<span id="selecttutor" style="display:none"><?php echo $user->get_uid(); ?></span>

</div>

<br />

<div id="maincontainer">

<div id="choosebox">
</div>

<div id="workarea">

<span id="inputdisabled" style="display:none">1</span>

<h1 id="headername" class="header">&nbsp;</h1>

<table cellpadding="8" width="50%" class="centered">
	<tr>
		<td><?php echo gettext('Name'); ?>:</td>
		<td><input type="text" id="student_name" disabled="disabled" maxlength="30" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Surname'); ?>:</td>
		<td><input type="text" id="student_surname" disabled="disabled" maxlength="30" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('E-mail'); ?>:</td>
		<td><input type="text" id="email" disabled="disabled" maxlength="30" onblur="validate_email()" /></td>
		<td>
			<img id="emailvalid" class="hiddenimg" src="img/valid.png" alt="valid" />
			<img id="emailbad" class="hiddenimg" src="img/bad.png" alt="bad" />
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('Parent e-mail'); ?>:</td>
		<td><input type="text" id="paremail" disabled="disabled" maxlength="30" onblur="validate_parent_email()" /></td>
		<td>
			<img id="paremailvalid" class="hiddenimg" src="img/valid.png" alt="valid" />
			<img id="paremailbad" class="hiddenimg" src="img/bad.png" alt="bad" />
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="text-align:center">
			<input type="button" id="addbutton" value="<?php echo gettext('Save'); ?>" disabled="disabled" onclick="save_student()" />
		</td>
		<td style="text-align:center">
			<input type="button" id="delbutton" value="<?php echo gettext('Delete'); ?>" disabled="disabled" onclick="delete_student()" />
		</td>
	</tr>
</table>

</div>

</div>

</div>
</body>
</html>
