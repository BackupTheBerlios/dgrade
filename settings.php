<?php
/*
 *      settings.php
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

$changeok = true;

if ( isset($_POST['change_settings']) ) {
	switch($_POST['change_settings']) {
		case 1:
			$user->set_name($_POST['name']);
			$user->set_surname($_POST['surname']);
			$user->set_email($_POST['email']);
			$user->set_styleid($_POST['style']);
			$user->save();
			break;
		case 2:
			$changeok = $user->change_pass($_POST['curpass'], $_POST['pass']);
			break;
		default:
			break;
	}
}

$styles = dgr_get_styles();
$current_style = $user->get_styleid();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>DGrade - <?php echo gettext('settings'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.18" />
	<link type="text/css" rel="stylesheet" href="<?php echo $style; ?>" />
	<script type="text/javascript" src="scripts/validate.js"></script>
</head>

<body>
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
<?php if ( $user->get_level() == 0 ) { ?>
<span class="menuitem"><a href="classes.php"><?php echo gettext('Classes'); ?></a></span>
<span class="menuitem"><a href="subjects.php"><?php echo gettext('Subjects & semesters'); ?></a></span>
<span class="menuitem"><a href="class_subjects.php"><?php echo gettext('Class subjects'); ?></a></span>
<span class="menuitem"><a href="users.php"><?php echo gettext('Users'); ?></a></span>
<?php } ?>
</div>

</div>


<form action="settings.php" method="post" onsubmit="return validate_email()">
<p><input type="hidden" name="change_settings" value="1" /></p>

<table cellpadding="8" width="50%" class="centered tableform">
<tr>
	<td colspan="3" class="bottombordered"style="text-align:center">
		<h3><?php echo gettext('User data'); ?></h3>
	</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr>
	<td><?php echo gettext('Name:'); ?></td>
	<td><input type="text" name="name" maxlength="30" value="<?php echo $user->get_name(); ?>" /></td>
</tr>
<tr>
	<td><?php echo gettext('Surname:'); ?></td>
	<td><input type="text" name="surname" maxlength="30" value="<?php echo $user->get_surname(); ?>" /></td>
</tr>
<tr>
	<td><?php echo gettext('E-mail:'); ?></td>
	<td>
	<input id="email" type="text" name="email" maxlength="30" onblur="validate_email()" value="<?php echo $user->get_email(); ?>" />
	</td>
	<td>
		<img id="emailvalid" class="hiddenimg" src="img/valid.png" alt="valid" />
		<img id="emailbad" class="hiddenimg" src="img/bad.png" alt="bad" />
	</td>
</tr>
<tr>
	<td><?php echo gettext('Style:'); ?></td>
	<td>
		<select name="style">
		<?php foreach ( $styles as $s ) if ( $s['id'] == $current_style ) { ?>
			<option value="<?php echo $s['id']; ?>" selected="selected">
				<?php echo $s['name']; ?>
			</option>
		<?php } else { ?>
			<option value="<?php echo $s['id']; ?>">
				<?php echo $s['name']; ?>
			</option>
		<?php } ?>
		</select>
	</td>
</tr>
<tr><td colspan="3" class="bottombordered">&nbsp;</td></tr>
<tr>
	<td colspan="3" style="text-align:center">
		<input type="submit" value="<?php echo gettext('Submit'); ?>" />
	</td>
</tr>
</table>
</form>

<br />

<?php if ( ! $changeok ) { ?>
<h2 class="error" style="text-align:center">Bad password!</h2>
<?php } ?>

<form action="settings.php" method="post" onsubmit="return validate_pass()">
<p><input type="hidden" name="change_settings" value="2" /></p>

<table cellpadding="8" width="50%" class="centered tableform">
	<tr>
		<td colspan="3" class="bottombordered"style="text-align:center">
			<h3><?php echo gettext('Change password'); ?></h3>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td><?php echo gettext('Current password:'); ?></td>
		<td><input type="password" name="curpass" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('New password:'); ?></td>
		<td><input id="pass" type="password" name="pass" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Confirm new password:'); ?></td>
		<td><input id="passconf" type="password" name="passconf" onblur="validate_pass()" /></td>
		<td>
			<img id="passvalid" class="hiddenimg" src="img/valid.png" alt="valid" />
			<img id="passbad" class="hiddenimg" src="img/bad.png" alt="bad" />
		</td>
	</tr>
	<tr><td colspan="3" class="bottombordered">&nbsp;</td></tr>
	<tr>
		<td colspan="3" style="text-align:center">
			<input type="submit" value="<?php echo gettext('Submit'); ?>" />
		</td>
	</tr>
</table>

</form>

<br />

</div>
</body>
</html>
