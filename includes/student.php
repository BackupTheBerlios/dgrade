<?php
/*
 *      student.php
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


class DGradeStudent
{

	private $id;

	function __construct( $id )
	{
		$this->id = $id;
	}

	function __destruct()
	{
	}

	function get_id()
	{
		return $this->id;
	}

	function get_info()
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_student_info($this->id);
	}

	function save_info( $name, $surname, $email, $paremail )
	{
		$dblink = DGradeDB::instance();
		return $dblink->save_student_info($this->id, $name, $surname, $email, $paremail);
	}

	function get_tutorid()
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_student_tutor($this->id);
	}

	function get_semester_grades( $semid )
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_grades($this->id, $semid);
	}

	function get_attendance( $semid, &$att )
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_attendance($this->id, $semid, $att);
	}

	function get_subject_grades( $subid, $semid )
	{
		$dblink = DGradeDB::instance();
		return $dblink->get_subject_grades($this->id, $subid, $semid);
	}

	function send( $semid, $from )
	{
		$dblink = DGradeDB::instance();
		$info = $dblink->get_student_info($this->id);
		if ( empty($info['email']) && empty($info['parent_email']) )
			return false;
		$semname = $dblink->get_semester_name($semid);
		$grades = $dblink->get_grades($this->id, $semid);
		$msg = '';
		$semestral = gettext('Semestral');
		$notes = gettext('Notes');
		foreach ( $grades as $g ) {
			$msg .= $g['name'] . ': ' . $g['grades'] . "\n";
			if ( ! empty($g['semestral']) )
				$msg .= $semestral . ': ' . $g['semestral'] . "\n";
			if ( ! empty($g['notes']) )
				$msg .= $notes . ': ' . $g['notes'] . "\n";
			$msg .= "\n";
		}
		$subject = "[dgrade] {$info['name']} {$info['surname']} - {$semname}";
		$msg = wordwrap($msg);
		$headers = "From: {$from}\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=utf-8\r\n";
		$headers .= "Content-Transfer-Encoding: quoted-printable\r\n"; 
		$ret = true;
		if ( ! empty($info['email']) )
			$ret = $ret && mail($info['email'], $subject, $msg, $headers);
		if ( ! empty($info['parent_email']) )
			$ret = $ret && mail($info['parent_email'], $subject, $msg, $headers);
		return $ret;
	}
}

?>
