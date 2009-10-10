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

function set_page()
{
	set_height();
	set_visibility();
	change_class();
}

function set_visibility()
{
	with ( document ) {
		var vis = getElementById('hrefvis').innerHTML;
		var lvl = getElementById('lvl').innerHTML;
		if ( vis == 0 && lvl > 0 )
			getElementById('managebox').style.visibility = 'hidden';
		else
			getElementById('managebox').style.visibility = 'visible';
	}
}

function toggle_row( rowid )
{
	var row = document.getElementById('toggle' + rowid);
	if ( row.style.display != 'table-row-group' )
		row.style.display = 'table-row-group';
	else
		row.style.display = 'none';
}

function toggle_attendance()
{
	var row = document.getElementById('attendance');
	if ( row.style.visibility != 'visible' )
		row.style.visibility = 'visible';
	else
		row.style.visibility = 'hidden';
}

function set_view()
{
	change_class();
	document.getElementById('workarea').innerHTML = '';
}

function change_semester()
{
	with ( document ) {
		var id = getElementById('chooseid').innerHTML;
		if ( id != 0 )
			change_grades(id);
	}
}

function change_selection( i, id )
{
	change_sel(i, id);
	change_grades(id);
}

/* AJAX */

function change_grades( id )
{
	with ( document ) {
		var semobj = getElementById('selsemester');
		var view = getElementById('view1').checked ? 1 : 2;
	}
	var semid = semobj.options[semobj.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			document.getElementById('workarea').innerHTML = xmlhttp.responseText;
		}
	};
	var url = 'ajax/get_grades.php?id=' + id + '&semid=' + semid + '&view=' + view + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function change_class()
{
	with ( document ) {
		var selobj = getElementById('selclass');
		var hrefvis = getElementById('hrefvis');
		var managehref = getElementById('managehref');
		var subjecthref = getElementById('subjecthref');
		var choosebox = getElementById('choosebox');
		var view = getElementById('view1').checked ? 1 : 2;
		var semobj = getElementById('selsemester');
	}
	var semid = semobj.options[semobj.selectedIndex].value;
	var classid = selobj.options[selobj.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			var json = eval('(' + xmlhttp.responseText + ')');
			hrefvis.innerHTML = json.hrefvis;
			managehref.href = json.managehref;
			subjecthref.href = json.subjecthref;
			choosebox.innerHTML = htmlspecialchars_decode(json.choosebox);
			set_visibility();
		}
	};
	var url = 'ajax/get_class_list.php?id=' + classid + '&view=' + view + '&semid=' + semid + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function change_attendance()
{
	with ( document ) {
		var selobj = getElementById('attsel');
		var hrefobj = getElementById('atthref');
		var absn = getElementById('absn');
		var expl = getElementById('expl');
		var late = getElementById('late');
	}
	var attid = selobj.options[selobj.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			var json = eval('(' + xmlhttp.responseText + ')');
			absn.value = json.absent;
			expl.value = json.explained;
			late.value = json.late;
			hrefobj.onclick = function() { save_attendance(attid); };
		}
	};
	var url = 'ajax/get_attendance.php?id=' + attid + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function save_grades( id )
{
	with ( document ) {
		var grades = getElementById('grades' + id).value;
		var notes = getElementById('notes' + id).value;
		var semestral = getElementById('semestral' + id).value;
		var chooseid = getElementById('chooseid').innerHTML;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			change_grades(chooseid);
		}
	};
	var url = 'ajax/modify_grade.php';
	var params = 'id=' + id + '&grades=' + encodeURIComponent(grades) + '&notes=' + encodeURIComponent(notes) + '&semestral=' + encodeURIComponent(semestral) + '&qid=' + Math.random();
	xmlhttp.open('POST', url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}

function save_attendance( id )
{
	with ( document ) {
		var absent = getElementById('absn').value;
		var explained = getElementById('expl').value;
		var late = getElementById('late').value;
		var chooseid = getElementById('chooseid').innerHTML;
	}
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			change_grades(chooseid);
		}
	};
	var url = 'ajax/modify_attendance.php?id=' + id + '&absent=' + parseInt(absent) + '&explained=' + parseInt(explained) + '&late=' + parseInt(late) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}

function sendone( id, semid )
{
	var msg = document.getElementById('mailmsg');
	msg.innerHTML = '...';
	var xmlhttp = get_ajax_request();
	var url = 'ajax/sendone.php?id=' + parseInt(id) + '&semid=' + parseInt(semid) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, false);
	xmlhttp.send(null);
	msg.innerHTML = xmlhttp.responseText;
}

function sendall( semid )
{
	// TODO: nice info
	var sel = document.getElementById('selclass');
	var classid = sel.options[sel.selectedIndex].value;
	var xmlhttp = get_ajax_request();
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 ) {
			alert(xmlhttp.responseText);
		}
	};
	var url = 'ajax/sendall.php?id=' + parseInt(classid) + '&semid=' + parseInt(semid) + '&qid=' + Math.random();
	xmlhttp.open('GET', url, true);
	xmlhttp.send(null);
}
