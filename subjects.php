<?php
/*
 *      subjects.php
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

if ( $user->get_level() != 0 )
	die();

$semesters = dgr_get_semesters();
$subjects = dgr_get_subjects();

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

<body onload="set_page(false)">
<div id="main">

<!-- topmenu -->
<div id="topmenu">

<div class="left">
<span><?php echo gettext('Logged in as:') . ' ' . $user->get_name() . ' ' . $user->get_surname(); ?></span>
<br />
<span><a href="index.php?logout=1"><?php echo gettext('Logout'); ?></a></span>
</div>

<div class="right">

<span class="menuitem"><a href="main.php"><?php echo gettext('Main page'); ?></a></span>

<span class="menuitem"><a href="classes.php"><?php echo gettext('Classes'); ?></a></span>
<span class="menuitem"><a href="class_subjects.php"><?php echo gettext('Class subjects'); ?></a></span>
<span class="menuitem"><a href="users.php"><?php echo gettext('Users'); ?></a></span>

<span class="menuitem"><a href="settings.php"><?php echo gettext('Settings'); ?></a></span>
</div>

</div>

<!-- menu -->
<div id="nav">

<span class="menuitem left doubleline"><?php echo gettext('Semester:'); ?></span>

<span class="menuitem left doubleline">
	<select id="selsemester" name="selclass" onchange="change_semester(this.options[this.selectedIndex].value)">
	<option value="0" selected="selected">
		<?php echo gettext('new semester'); ?>
	</option>
	<?php foreach ( $semesters as $sem ) { ?>
		<option value="<?php echo $sem['id']; ?>">
			<?php echo $sem['name']; ?>
		</option>
	<?php } ?>
	</select>
	<a style="font-size:10pt" href="#" onclick="delete_semester()"><?php echo gettext('delete'); ?></a>
</span>

<!-- reverse order -->

<span class="menuitem right doubleline">
	<a href="#" onclick="save_semester()"><?php echo gettext('save'); ?></a>
</span>

<span class="menuitem right doubleline">
	<input id="semname" type="text" maxlength="30" name="name" />
</span>

<span class="menuitem right doubleline"><?php echo gettext('Name:'); ?></span>

<span class="menuitem right doubleline">
	<input id="semend" type="text" maxlength="10" size="10" />
</span>
<span class="menuitem right doubleline"><?php echo gettext('End:'); ?></span>

<span class="menuitem right doubleline">
	<input id="semstart" type="text" maxlength="10" size="10" />
</span>
<span class="menuitem right doubleline"><?php echo gettext('Start:'); ?></span>

</div>

<br />

<div id="maincontainer">
<div id="choosebox">


<span id="chooseheader"><?php echo gettext('Subjects'); ?></span>
<span id="choosesel" style="display:none">1</span>
<span id="chooseid" style="display:none">0</span>

<table cellpadding="0" cellspacing="0" width="100%">

<?php
$i = 0;
foreach ( $subjects as $sub ) {
	$tr = 'choose' . ++$i;
?>

<tr id="<?php echo $tr; ?>" onmouseover="make_pointer(this)" onclick="change_subject_selection(<?php echo $i; ?>, <?php echo $sub['subject_id'] ?>, '<?php echo $sub['name']; ?>')">
	<td style="width:15%" class="choosetd"><?php echo $i; ?></td>
	<td class="choosetd"><?php echo $sub['name']; ?></td>
</tr>

<?php } ?>

<tr>
	<td class="choosetd" colspan="2">&nbsp;</td>
</tr>

<tr id="<?php echo 'choose' .  ++$i; ?>" onmouseover="make_pointer(this)" onclick="change_subject_selection(<?php echo $i; ?>, 0, '')">
	<td class="choosetd" style="text-align:center" colspan="2"><?php echo gettext('new subject'); ?></td>
</tr>

</table>

</div>

<div id="workarea">
<span id="inputdisabled" style="display:none">1</span>

<table style="padding-top:64px" cellpadding="8" width="50%" class="centered">
	<tr>
		<td><?php echo gettext('Name:'); ?></td>
		<td><input type="text" id="sub_name" disabled="disabled" maxlength="30" /></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="text-align:center">
			<input type="button" id="addbutton" value="Save" disabled="disabled" onclick="save_subject()" />
		</td>
		<td style="text-align:center">
			<input type="button" id="delbutton" value="Delete" disabled="disabled" onclick="delete_subject()" />
		</td>
	</tr>
</table>
</div>

</div>


</div>

</body>
</html>
