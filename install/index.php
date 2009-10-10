<?php
/*
 *      index.php
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

dgr_startup();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo gettext('DGrade - installation'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.16" />
	<link type="text/css" rel="stylesheet" href="<?php echo dgr_get_style('..'); ?>" />
	<script type="text/javascript" src="../scripts/validate.js"></script>
</head>

<body>

<div id="main">

<h1 class="header"><span class="rounded"><?php echo gettext('DGrade - installation'); ?></span></h1>

<form action="do_install.php" method="post" onsubmit="return check_install_form()">

<p><input name="dgrade_install" type="hidden" value="1" /></p>

<table cellpadding="8" width="60%" class="centered tableform">
	<tr>
		<td><?php echo gettext('Administrator username'); ?>:</td>
		<td><input id="user" name="user" type="text" value="admin" maxlength="30" onblur="validate_user()" /></td>
		<td>
			<img id="uservalid" src="../img/valid.png" alt="valid" />
			<img id="userbad" class="hiddenimg" src="../img/bad.png" alt="bad" />
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('E-mail'); ?>:</td>
		<td><input id="email" name="email" type="text" maxlength="30" onblur="validate_email()" /></td>
		<td>
			<img id="emailvalid" class="hiddenimg" src="../img/valid.png" alt="valid" />
			<img id="emailbad" class="hiddenimg" src="../img/bad.png" alt="bad" />
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('Password'); ?>:</td>
		<td><input id="pass" name="pass" type="password" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Confirm password'); ?>:</td>
		<td><input id="passconf" name="passconf" type="password" onblur="validate_pass()" /></td>
		<td>
			<img id="passvalid" class="hiddenimg" src="../img/valid.png" alt="valid" />
			<img id="passbad" class="hiddenimg" src="../img/bad.png" alt="bad" />
		</td>
	</tr>
	<tr>
		<td><?php echo gettext('Clear database'); ?>:</td>
		<td><input name="cleardb" type="checkbox" value="yes" /></td>
	</tr>
	<tr><td colspan="3" class="bottombordered">&nbsp;</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td><?php echo gettext('Name'); ?>:</td>
		<td><input name="name" type="text" maxlength="30" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Surname'); ?>:</td>
		<td><input name="surname" type="text" maxlength="30" /></td>
	</tr>
	<tr><td colspan="3" class="bottombordered">&nbsp;</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td colspan="3" style="text-align:center">
			<input id="submit" type="submit" value="<?php echo gettext('Submit'); ?>" />
		</td>
	</tr>
</table>

</form>

</div>

</body>
</html>
