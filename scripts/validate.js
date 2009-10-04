/*
 *      install.js
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

function is_latin( str, dot_allowed )
{
	var valid = "_0123456789abcdefghijklmnoprstuvwxyzABCDEFGHIJKLMNOPRSTUVWXYZ";
	if ( typeof dot_allowed == "undefined" )
		dot_allowed = false;
	if ( dot_allowed )
		valid += ".";
	for (i=0; i<str.length; i++)
		if ( valid.indexOf(str.charAt(i), 0) == -1 )
			return false;
	return true;
}

function validate_user()
{
	with ( document ) {
		var u = getElementById('user').value;
		var fieldvalid = getElementById('uservalid');
		var fieldbad = getElementById('userbad');
	}
	if ( u.length == 0 || ! is_latin(u) ) {
		fieldvalid.style.display = 'none';
		fieldbad.style.display = 'block';
		return false;
	}
	fieldbad.style.display = 'none';
	fieldvalid.style.display = 'block';
	return true;
}

function validate_pass()
{
	with ( document ) {
		var p1 = getElementById('pass').value;
		var p2 = getElementById('passconf').value;
		var fieldvalid = getElementById('passvalid');
		var fieldbad = getElementById('passbad');
	}
	if ( p1 == '' || p1 != p2 ) {
		fieldvalid.style.display = 'none';
		fieldbad.style.display = 'block';
		return false;
	}
	fieldbad.style.display = 'none';
	fieldvalid.style.display = 'block';
	return true;
}

function internal_email_validate( e, fieldvalid, fieldbad )
{
	var re = /.+@.+\..+/;
	if ( e.length > 0 && ! re.test(e) ) {
		fieldvalid.style.display = 'none';
		fieldbad.style.display = 'block';
		return false;
	}
	fieldbad.style.display = 'none';
	fieldvalid.style.display = 'block';
	return true;
}

function validate_parent_email()
{
	with ( document ) {
		var e = getElementById('paremail').value;
		var fieldvalid = getElementById('paremailvalid');
		var fieldbad = getElementById('paremailbad');
	}
	return internal_email_validate(e, fieldvalid, fieldbad);
}

function validate_email()
{
	with ( document ) {
		var e = getElementById('email').value;
		var fieldvalid = getElementById('emailvalid');
		var fieldbad = getElementById('emailbad');
	}
	return internal_email_validate(e, fieldvalid, fieldbad);
}

function check_install_form()
{
	var ret = validate_user();
	ret = validate_pass() && ret;
	return validate_email() && ret;
}
