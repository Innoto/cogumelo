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

Cogumelo::load('c_model/Connection');

//
// Mysql connection class
//

class MysqlConnection extends Connection
{
	var $db;
	
	function __construct(){

		@$this->db = new mysqli(DB_HOSTNAME ,DB_USER , DB_PASSWORD, DB_NAME,  DB_PORT);

		if ($this->db->connect_error)
			Cogumelo::error(mysqli_connect_error());
		else
			Cogumelo::debug("mySQLi: Connection Stablished to ".DB_HOSTNAME);
		
		@mysqli_query($this->db ,"START TRANSACTION;");
	}
	
	function close()
	{
		mysqli_query($this->db ,"COMMIT;");
		 $this->db->close();
		
		Cogumelo::debug("mySQLi: Connection closed");
	}

}

?>