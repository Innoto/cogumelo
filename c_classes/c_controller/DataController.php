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

Cogumelo::load("c_model/Facade");

//
// abstract DataController class
//

abstract class DataController {

	var $voClass;

	// query parameters
	private $range = array();
	private $order = array();
	private $filters = array();


	//
	//	Constructor 
	//
	function __construct() {

	}



	//
	//	auto find method
	//
	function find($id, $key=false) 
	{
		Cogumelo::debug( "Called find on ".get_called_class()." with id=". $id);
		$data = $this->data->Find($id, $key);

		return $data;
	}
	
	
	//
	// auto list method
	//
	function listItems($filters = false, $range = false, $order = false, $cache = false)
	{

		Cogumelo::debug( "Called listItems on ".get_called_class() );
		$data = $this->data->listItems($filters, $range, $order, $cache);

		return $data;
	}

	//
	// auto count method
	//
	function listCount($filters = false)
	{


		Cogumelo::debug( "Called listCount on ".get_called_class() );
		$data = $this->data->listCount($filters);

		return $data;
	}

	
	//
	//	auto create  method
	//	$data can be (array) or (object)
	function create($data)
	{
		Cogumelo::debug( "Called create on ".get_called_class() );
		if(!is_object($data))
			$data = new $this->voClass($data);

		$data = $this->data->Create($data);

		return $data;
		
	}
	
	
    //
	//	auto update method
	//	data can be array or VO object
	function update($data)		
	{
		Cogumelo::debug( "Called update on ".get_called_class() );
		if(!is_object($data))
			$data = new $this->voClass($data);

		$data = $this->data->Update($data);
				
		return $data;
	}


	//
	//	auto delete method
	//
	function delete($id)
	{
		Cogumelo::debug( "Called delete on ".get_called_class()." with id=".$id );
		$data = $this->data->Delete($id);
		
		return $data;
	}
}