<?php

/*
Cogumelo v1.0a - Innoto S.L.
Copyright (C) 2013 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@innoto.es>

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



/**
* DataController Class
*
* abstract DataController class
*
* @author: pablinhob
*/


Cogumelo::load("c_model/Facade");


abstract class DataController {

	var $voClass;

	// query parameters
	private $range = array();
	private $order = array();
	private $filters = array();


	function __construct() {

	}


  /*
  * @param mixed $id identifier
  * @param string $key vo key to set at id (false is VO primary key)
  */
	function find($id, $key=false) 
	{
		Cogumelo::debug( "Called find on ".get_called_class()." with id=". $id);
		$data = $this->data->Find($id, $key);

		return $data;
	}
	
	
  /*
  *	List items from table
  *
  * @param array $filters array of filters
  * @param array $range two element array with result range ex. array(0,100)
  * @param array $order order for query 
  * @apram boolean $cache true means cache is enabled
  */
	function listItems($filters = false, $range = false, $order = false, $cache = false)
	{

		Cogumelo::debug( "Called listItems on ".get_called_class() );
		$data = $this->data->listItems($filters, $range, $order, $cache);

		return $data;
	}


  /*
  *	Count items from table
  *
  * @param array $filters array of filters
	*/
	function listCount($filters = false)
	{

		Cogumelo::debug( "Called listCount on ".get_called_class() );
		$data = $this->data->listCount($filters);

		return $data;
	}

	
  /*
  *	create item
  *
  * @param mixed $data can be (array) or (VO object)
  */
	function create($data)
	{
		Cogumelo::debug( "Called create on ".get_called_class() );
		if(!is_object($data))
			$data = new $this->voClass($data);

		$data = $this->data->Create($data);

		return $data;
		
	}
	
	
  /*
  *	update item 
  *
  * @param mixed $data can be array or VO object
  */
	function update($data)		
	{
		Cogumelo::debug( "Called update on ".get_called_class() );
		if(!is_object($data))
			$data = new $this->voClass($data);

		$data = $this->data->Update($data);
				
		return $data;
	}


  /*
  *	delete item 
  *
  * @param mixed $id must be primary key of VO
  */
	function delete($id)
	{
		Cogumelo::debug( "Called delete on ".get_called_class()." with id=".$id );
		$data = $this->data->Delete($id);
		
		return $data;
	}
}