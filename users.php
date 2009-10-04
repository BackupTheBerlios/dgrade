<?php
/*
 *      users.php
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

$users = dgr_get_users();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>DGrade - <?php echo gettext('users'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.18" />
	<link type="text/css" rel="stylesheet" href="<?php echo $style; ?>" />
	<script type="text/javascript" src="scripts/common.js"></script>
	<script type="text/javascript" src="scripts/manage.js"></script>
	<script type="text/javascript" src="scripts/validate.js"></script>
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

<span class="menuitem"><a href="subjects.php"><?php echo gettext('Manage subjects'); ?></a></span>
<span class="menuitem"><a href="class_subjects.php"><?php echo gettext('Manage class subjects'); ?></a></span>
<span class="menuitem"><a href="users.php"><?php echo gettext('Manage users'); ?></a></span>

<span class="menuitem"><a href="settings.php"><?php echo gettext('Settings'); ?></a></span>
</div>

</div>


<!-- menu -->
<div id="nav">

</div>

<br />

<div id="maincontainer">

<div id="choosebox">

<span id="chooseheader"><?php echo gettext('Users'); ?></span>
<span id="choosesel" style="display:none">1</span>
<span id="chooseid" style="display:none">0</span>

<table cellpadding="0" cellspacing="0" width="100%">

<?php
$i = 0;
foreach ( $users as $u ) {
	$tr = 'choose' . ++$i;
?>

<tr id="<?php echo $tr; ?>" onmouseover="make_pointer(this)" onclick="change_user_selection(<?php echo $i; ?>, <?php echo $u['uid'] ?>)">
	<td style="width:15%" class="choosetd"><?php echo $i; ?></td>
	<td class="choosetd"><?php echo $u['name']; ?></td>
	<td class="choosetd"><?php echo $u['surname']; ?></td>
</tr>

<?php } ?>

<tr>
	<td class="choosetd" colspan="3">&nbsp;</td>
</tr>

<tr id="<?php echo 'choose' .  ++$i; ?>" onmouseover="make_pointer(this)" onclick="change_user_selection(<?php echo $i; ?>, 0)">
	<td class="choosetd" style="text-align:center" colspan="3"><?php echo gettext('new user'); ?></td>
</tr>

</table>

</div>

<div id="workarea">

<span id="inputdisabled" style="display:none">1</span>

<table style="padding-top:64px" cellpadding="8" width="50%" class="centered">
	<tr>
		<td><?php echo gettext('Login:'); ?></td>
		<td><input type="text" id="login" disabled="disabled" maxlength="30" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Password:'); ?></td>
		<td><input type="password" id="pass" disabled="disabled" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Name:'); ?></td>
		<td><input type="text" id="user_name" disabled="disabled" maxlength="30" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Surname:'); ?></td>
		<td><input type="text" id="user_surname" disabled="disabled" maxlength="30" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Email:'); ?></td>
		<td><input type="text" id="email" disabled="disabled" maxlength="30" onblur="validate_email()" /></td>
		<td>
			<img id="emailvalid" class="hiddenimg" src="img/valid.png" alt="valid" />
			<img id="emailbad" class="hiddenimg" src="img/bad.png" alt="bad" />
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('Level:'); ?></td>
		<td>
		<select id="levelsel" disabled="disabled">
			<option value="0"><?php echo gettext('Administrator'); ?></option>
			<option value="1"><?php echo gettext('Viewer'); ?></option>
			<option value="2"><?php echo gettext('Teacher'); ?></option>
		</select>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td style="text-align:center">
			<input type="button" id="addbutton" value="Save" disabled="disabled" onclick="save_user()" />
		</td>
		<td style="text-align:center">
			<input type="button" id="delbutton" value="Delete" disabled="disabled" onclick="delete_user()" />
		</td>
	</tr>
</table>

</div>

</div>

</div>
</body>
</html>
