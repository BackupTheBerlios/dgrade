<?php
/*
 *      class.php
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


class DGradeClass
{

	private $id;
	private $name;
	private $startyear;
	private $tutorid;

	public $students;

	function __construct( $classid )
	{
		try {
			$dblink = DGradeDB::instance();
		} catch ( Exception $e ) {
			throw $e;
		}
		$r = $dblink->get_class($classid, $this->name, $this->startyear, $this->tutorid,
						$this->students);
		if ( ! $r )
			throw new Exception($dblink->get_error());
		$this->id = $classid;
	}

	function __destruct()
	{
	}

	function get_id()
	{
		return $this->id;
	}

	function get_name()
	{
		return $this->name;
	}

	function set_name( $n )
	{
		$this->name = $n;
	}

	function get_startyear()
	{
		return $this->startyear;
	}

	function set_startyear( $y )
	{
		$this->startyear = (int)$y;
	}

	function get_tutorid()
	{
		return $this->tutorid;
	}

	function set_tutorid( $id )
	{
		$this->tutorid = (int)$id;
	}

	function save()
	{
		$dblink = DGradeDB::instance();
		$dblink->write_class_info($this->id, $this->name, $this->startyear, $this->tutorid);
	}

	function get_subjects( $semid )
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_class_subjects( $this->id, $semid );
	}

	function get_student_brief( $id )
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_student_info_brief( $id );
	}

}

?>
