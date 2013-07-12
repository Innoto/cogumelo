<?php
/*
Cogumelo v0.2 - Innoto S.L.
Copyright (C) 2010 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
USA.
*/

//
// Session controller (Superclass).
//

abstract class SessionController
{
	protected $session_id;		// Session ID String
	
	//
	// Constructor
	//
	function __construct() {}

	//
	// Set data in the session
	//
	public function setSession($data)
	{
		if ( !isset($_SESSION[$this->session_id]) ) 	
			unset($_SESSION[$this->session_id]);
			
		$_SESSION[$this->session_id] = serialize($data);
	}
	
	//
	// Remove data from the session. Session is not set.
	//
	public function delSession()
	{
		if( isset($_SESSION[$this->session_id]) )
			unset($_SESSION[$this->session_id]);
	}

	//
	// Get current data information from session
	//
	public function getSession()
	{
		if( isset($_SESSION[$this->session_id]) )
		{
			$data = $_SESSION[$this->session_id];
			return unserialize($data);
		}
		else return false;
	}

	//
	// Check if the session is set.
	//
	public function isSession()
	{
		if( isset($_SESSION[$this->session_id]) )
			return true;
		else return false;
	}
	
	public function getSessionId()
	{
		return $this->session_id;
	}
}
?>