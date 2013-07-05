<?php
/*
Cogumelo v0.5 - Innoto S.L.
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
// UserAdmin Access Controller
//

Cogumelo::Load('AccessController');

app::Load('UseradminSessionController');
app::Load('UseradminController');
app::Load('UseradminVO');

class UserAdminAccessController extends AccessController
{
	//
	// Constructor
	//
	function UserAdminAccesscontroller()
	{  
		$this->setSessioncontrol( new UserAdminSessionController() );
		$this->setSessiondata($this->sessioncontrol->getUser());
	}

	//
	// Is current User a Loged Administrator?
	//
	function isAdmin()
	{
		if($this->sessiondata)
			 return true;
		else return false;
	}
	 
	//
	// Login an Admin User
	//
	function UserAdminLogin($username, $passwd)
	{
		$usercontrol= new UserAdminController();
		
		$user=new UserAdminVO();
		$user->setter('login', $username);
		$user->setter('passwd', $passwd);  
		
		if($logeduser = $usercontrol->AuthenticateUseradmin($user))
		{
			$this->sessioncontrol->setUser($logeduser);
			Cogumelo::Log("Accepted UserAdmin authentication: user ".$username." is logged", 2);
			return true;
		}          
		else
		{
			Cogumelo::Log("Failed UserAdmin authentication: user ".$username, 2);
			return false;
		}
	}
	
	//
	// Logout a User
	//
	function Logout()
	{
		if($currentuser = $this->sessioncontrol->getUser())
		{   	
			$this->sessioncontrol->delUser();
			Cogumelo::Log("UserAdmin ".$currentuser->getter('login')." Logged out", 2);
			return true;
		}   
		else
		{
			Cogumelo::Log("Unable to Logout", 2);
			return false;
		}	
	} 
}
?>