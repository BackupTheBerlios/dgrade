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

require_once dirname(__FILE__) . '/common.php';

dgr_require('/includes/user.php');


$bad_login = false;
$errmsg = '';

if ( isset($_GET['logout']) ) {
	try {
		$user = new DGradeUser();
		$user->logout();
		$user = null;
	} catch ( Exception $e ) {
		/* do nothing */
	}
	dgr_redirect('index.php');
}

if ( isset($_POST['dgrade_login']) ) {
	try {
		$user = new DGradeUser($_POST['user'], $_POST['pass']);
	} catch ( Exception $e ) {
		$bad_login = true;
		$errmsg = $e->getMessage();
	}
	if ( ! $bad_login )
		dgr_redirect('main.php');
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>DGrade - <?php echo gettext('starting page'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.16" />
	<link type="text/css" rel="stylesheet" href="<?php echo dgr_get_style(); ?>" />
</head>

<body>
<div id="main">

<h1 class="header"><span class="rounded">DGrade</span></h1>

<?php if ( $bad_login ) { ?>
<h3 class="header error"><?php echo gettext($errmsg); ?></h3>
<?php } ?>

<form action="index.php" method="post">

<p><input type="hidden" name="dgrade_login" value="1" /></p>

<table cellpadding="8" width="40%" class="centered tableform">
	<tr>
		<td><?php echo gettext('Username:'); ?></td>
		<td><input type="text" name="user" /></td>
	</tr>
	<tr>
		<td><?php echo gettext('Password:'); ?></td>
		<td><input type="password" name="pass" /></td>
	</tr>
	<tr><td colspan="2" class="bottombordered">&nbsp;</td></tr>
	<tr>
		<td colspan="2" style="text-align:center">
			<input type="submit" value="<?php echo gettext('Submit'); ?>" />
		</td>
	</tr>
</table>

</form>

</div>
</body>
</html>
