<?php
/*
 *      user.php
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


dgr_require('/includes/db.php');


class DGradeUser
{

	private $classes;

	function __construct( $username = '', $pass = '' )
	{
		session_start();
		if ( ! isset($_SESSION['uid']) ) {
			if ( empty($username) || empty($pass) )
				throw new Exception('Bad username or password');
			try {
				$dblink = DGradeDB::instance();
			} catch ( Exception $e ) {
				throw $e;
			}
			$r = $dblink->get_user_info($username, sha1($pass),
						$uid, $name, $surname, $email,
						$level, $styleid, $classid);
			if ( ! $r )
				throw new Exception('Bad username or password');
			$_SESSION['uid'] = $uid;
			$_SESSION['name'] = $name;
			$_SESSION['surname'] = $surname;
			$_SESSION['email'] = $email;
			$_SESSION['level'] = $level;
			$_SESSION['styleid'] = $styleid;
			$this->classes = $dblink->get_classes($uid, $level);
		}
	}

	function __destruct()
	{
	}

	function logout()
	{
		if ( session_id != '' || isset($_COOKIE[session_name()]) )
			setcookie(session_name(), '', time()-3600, '/');
		session_destroy();
	}

	function get_uid()
	{
		return $_SESSION['uid'];
	}

	/* no need for set_uid, as changing it is not possible */

	function get_name()
	{
		return $_SESSION['name'];
	}

	function set_name( $name )
	{
		$_SESSION['name'] = $name;
	}

	function get_surname()
	{
		return $_SESSION['surname'];
	}

	function set_surname( $surname )
	{
		$_SESSION['surname'] = $surname;
	}

	function get_email()
	{
		return $_SESSION['email'];
	}

	function set_email( $email )
	{
		$_SESSION['email'] = $email;
	}

	function get_level()
	{
		return $_SESSION['level'];
	}

	function set_level( $level )
	{
		$_SESSION['level'] = $level;
	}

	function get_styleid()
	{
		return $_SESSION['styleid'];
	}

	function set_styleid( $styleid )
	{
		$styleid = (int)$styleid;
		$_SESSION['styleid'] = ($styleid > 0) ? $styleid : 1;
	}

	function get_classid()
	{
		return $_SESSION['classid'];
	}

	function set_classid( $id )
	{
		$_SESSION['classid'] = (int)$id;
	}

	function save()
	{
		$dblink = DGradeDB::instance();
		$dblink->write_user_info($_SESSION['uid'], $_SESSION['name'], $_SESSION['surname'],
					$_SESSION['email'], $_SESSION['level'], $_SESSION['styleid']);
	}

	function change_pass( $oldpass, $newpass )
	{
		$dblink = DGradeDB::instance();
		return $dblink->change_user_pass($this->get_uid(), sha1($oldpass), sha1($newpass));
	}

	function get_classes( $semid )
	{
		$dblink = DGradeDB::instance();
		if ( $this->get_level() < 2 )
			$ret = $dblink->get_classes();
		else
			$ret = $dblink->get_user_classes($this->get_uid(), $semid);
		return $ret;
	}

	function get_tutored()
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_tutored($this->get_uid());
	}
}

?>
