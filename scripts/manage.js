/*
 *      main.js
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

function set_page( loadclass )
{
	set_height();
	if ( loadclass )
		change_class();
}

function enable_student_form()
{
	with ( document ) {
		getElementById('student_name').disabled = false;
		getElementById('student_surname').disabled = false;
		getElementById('email').disabled = false;
		getElementById('paremail').disabled = false;
		getElementById('addbutton').disabled = false;
		if ( getElementById('chooseid').innerHTML != '0' )
			getElementById('delbutton').disabled = false;
	}
}

function clear_field( field )
{
	field.value = '';
	field.disabled = true;
}

function hide_validate_img()
{
	with ( document ) {
		getElementById('emailvalid').style.display = 'none';
		getElementById('emailbad').style.display = 'none';
		getElementById('paremailvalid').style.display = 'none';
		getElementById('paremailvalid').style.display = 'none';
	}
}

function clear_student_form()
{
	with ( document ) {

		clear_field(getElementById('student_name'));
		clear_field(getElementById('student_surname'));
		clear_field(getElementById('email'));
		clear_field(getElementById('paremail'));
		getElementById('headername').innerHTML = '&nbsp;';
		getElementById('addbutton').disabled = true;
		getElementById('delbutton').disabled = true;
		getElementById('inputdisabled').innerHTML = '1';
	}
	hide_validate_img();
}

function clear_subject_form()
{
	with ( document ) {
		clear_field(getElementById('sub_name'));
		getElementById('addbutton').disabled = true;
		getElementById('delbutton').disabled = true;
		getElementById('inputdisabled').innerHTML = '1';
	}
}

function change_student_selection( i, id )
{
	change_sel(i, id);
	var edit = document.getElementById('inputdisabled');
	if ( edit.innerHTML == '1' ) {
		enable_student_form();
		edit.innerHTML = '0';
	}
	change_student(id);
	hide_validate_img();
}

function change_tutor( id )
{
	document.getElementById('selecttutor').innerHTML = id;
}

function change_semester( id )
{
	with ( document ) {
		var semname = getElementById('semname');
		var selsemester = getElementById('selsemester');
		var semstart = getElementById('semstart');
		var semend = getElementById('semend');
	}
	var dis = (id != 0);
	semstart.disabled = dis;
	semend.disabled = dis;
	if ( id == 0 ) {
		semname.value = '';
		return;
	}
	semname.value = selsemester.options[selsemester.selectedIndex].text;
}

function change_subject_selection( i, id, name )
{
	change_sel(i, id);
	var edit = document.getElementById('inputdisabled');
	if ( edit.innerHTML == '1' ) {
		enable_subject_form();
		edit.innerHTML = '0';
	}
	change_subject(id, name);
}

function enable_subject_form()
{
	with ( document )
	{
		getElementById('sub_name').disabled = false;
		getElementById('addbutton').disabled = false;
		if ( getElementById('chooseid').innerHTML != '0' )
			getElementById('delbutton').disabled = false;
	}
}

function enable_user_form()
{
	with ( document ) {
		getElementById('pass').disabled = false;
		getElementById('user_name').disabled = false;
		getElementById('user_surname').disabled = false;
		getElementById('email').disabled = false;
		getElementById('levelsel').disabled = false;
		getElementById('addbutton').disabled = false;
	}
}

function enable_csubject_form()
{
	with ( document ) {
		getElementById('subsel').disabled = false;
		getElementById('teachsel').disabled = false;
		getElementById('blockyes').disabled = false;
		getElementById('blockno').disabled = false;
		getElementById('descyes').disabled = false;
		getElementById('descno').disabled = false;
		getElementById('addbutton').disabled = false;
	}
}

function clear_csubject_form()
{
	with ( document ) {
		getElementById('subsel').disabled = true;
		getElementById('teachsel').disabled = true;
		getElementById('blockyes').disabled = true;
		getElementById('blockno').disabled = true;
		getElementById('descyes').disabled = true;
		getElementById('descno').disabled = true;
		getElementById('addbutton').disabled = true;
		getElementById('delbutton').disabled = true;
		getElementById('inputdisabled').innerHTML = '1';
	}
}

function clear_user_form()
{
	with ( document ) {
		clear_field(getElementById('login'));
		clear_field(getElementById('pass'));
		clear_field(getElementById('user_name'));
		clear_field(getElementById('user_surname'));
		clear_field(getElementById('email'));
		getElementById('levelsel').disabled = true;
		getElementById('addbutton').disabled = true;
		getElementById('delbutton').disabled = true;
		getElementById('emailvalid').style.display = 'none';
		getElementById('emailbad').style.display = 'none';
		getElementById('inputdisabled').innerHTML = '1';
	}
}

function change_subject( id, name )
{
	with ( document ) {
		getElementById('sub_name').value = name;
		getElementById('delbutton').disabled = (id == 0);
	}
}

function change_user_selection( i, id )
{
	change_sel(i, id);
	var edit = document.getElementById('inputdisabled');
	if ( edit.innerHTML == '1' ) {
		enable_user_form();
		edit.innerHTML = '0';
	}
	with ( document ) {
		var b = (id != 0);
		getElementById('delbutton').disabled = ! b;
		getElementById('login').disabled = b;
	}
	change_user(id);
}

function change_csubject_selection( i, id )
{
	change_sel(i, id);
	var edit = document.getElementById('inputdisabled');
	if ( edit.innerHTML == '1' ) {
		enable_csubject_form();
		edit.innerHTML = '0';
	}
	document.getElementById('delbutton').disabled = (id == 0);
	change_csubject_details(id);
}

/* AJAX */

function change_class()
{
	with ( document ) {
		var classid = getElementById('selclassid').innerHTML;
		var choosebox = getElementById('choosebox');
	}
	if ( classid == 0 ) {
		clear_student_form();
		return;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			choosebox.innerHTML = xmlhttp.responseText;
		}
	};
	var url = 'ajax/get_managed_class_list.php?id=' + classid + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
	document.getElementById('inputdisabled').innerHTML = '1';
	clear_student_form();
}

function change_student( id )
{
	with ( document ) {
		var headername = getElementById('headername');
		var name = getElementById('student_name');
		var surname = getElementById('student_surname');
		var email = getElementById('email');
		var paremail = getElementById('paremail');
		getElementById('delbutton').disabled = (id == 0);
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			var json = eval('(' + xmlhttp.responseText + ')');
			headername.innerHTML = htmlspecialchars_decode(json.nameheader);
			name.value = htmlspecialchars_decode(json.name);
			surname.value = htmlspecialchars_decode(json.surname);
			email.value = htmlspecialchars_decode(json.email);
			paremail.value = htmlspecialchars_decode(json.paremail);
		}
	};
	var url = 'ajax/get_managed_student.php?id=' + id + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function change_user ( id )
{
	with ( document ) {
		var login = getElementById('login');
		var name = getElementById('user_name');
		var surname = getElementById('user_surname');
		var email = getElementById('email');
		var level = getElementById('levelsel');
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			var json = eval('(' + xmlhttp.responseText + ')');
			login.value = json.login;
			name.value = json.name;
			surname.value = json.surname;
			email.value = json.email;
			for (i=0; i<level.length; i++)
				if ( level.options[i].value == json.level ) {
					level.selectedIndex = i;
					break;
				}
		}
	};
	var url = 'ajax/get_managed_user.php?id=' + parseInt(id) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function change_csubject_details( id )
{
	with ( document ) {
		var subsel = getElementById('subsel');
		var classid = getElementById('classidspan').innerHTML;
		var teachsel = getElementById('teachsel');
		var blockyes = getElementById('blockyes');
		var blockno = getElementById('blockno');
		var descyes = getElementById('descyes');
		var descno = getElementById('descno');
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			var json = eval('(' + xmlhttp.responseText + ')');
			for (i=0; i<subsel.length; i++)
				if ( subsel.options[i].value == json.subid ) {
					subsel.selectedIndex = i;
					break;
				}
			for (i=0; i<teachsel.length; i++)
				if ( teachsel.options[i].value == json.teachid ) {
					teachsel.selectedIndex = i;
					break;
				}
			if ( json.block == 't' )
				blockyes.checked = true;
			else
				blockno.checked = true;
			if ( json.desc == 't' )
				descyes.checked = true;
			else
				descno.checked = true;
		}
	};
	var url = 'ajax/get_csubject_info.php?id=' + parseInt(id) + '&classid=' + classid + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function change_managed_class( classid )
{
	if ( classid < 0 )
		return;
	with ( document ) {
		if ( classid == 0 ) {
			getElementById('classname').value = '';
			var d = new Date();
			getElementById('classyear').value = d.getFullYear();
			getElementById('classtutor').selectedIndex = 0;
			getElementById('choosebox').innerHTML = '';
			clear_student_form();
			return;
		}
		var classidspan = getElementById('selclassid');
		var selclass = getElementById('selclass');
		var name = selclass.options[selclass.selectedIndex].text;
		getElementById('classname').value = name;
		var classyear = getElementById('classyear');
		var sel = getElementById('classtutor');
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			var json = eval('(' + xmlhttp.responseText + ')');
			classyear.value = json.year;
			for (i=0; i<sel.length; i++)
				if ( sel.options[i].value == json.tutor ) {
					sel.selectedIndex = i;
					break;
				}
		}
	};
	var url = 'ajax/get_class_info.php?id=' + classid + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
	classidspan.innerHTML = classid;
	change_class();
}

function save_class()
{
	with ( document ) {
		var classid = getElementById('selclassid').innerHTML;
		var name = getElementById('classname').value;
		var startyear = getElementById('classyear').value;
		var tutorid = getElementById('selecttutor').innerHTML;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			var json = eval('(' + xmlhttp.responseText + ')');
			if ( json.added == 1 ) {
				var selclass = document.getElementById('selclass');
				selclass.innerHTML = htmlspecialchars_decode(json.selhtml);
				for (i=0; i<selclass.length; i++)
					if ( selclass.options[i].value == classid ) {
						selclass.selectedIndex = i;
						break;
					}
			}
		}
	};
	var url = 'ajax/change_class.php';
	var params = 'id=' + classid + '&name=' + encodeURIComponent(name) + '&startyear=' + encodeURIComponent(startyear) + '&tutorid=' + tutorid + '&qid=' + Math.random();
	xmlhttp.open('POST', url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}

function delete_class()
{
	with ( document ) {
		var classid = getElementById('selclassid').innerHTML;
		var selclass = getElementById('selclass');
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			selclass.innerHTML = xmlhttp.responseText;
			change_managed_class(0);
		}
	};
	var url = 'ajax/delete_class.php?id=' + classid + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function save_student()
{
	if ( ! validate_email() || ! validate_parent_email() )
		return;
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		var classid = getElementById('selclassid').innerHTML;
		var name = getElementById('student_name').value;
		var surname = getElementById('student_surname').value;
		var email = getElementById('email').value;
		var paremail = getElementById('paremail').value;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			change_managed_class(classid);
			clear_student_form();
		}
	};
	var url = 'ajax/save_student_info.php';
	var params = 'id=' + parseInt(id) + '&c=' + parseInt(classid) + '&n=' + encodeURIComponent(name) + '&s=' + encodeURIComponent(surname) + '&e=' + encodeURIComponent(email) + '&p=' + encodeURIComponent(paremail) + '&qid=' + Math.random();
	xmlhttp.open('POST', url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}

function save_user()
{
	if ( ! validate_email() )
		return;
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		var login = getElementById('login').value;
		var pass = getElementById('pass').value;
		var name = getElementById('user_name').value;
		var surname = getElementById('user_surname').value;
		var email = getElementById('email').value;
		var sel = getElementById('levelsel');
	}
	if ( id == '0' && pass == '' )
		return;
	var level = sel.options[sel.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			document.getElementById('choosebox').innerHTML = xmlhttp.responseText;
			clear_user_form();
		}
	};
	var url = 'ajax/save_user_info.php';
	var params = 'id=' + parseInt(id) + '&l=' + encodeURIComponent(login) + '&p=' + encodeURIComponent(pass) + '&n=' + encodeURIComponent(name) + '&s=' + encodeURIComponent(surname) + '&e=' + encodeURIComponent(email) + '&lvl=' + parseInt(level) + '&qid=' + Math.random();
	xmlhttp.open('POST', url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}

function save_csubject()
{
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		var subsel = getElementById('subsel');
		var teachsel = getElementById('teachsel');
		var block = getElementById('blockyes').checked ? 1 : 0;
		var desc = getElementById('descyes').checked ? 1 : 0;
		var classid = getElementById('classidspan').innerHTML;
		var selsem = getElementById('selsemester');
	}
	var subid = subsel.options[subsel.selectedIndex].value;
	var teachid = teachsel.options[teachsel.selectedIndex].value;
	var semid = selsem.options[selsem.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			alert(xmlhttp.responseText);
			change_csubjects(false);
			clear_csubject_form();
		}
	};
	var url = 'ajax/save_csubject.php?id=' + id + '&classid=' + classid + '&semid=' + semid + '&subid=' + subid + '&teachid=' + teachid + '&block=' + block + '&desc=' + desc + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function delete_csubject()
{
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		var classid = getElementById('classidspan').innerHTML;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			alert(xmlhttp.responseText);
			change_csubjects(false);
			clear_csubject_form();
		}
	};
	var url = 'ajax/delete_csubject.php?id=' + id + '&classid=' + classid + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function save_subject()
{
	with ( document ) {
		var choosebox = getElementById('choosebox');
		var id = getElementById('chooseid').innerHTML;
		var name = getElementById('sub_name').value;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			choosebox.innerHTML = xmlhttp.responseText;
		}
	};
	var url = 'ajax/change_subject.php';
	var params = 'id=' + parseInt(id) + '&n=' + encodeURIComponent(name) + '&qid=' + Math.random();
	xmlhttp.open('POST', url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}

function delete_student()
{
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		var classid = getElementById('selclassid').innerHTML;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			change_managed_class(classid);
			clear_student_form();
		}
	};
	var url = 'ajax/delete_student.php?id=' + parseInt(id) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function save_semester()
{
	with ( document ) {
		var sel = getElementById('selsemester');
		var name = getElementById('semname').value;
		var semstart = getElementById('semstart').value;
		var semend = getElementById('semend').value;
	}
	var id = sel.options[sel.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			alert(xmlhttp.responseText);
			sel.innerHTML = xmlhttp.responseText;
		}
	};
	var url = 'ajax/change_semester.php'
	var params = 'id=' + parseInt(id) + '&n=' + encodeURIComponent(name) + '&semstart=' + encodeURIComponent(semstart) + '&semend=' + encodeURIComponent(semend) + '&qid=' + Math.random();
	xmlhttp.open('POST', url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}

function delete_semester()
{
	var sel = document.getElementById('selsemester');
	var id = sel.options[sel.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			sel.innerHTML = xmlhttp.responseText;
		}
	};
	var url = 'ajax/delete_semester.php?id=' + parseInt(id) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function delete_subject()
{
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		var choosebox = getElementById('choosebox');
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			choosebox.innerHTML = xmlhttp.responseText;
			clear_subject_form();
		}
	};
	var url = 'ajax/delete_subject.php?id=' + parseInt(id) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function delete_user()
{
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		var choosebox = getElementById('choosebox');
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			choosebox.innerHTML = xmlhttp.responseText;
			clear_user_form();
		}
	};
	var url = 'ajax/delete_user.php?id=' + parseInt(id) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function change_csubjects( change_class )
{
	with ( document ) {
		var classid;
		if ( change_class ) {
			var selclass = getElementById('selclass');
			classid = selclass.options[selclass.selectedIndex].value;
			getElementById('classidspan').innerHTML = classid;
		} else
			classid = getElementById('classidspan').innerHTML;
		var selsem = getElementById('selsemester');
		var choosebox = getElementById('choosebox');
	}
	var semid = selsem.options[selsem.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			choosebox.innerHTML = xmlhttp.responseText;
		}
	};
	var url = 'ajax/get_class_subjects.php?id=' + parseInt(classid) + '&semid=' + parseInt(semid) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}
