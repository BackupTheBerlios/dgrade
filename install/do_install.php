<?php
/*
 *      install.php
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

if ( ! isset($_POST['dgrade_install']) )
	dgr_redirect('install.php');

dgr_startup();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo gettext('DGrade - Error'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.16" />
	<link type="text/css" rel="stylesheet" href="<?php echo dgr_get_style('..'); ?>" />
</head>

<body>

<div id="main">


<?php

dgr_require('/includes/db.php');


function sql_tables_from_file( $filename, &$dblink, $stop_on_error = true )
{
	$sql = file($filename);
	foreach( $sql as $sqlline )
		if ( substr($sqlline, 0, 2) != "--" )
			$sql_cmd .= $sqlline;
	$sql_cmd = split(';', $sql_cmd);
	foreach( $sql_cmd as $query ) {
		$query = trim($query);
		if ( empty($query) )
			continue;
		if ( ! $dblink->query($query) && $stop_on_error )
			return false;
	}
	return true;
}


/* main */

if ( ! file_exists('drop_tables.sql') || ! file_exists('create_tables.sql') )
	dgr_error(array('Incomplete installation. Exiting...'));

try {
	$dblink = DGradeDB::instance();
} catch ( Exception $e ) {
	dgr_error(array($e->getMessage()));
}

$errmsg = '';

if ( isset($_POST['cleardb']) && $_POST['cleardb'] == 'yes' )
	sql_tables_from_file('drop_tables.sql', $dblink, false);

if ( ! sql_tables_from_file('create_tables.sql', $dblink) )
	dgr_error(array('Cannot install dGrade', $dblink->get_error()));


if ( ! $dblink->add_user($_POST['user'], $_POST['pass'], $_POST['name'], $_POST['surname'], $_POST['email'], 0) )
	dgr_error(array('Cannot install dGrade', $dblink->get_error()));

?>

<h1 class="header"><span class="rounded"><?php echo gettext('Installation successful'); ?></span></h1>
<h3 class="header"><a href="../index.php"><?php echo gettext('Log in'); ?></a></h3>

</div>
</body>
</html>
