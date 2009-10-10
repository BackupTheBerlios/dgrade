<?php
/*
 *      common.php
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

/* dgr_require defined below */
dgr_require('/config.php');
dgr_require('/includes/db.php');


function dgr_require( $path )
{
	require_once dirname(__FILE__) . $path;
}

function dgr_redirect( $path )
{
	$host = $_SERVER['HTTP_HOST'];
	$uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	header("Location: http://{$host}{$uri}/{$path}");
	exit;
}

function dgr_startup()
{
	setlocale(LC_ALL, DGRADE_LANG);
	bindtextdomain('dgrade', dirname(__FILE__) . '/locale');
	textdomain('dgrade');
}

function dgr_error( $msg )
{
	$level = 1;
	foreach ( $msg as $m ) {
		echo "<h${level} class=\"header\">" . $m . "</h${level}>";
		if ( $level < 6 )
			$level++;
	}
	die('</div></body></html>');
}

function dgr_get_style( $prefix = '.', $styleid = 1 )
{
	try {
		$dblink = DGradeDB::instance();
	} catch ( Exception $e ) {
		return $prefix . '/styles/' . DGRADE_DEFAULT_STYLE;
	}
	$name = $dblink->get_style_name($styleid);
	return $prefix . '/styles/' . $name;
}

function dgr_get_styles()
{
	try {
		$dblink = DGradeDB::instance();
	} catch ( Exception $e ) {
		return array(1, DGRADE_DEFAULT_STYLE);
	}
	return $dblink->get_styles();
}

function dgr_get_classes()
{
	try {
		$dblink = DGradeDB::instance();
	} catch ( Exception $e ) {
		return array();
	}
	return $dblink->get_classes();
}

function dgr_get_users( $fold = false )
{
	try {
		$dblink = DGradeDB::instance();
	} catch ( Exception $e ) {
		return array();
	}
	return $dblink->get_users_brief($fold);
}

function dgr_get_semesters()
{
	try {
		$dblink = DGradeDB::instance();
	} catch ( Exception $e ) {
		return array();
	}
	return $dblink->get_semesters();
}

function dgr_strip_whitespaces( $str )
{
	$ws = array(' ', '\t', '\n', '\r', '\0', '\x0B');
	return str_replace($ws, '', $str);
}

function dgr_get_subjects()
{
	try {
		$dblink = DGradeDB::instance();
	} catch ( Exception $e ) {
		return array();
	}
	return $dblink->get_subjects();
}

?>
