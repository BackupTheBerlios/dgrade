BEGIN;


CREATE TABLE dgr_style (
	id SERIAL PRIMARY KEY,
	name VARCHAR(30) NOT NULL UNIQUE -- filename
);
-- initalize id sequence
SELECT setval('dgr_style_id_seq', 1);

-- add default style
INSERT INTO dgr_style VALUES ( 1, 'default.css' );


CREATE TABLE dgr_user (
	uid SERIAL NOT NULL UNIQUE,
	login VARCHAR(30) PRIMARY KEY,
	passhash CHAR(40) NOT NULL, -- hexadecimal SHA-1 digest
	name VARCHAR(30),
	surname VARCHAR(30),
	email VARCHAR(30),
	lvl SMALLINT DEFAULT 2 CHECK (lvl >= 0 AND lvl <= 2),
	style INT NOT NULL DEFAULT 1 REFERENCES dgr_style(id) ON UPDATE CASCADE ON DELETE SET DEFAULT,
	CONSTRAINT dgr_style_valid CHECK (style >= 1)
);
-- initialize uid sequence
SELECT setval('dgr_user_uid_seq', 1);

CREATE INDEX dgr_user_uid_idx ON dgr_user(uid);

CREATE TABLE dgr_class (
	class_id SERIAL PRIMARY KEY,
	name VARCHAR(30) NOT NULL,
	startyear INT CHECK (startyear >= 2000 AND startyear <= 2020),
	tutor_id INT REFERENCES dgr_user(uid) ON UPDATE CASCADE ON DELETE SET NULL
);
-- initialize class_id sequence
SELECT setval('dgr_class_class_id_seq', 1);


CREATE TABLE dgr_subject (
	subject_id SERIAL PRIMARY KEY,
	name VARCHAR(30) NOT NULL
);
-- initialize subject_id sequence
SELECT setval('dgr_subject_subject_id_seq', 1);

CREATE INDEX dgr_subject_name_idx ON dgr_subject(name);


CREATE TABLE dgr_semester (
	id SERIAL PRIMARY KEY,
	name VARCHAR(30) NOT NULL
);
-- initialize id sequence
SELECT setval('dgr_semester_id_seq', 1);

CREATE INDEX dgr_semester_name_idx ON dgr_semester(name);


CREATE TABLE dgr_subject_semester (
	id SERIAL PRIMARY KEY,
	subject_id INT REFERENCES dgr_subject(subject_id) ON UPDATE CASCADE ON DELETE CASCADE,
	block_teacher BOOLEAN DEFAULT TRUE,
	descriptive_grade BOOLEAN DEFAULT FALSE,
	semester_id INT REFERENCES dgr_semester(id) ON UPDATE CASCADE ON DELETE CASCADE,
	uid INT DEFAULT NULL REFERENCES dgr_user(uid) ON UPDATE CASCADE ON DELETE SET DEFAULT,
	class_id INT REFERENCES dgr_class(class_id) ON UPDATE RESTRICT ON DELETE CASCADE
);
-- initialize id sequence
SELECT setval('dgr_subject_semester_id_seq', 1);


CREATE TABLE dgr_student (
	id SERIAL PRIMARY KEY,
	name VARCHAR(30) NOT NULL,
	surname VARCHAR(30) NOT NULL,
	email VARCHAR(30),
	parent_email VARCHAR(30),
	class_id INT REFERENCES dgr_class(class_id) ON UPDATE CASCADE ON DELETE CASCADE
);
-- initialize id sequence
SELECT setval('dgr_student_id_seq', 1);

CREATE INDEX dgr_student_surname_idx ON dgr_student(surname);


CREATE TABLE dgr_attendance (
	id SERIAL PRIMARY KEY,
	day_start DATE NOT NULL,
	day_end DATE NOT NULL,
	student_id INT REFERENCES dgr_student(id) ON UPDATE CASCADE ON DELETE CASCADE,
	semester_id INT REFERENCES dgr_semester(id) ON UPDATE CASCADE ON DELETE CASCADE,
	absent INT DEFAULT 0,
	explained INT DEFAULT 0,
	late INT DEFAULT 0
);
-- initialize id sequence
SELECT setval('dgr_attendance_id_seq', 1);

CREATE INDEX dgr_attendance_student_id_idx ON dgr_attendance(student_id);
CREATE INDEX dgr_attendance_semester_id_idx ON dgr_attendance(semester_id);


CREATE TABLE dgr_grade (
	id SERIAL PRIMARY KEY,
	grades BYTEA,
	semestral TEXT,
	notes TEXT,
	student_id INT REFERENCES dgr_student(id) ON UPDATE CASCADE ON DELETE CASCADE,
	subject_id INT REFERENCES dgr_subject_semester(id) ON UPDATE CASCADE ON DELETE CASCADE
);
-- initialize id sequence
SELECT setval('dgr_grade_id_seq', 1);


COMMIT;
