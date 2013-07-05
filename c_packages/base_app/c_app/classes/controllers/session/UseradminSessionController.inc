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
// Session controller (UserAdmin).
//

Cogumelo::Load('SessionController');
app::Load('UseradminVO');

class UserAdminSessionController extends SessionController
{
	//
	// Constructor
	//
	function UserAdminSessionController()
	{
		parent::__construct();
		$this->session_id = "UserAdmin-Session";
	}

	//
	// Set userdata in the session from UserVO
	//
	public function setUser($data)
	{ 	
		$this->setSession($data); 	
	}

	//
	// Remove userdata from the session. Session is not set.
	//
	public function delUser()
	{
		$this->delSession();
	}
 
	//
	// Get current userdata information from session
	//
	public function getUser()
	{
		return $this->getSession();	
	}
	
	//
	// Check if the user session is set.
	//
	public function isUserSet()
	{
		return $this->isSession();
	}
}
?>