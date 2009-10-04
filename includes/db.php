<?php
/*
 *      db.php
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

dgr_require('/config.php');


// TODO: add addslashes() and stripslashes() where necessary

class DGradeDB
{

	private $conn = null;

	private function __construct()
	{
		$this->conn = @pg_connect('host=' . DGRADE_DB_SERV . ' dbname=' . DGRADE_DB_NAME . ' user=' . DGRADE_DB_USER . ' password=' . DGRADE_DB_PASS);
		if ( ! $this->status() )
			throw new Exception('Cannot connect to database');
	}

	static public function instance()
	{
		static $i = null;
		if ( ! isset($i) )
			$i = new DGradeDB();
		return $i;
	}

	function __destruct()
	{
		@pg_close($this->conn);
	}

	function status()
	{
		return pg_connection_status($this->conn) == PG_CONNECTION_OK;
	}

	function query( $query )
	{
		//echo $query;
		return pg_query($this->conn, $query);
	}

	function get_error()
	{
		return @pg_last_error($this->conn);
	}

	function add_user( $login, $pass, $name, $surname, $email, $level = 2 )
	{
		$name = addslashes($name);
		$surname = addslashes($surname);
		$email = addslashes($email); /* just in case */
		$level = (int)$level;
		return $this->query("INSERT INTO dgr_user VALUES ( nextval('dgr_user_uid_seq'), '{$login}', '{$pass}', '{$name}', '{$surname}', '{$email}', $level, DEFAULT )");
	}

	function change_user( $id, $pass, $name, $surname, $email, $level )
	{
		$id = (int)$id;
		$name = addslashes($name);
		$surname = addslashes($surname);
		$email = addslashes($email);
		$level = (int)$level;
		$r = $this->query("UPDATE dgr_user SET name='{$name}', surname='{$surname}', email='{$email}', lvl={$level} WHERE uid={$id}");
		$ret = $r ? true : false;
		if ( ! empty($pass) ) {
			$pass = sha1($pass);
			$r = $this->query("UPDATE dgr_user SET passhash='{$pass}' WHERE uid={$id}");
			$ret = $ret && ($r ? true : false);
		}
		return $ret;
	}

	function delete_user( $id )
	{
		$id = (int)$id;
		return $this->query("DELETE FROM dgr_user WHERE uid={$id}");
	}

	function get_style_name( $styleid )
	{
		$styleid = (int)$styleid;
		$r = $this->query("SELECT name FROM dgr_style WHERE id={$styleid}");
		if ( ! $r || pg_num_rows($r) <= 0 )
			return DGRADE_DEFAULT_STYLE;
		$name = pg_fetch_row($r);
		return stripslashes($name[0]);
	}

	function get_user_info( $user, $pass, &$uid, &$name, &$surname, &$email, &$level, &$style )
	{
		$r = $this->query("SELECT uid, name, surname, email, lvl, style FROM dgr_user WHERE login='{$user}' AND passhash='{$pass}'");
		if ( ! $r || pg_num_rows($r) <= 0 )
			return false;
		$info = pg_fetch_row($r);
		$uid = $info[0];
		$name = stripslashes($info[1]);
		$surname = stripslashes($info[2]);
		$email = stripslashes($info[3]);
		$level = $info[4];
		$style = $info[5];

		return true;
	}

	function get_user_details( $uid )
	{
		$uid = (int)$uid;
		$r = $this->query("SELECT login, name, surname, email, lvl FROM dgr_user WHERE uid={$uid}");
		if ( ! $r )
			return array();
		$row = pg_fetch_assoc($r);
		return $row;
	}

	function write_user_info( $uid, $name, $surname, $email, $level, $styleid )
	{
		$name = addslashes($name);
		$surname = addslashes($surname);
		$email = addslashes($email);
		$r = $this->query("UPDATE dgr_user SET name='{$name}', surname='{$surname}', email='{$email}', lvl={$level}, style={$styleid} WHERE uid={$uid}");
		return $r ? true : false;
	}

	function get_class( $classid, &$name, &$startyear, &$tutorid, &$students )
	{
		$classid = (int)$classid;
		$r = $this->query("SELECT name, startyear, tutor_id from dgr_class WHERE class_id={$classid}");
		if ( ! $r || pg_num_rows($r) < 1 ) {
			return false;
		}
		$info = pg_fetch_row($r);
		$name = stripslashes($info[0]);
		$startyear = $info[1];
		$tutorid = $info[2];

		$r = $this->query("SELECT id FROM dgr_student WHERE class_id={$classid} ORDER BY id");
		if ( ! $r )
			return false;
		$students = array();
		while ( $row = pg_fetch_row($r) )
			$students[] = $row[0];

		return true;
	}

	function get_styles()
	{
		$r = $this->query("SELECT id, name FROM dgr_style");
		if ( ! $r )
			return array(1, DGRADE_DEFAULT_STYLE);
		$ret = array();
		while ( $row = pg_fetch_assoc($r) ) {
			$row['name'] = stripslashes($row['name']);
			$ret[] = $row;
		}
		return $ret;
	}

	function change_user_pass( $uid, $oldhash, $newhash )
	{
		$r = $this->query("SELECT passhash FROM dgr_user WHERE uid={$uid}");
		$row = pg_fetch_row($r);
		if ( strcmp($row[0], $oldhash) != 0 )
			return false;
		$r = $this->query("UPDATE dgr_user SET passhash='{$newhash}' WHERE uid={$uid}");
		return $r ? true : false;
	}

	function get_classes()
	{
		$ret = array();
		$r = $this->query("SELECT class_id, name FROM dgr_class ORDER BY class_id");
		if ( ! $r )
			return $ret;
		while ( $row = pg_fetch_assoc($r) ) {
			$ret[] = $row;
		}
		return $ret;
	}

	function get_class_year( $classid )
	{
		$r = $this->query("SELECT startyear FROM dgr_class WHERE class_id={$classid}");
		if ( ! $r )
			return '';
		$row = pg_fetch_row($r);
		return $row[0];
	}

	function get_users_brief( $fold = false )
	{
		$r = $this->query("SELECT uid, name, surname FROM dgr_user");
		if ( ! $r )
			return array();
		$ret = array();
		while ( $row = pg_fetch_assoc($r) ) {
			if ( $fold ) {
				$usr = array();
				$usr['id'] = $row['uid'];
				$usr['name'] = $row['name'] . ' ' . $row['surname'];
				$ret[] = $usr;
			} else
				$ret[] = $row;
		}
		return $ret;
	}

	function get_class_tutor( $classid )
	{
		$r = $this->query("SELECT tutor_id FROM dgr_class WHERE class_id={$classid}");
		if ( ! $r )
			return 1;
		$row = pg_fetch_row($r);
		return $row[0];
	}

	function get_user_classes( $uid, $semid )
	{
		$r = $this->query("SELECT class_id, name FROM dgr_class WHERE tutor_id = {$uid}");
		$ret = array();
		if ( $r ) {
			$row = pg_fetch_assoc($r);
			$ret[] = $row;
		}
		$r = $this->query("SELECT DISTINCT class_id, name FROM dgr_class JOIN dgr_subject_semester USING (class_id) WHERE uid={$uid} AND semester_id={$semid}");
		if ( ! $r )
			return $ret;
		while ( $row = pg_fetch_assoc($r) )
			$ret[] = $row;
		return $ret;
	}

	function get_tutored( $uid )
	{
		$r = $this->query("SELECT class_id FROM dgr_class WHERE tutor_id={$uid}");
		if ( ! $r )
			return 0;
		$row = pg_fetch_row($r);
		return $row[0];
	}

	function get_student_info_brief( $id )
	{
		$r = $this->query("SELECT id, name, surname FROM dgr_student WHERE id={$id}");
		if ( ! $r )
			return array();
		return pg_fetch_assoc($r);
	}

	function get_student_info( $id )
	{
		$r = $this->query("SELECT name, surname, email, parent_email FROM dgr_student WHERE id={$id}");
		if ( ! $r )
			return array();
		return pg_fetch_assoc($r);
	}

	function get_class_subjects( $classid, $semid )
	{
		$r = $this->query("SELECT id, name, uid FROM dgr_subject_semester JOIN dgr_subject USING (subject_id) WHERE semester_id={$semid} AND class_id={$classid}");
		$ret = array();
		while ( $row = pg_fetch_assoc($r) )
			$ret[] = $row;
		return $ret;
	}

	function get_semesters()
	{
		$r = $this->query("SELECT id, name FROM dgr_semester ORDER BY id DESC");
		if ( ! $r )
			return array();
		$ret = array();
		while ( $row = pg_fetch_assoc($r) )
			$ret[] = $row;
		return $ret;
	}

	function get_semester_name( $semid )
	{
		$semid = (int)$semid;
		$r = $this->query("SELECT name FROM dgr_semester WHERE id={$semid}");
		if ( ! $r )
			return '';
		$row = pg_fetch_row($r);
		return $row[0];
	}

	function get_grades( $id, $semid )
	{
		$r = $this->query("SELECT dgr_grade.id, grades, semestral, notes, name, descriptive_grade, block_teacher, uid FROM dgr_grade JOIN dgr_subject_semester ON dgr_grade.subject_id=dgr_subject_semester.id JOIN dgr_subject ON dgr_subject_semester.subject_id=dgr_subject.subject_id WHERE student_id={$id} AND semester_id={$semid} ORDER BY dgr_grade.id");
		if ( ! $r )
			return array();
		$ret = array();
		while ( $row = pg_fetch_assoc($r) )
			$ret[] = $row;
		return $ret;
	}

	function get_subject_grades( $id, $subid, $semid )
	{
		$r = $this->query("SELECT dgr_grade.id, grades, semestral, notes, descriptive_grade, block_teacher FROM dgr_grade JOIN dgr_subject_semester ON dgr_grade.subject_id=dgr_subject_semester.id WHERE dgr_grade.subject_id={$subid} AND semester_id={$semid} AND student_id={$id}");
		if ( ! $r )
			return array();
		$row = pg_fetch_assoc($r);
		return $row;
	}

	function get_attendance( $id, $semid, &$att )
	{
		$r = $this->query("SELECT id, day_start, day_end, absent, explained, late FROM dgr_attendance WHERE student_id={$id} AND semester_id={$semid} ORDER BY day_end DESC");
		$total = array( 'absent' => 0, 'explained' => 0, 'late' => 0 );
		if ( ! $r )
			return $total;
		while ( $row = pg_fetch_assoc($r) ) {
			$att[] = $row;
			$total['absent'] += $row['absent'];
			$total['explained'] += $row['explained'];
			$total['late'] += $row['late'];
		}
		return $total;
	}

	function get_attendance_info( $id )
	{
		$r = $this->query("SELECT absent, explained, late FROM dgr_attendance WHERE id={$id}");
		if ( ! $r )
			return array();
		$row = pg_fetch_assoc($r);
		return $row;
	}

	function get_student_tutor( $id )
	{
		$r = $this->query("SELECT tutor_id FROM dgr_student JOIN dgr_class USING (class_id) WHERE id={$id}");
		if ( ! $r )
			return 0;
		$row = pg_fetch_row($r);
		return $row[0];
	}

	function add_class( $name, $startyear, $tutorid )
	{
		$name = addslashes($name);
		$startyear = (int)$startyear;
		$tutorid = (int)$tutorid;
		return $this->query("INSERT INTO dgr_class VALUES ( nextval('dgr_class_class_id_seq'), '{$name}', {$startyear}, {$tutorid} )");
	}

	function write_class_info( $id, $name, $startyear, $tutorid )
	{
		$name = addslashes($name);
		$r = $this->query("UPDATE dgr_class SET name='{$name}', startyear={$startyear}, tutor_id={$tutorid} WHERE class_id={$id}");
		return $r ? true : false;
	}

	function delete_class( $id )
	{
		return $this->query("DELETE FROM dgr_class WHERE class_id={$id}");
	}

	function can_modify_grade( $id, $uid )
	{
		$r = $this->query("SELECT tutor_id FROM dgr_grade JOIN dgr_student ON dgr_grade.student_id=dgr_student.id JOIN dgr_class USING (class_id) WHERE dgr_grade.id={$id}");
		$row = pg_fetch_row($r);
		if ( $row[0] == $uid )
			return true;
		$r = $this->query("SELECT uid FROM dgr_grade JOIN dgr_subject_semester ON dgr_grade.subject_id=dgr_subject_semester.id WHERE dgr_grade.id={$id}");
		$row = pg_fetch_row($r);
		if ( $row[0] == $uid )
			return true;
		return false;
	}

	function modify_grade( $id, $grades, $notes, $semestral )
	{
		$grades = addslashes($grades);
		$notes = addslashes($notes);
		$semestral = addslashes($semestral);
		$r = $this->query("UPDATE dgr_grade SET grades='{$grades}', notes='{$notes}', semestral='{$semestral}' WHERE id={$id}");
		return $r ? true : false;
	}

	function can_modify_attendance( $id, $uid )
	{
		$id = (int)$id;
		$uid = (int)$uid;
		$r = $this->query("SELECT tutor_id FROM dgr_attendance JOIN dgr_student ON dgr_attendance.student_id=dgr_student.id JOIN dgr_class USING (class_id) WHERE dgr_attendance.id={$id}");
		$row = pg_fetch_row($r);
		return $row[0] == $uid;
	}

	function modify_attendance( $id, $absent, $explained, $late )
	{
		$id = (int)$id;
		$absent = (int)$absent;
		$explained = (int)$explained;
		$late = (int)$late;
		$r = $this->query("UPDATE dgr_attendance SET absent={$absent}, explained={$explained}, late={$late} WHERE id={$id}");
		return $r ? true : false;
	}

	function add_student( $classid, $name, $surname, $email, $paremail )
	{
		$classid = (int)$classid;
		$name = addslashes($name);
		$surname = addslashes($surname);
		$email = addslashes($email);
		$paremail = addslashes($paremail);
		return $this->query("INSERT INTO dgr_student VALUES ( nextval('dgr_student_id_seq'), '{$name}', '{$surname}', '{$email}', '{$paremail}', {$classid} )");
	}

	function save_student_info( $id, $name, $surname, $email, $paremail )
	{
		$id = (int)$id;
		$name = addslashes($name);
		$surname = addslashes($surname);
		$email = addslashes($email);
		$paremail = addslashes($paremail);
		$r = $this->query("UPDATE dgr_student SET name='{$name}', surname='{$surname}', email='{$email}', parent_email='{$paremail}' WHERE id={$id}");
		return $r ? true : false;
	}

	function delete_student( $id ) {
		$id = (int)$id;
		return $this->query("DELETE FROM dgr_student WHERE id={$id}");
	}

	function get_subjects()
	{
		$id = (int)$id;
		$r = $this->query("SELECT subject_id, name FROM dgr_subject ORDER BY name");
		$ret = array();
		if ( ! $r )
			return $ret;
		while ( $row = pg_fetch_assoc($r) )
			$ret[] = $row;
		return $ret;
	}

	function add_semester( $name, $start, $end )
	{
		$name = addslashes($name);
		$r = $this->query("SELECT id FROM dgr_student");
		$students = array();
		while ( $row = pg_fetch_row($r) )
			$students[] = $row[0];
		$this->query("BEGIN");
		$this->query("INSERT INTO dgr_semester VALUES ( nextval('dgr_semester_id_seq'), '{$name}' )");
		$r = $this->query("SELECT currval('dgr_semester_id_seq')");
		$row = pg_fetch_row($r);
		$semid = $row[0];
		$firstinterval = 7 - strftime('%u', $start);
		$firstweekend = strtotime("+{$firstinterval} days", $start);
		$daystart = strftime('%Y-%m-%d', $start);
		$dayend = strftime('%Y-%m-%d', $firstweekend);
		foreach ( $students as $st )
			$this->query("INSERT INTO dgr_attendance VALUES ( nextval('dgr_attendance_id_seq'), '{$daystart}', '{$dayend}', {$st}, {$semid}, 0, 0, 0 )");
		$start = $firstweekend;
		while ( $start < $end ) {
			$endweek = strtotime("+7 days", $start);
			if ( $endweek > $end )
				$endweek = $end;
			$daystart = strftime('%Y-%m-%d', $start);
			$dayend = strftime('%Y-%m-%d', $endweek);
			foreach ( $students as $st )
				$this->query("INSERT INTO dgr_attendance VALUES ( nextval('dgr_attendance_id_seq'), '{$daystart}', '{$dayend}', {$st}, {$semid}, 0, 0, 0 )");
			$start = $endweek;
		}
		$this->query("COMMIT");
	}

	function change_semester( $id, $name )
	{
		$id = (int)$id;
		$name = addslashes($name);
		$r = $this->query("UPDATE dgr_semester SET name='{$name}' WHERE id={$id}");
		return $r ? true : false;
	}

	function delete_semester( $id )
	{
		$id = (int)$id;
		return $this->query("DELETE FROM dgr_semester WHERE id={$id}");
	}

	function add_subject( $name )
	{
		$name = addslashes($name);
		return $this->query("INSERT INTO dgr_subject VALUES ( nextval('dgr_subject_subject_id_seq'), '{$name}' )");
	}

	function change_subject( $id, $name )
	{
		$id = (int)$id;
		$name = addslashes($name);
		$r = $this->query("UPDATE dgr_subject SET name='{$name}' WHERE subject_id={$id}");
		return $r ? true : false;
	}

	function delete_subject( $id )
	{
		$id = (int)$id;
		return $this->query("DELETE FROM dgr_subject WHERE subject_id={$id}");
	}

	function get_subject_details( $id )
	{
		$id = (int)$id;
		$r = $this->query("SELECT subject_id, block_teacher, descriptive_grade, uid FROM dgr_subject_semester WHERE id={$id}");
		if ( ! $r )
			return null;
		return pg_fetch_assoc($r);
	}

	function change_csubject( $id, $subid, $teachid, $block, $desc )
	{
		$id = (int)$id;
		$subid = (int)$subid;
		$teachid = (int)$teachid;
		$block = $block ? 'TRUE' : 'FALSE';
		$desc = $desc ? 'TRUE' : 'FALSE';
		$r = $this->query("UPDATE dgr_subject_semester SET subject_id={$subid}, uid={$teachid}, block_teacher={$block}, descriptive_grade={$desc} WHERE id={$id}");
		return $r ? true : false;
	}

	function add_csubject( $classid, $semid, $subid, $teachid, $block, $desc )
	{
		$classid = (int)$classid;
		$semid = (int)$semid;
		$subid = (int)$subid;
		$teachid = (int)$teachid;
		$block = $block ? 'TRUE' : 'FALSE';
		$desc = $block ? 'TRUE' : 'FALSE';
		$this->query("BEGIN");
		$this->query("INSERT INTO dgr_subject_semester VALUES ( nextval('dgr_subject_semester_id_seq'), {$subid}, {$block}, {$desc}, {$semid}, {$teachid}, {$classid} )");
		$r = $this->query("SELECT currval('dgr_subject_semester_id_seq')");
		$this->query("COMMIT");
		$row = pg_fetch_row($r);
		return $row[0];
	}

	function delete_csubject( $id )
	{
		$id = (int)$id;
		return $this->query("DELETE FROM dgr_subject_semester WHERE id={$id}");
	}

	function add_grade( $subid, $id )
	{
		$subid = (int)$subid;
		$id = (int)$id;
		return $this->query("INSERT INTO dgr_grade VALUES ( nextval('dgr_grade_id_seq'), '', '', '', {$id}, {$subid} )");
	}
}

?>
