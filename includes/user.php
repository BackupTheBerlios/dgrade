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

	public function __construct( $username = '', $pass = '' )
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
			$r = $dblink->get_user_info($username, $pass,
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

	public function __destruct()
	{
	}

	public function logout()
	{
		if ( session_id != '' || isset($_COOKIE[session_name()]) )
			setcookie(session_name(), '', time()-3600, '/');
		session_destroy();
	}

	public function get_uid()
	{
		return $_SESSION['uid'];
	}

	/* no need for set_uid, as changing it is not possible */

	public function get_name()
	{
		return $_SESSION['name'];
	}

	public function set_name( $name )
	{
		$_SESSION['name'] = $name;
	}

	public function get_surname()
	{
		return $_SESSION['surname'];
	}

	public function set_surname( $surname )
	{
		$_SESSION['surname'] = $surname;
	}

	public function get_email()
	{
		return $_SESSION['email'];
	}

	public function set_email( $email )
	{
		$_SESSION['email'] = $email;
	}

	public function get_level()
	{
		return $_SESSION['level'];
	}

	public function set_level( $level )
	{
		$_SESSION['level'] = $level;
	}

	public function get_styleid()
	{
		return $_SESSION['styleid'];
	}

	public function set_styleid( $styleid )
	{
		$styleid = (int)$styleid;
		$_SESSION['styleid'] = ($styleid > 0) ? $styleid : 1;
	}

	public function get_classid()
	{
		return $_SESSION['classid'];
	}

	public function set_classid( $id )
	{
		$_SESSION['classid'] = (int)$id;
	}

	public function save()
	{
		$dblink = DGradeDB::instance();
		$dblink->set_user_info($_SESSION['uid'], $_SESSION['name'], $_SESSION['surname'],
					$_SESSION['email'], $_SESSION['level'], $_SESSION['styleid']);
	}

	public function change_pass( $oldpass, $newpass )
	{
		$dblink = DGradeDB::instance();
		return $dblink->set_user_pass($this->get_uid(), $oldpass, $newpass);
	}

	public function get_classes( $semid )
	{
		$dblink = DGradeDB::instance();
		if ( $this->get_level() < 2 )
			$ret = $dblink->get_classes();
		else
			$ret = $dblink->get_user_classes($this->get_uid(), $semid);
		return $ret;
	}

	public function get_tutored()
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_tutored($this->get_uid());
	}
}

?>
