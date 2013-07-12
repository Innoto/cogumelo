<?php
/*
Cogumelo v1.0 - Innoto S.L.
Copyright (C) 2013 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

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
// DAO Superclass
//

class DAO
{
	public static function factory($entity, $module)
	{
		

		$classpath = 'model/'. DB_ENGINE . '/'. ucfirst(DB_ENGINE) .$entity.'DAO';

		// check if entity is in module or is in main project
		if($module) {
			eval($module.'::load("'. $classpath .'");');
		else
		{
			Cogumelo::load($classpath);
		}



		$dao_obj = ucfirst(DB_ENGINE).$entity;

		eval('$dao_obj_return = new '.$dao_obj.'DAO();');
		return $dao_obj_return;
	}
}
?>