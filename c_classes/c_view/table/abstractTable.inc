<?php

//TODO: Meter na VO class os nomes de row, de xeito que se poidan cargar automáticamente de forma simple


		//// clienttable reference
/*
		$this->clientTable['cols'] // rows in cols
		$this->clientTable['rowssno'] // rows in page
		$this->clientTable['status']['page'] // current page;
		$this->clientTable['command']['command_name'] // current page;		
		$this->clientTable['command']['val'] // value;	
*/
Cogumelo::Load('genericTableCol');



abstract class abstractTable {

	protected $cols = array(); // array of tableCol
	protected $totalrows;

	protected $VOclass;
	protected $dataController;

	protected $dataVOarray = array();
	protected $clientTable;



	//
	//	VO Class and Datacontroller
	//

	// set and get VO class and add column id
	function setVOclass($voclass) {
		$this->VOclass = $voclass;

		// set column used as univique id
		$this->setCol(new genericTableCol(	$voclass::$keyId , ''));
	} 

	function getVOclass() {
		return $this->VOclass;
	}

	// set datacontroller object
	function setDatacontrollerClass($datacontroller){
		$this->dataController = new $datacontroller;
	}





	//
	// Cols methods
	//

	// set col
	function setCol($col) {
		$this->cols[$col->getId()] = $col;
	}

	// get col object by name
	function getCol($name){
		return $cols[$name];
	}
	

	// get client col from clientTable
/*	function getOrderCol($id) {
		if($clientTable['cols'] != false)
			return $this->clientTable['cols'][$id]['order'];
		else
			return false;
	} creo que non é necesario este método*/

	// get order array from cols
	function getOrderArray(){
		$orarray = array();

		return $orarray; 
	}

	// set Order Array manually
	function setOrderArray(){

	}

	// get one auto col ()
	function autoSetcol($colid ) {
		$this->autoSetcollist($colid);
	}

	// get autocol if $colid_s is an array, set only one
	function autoSetcollist($colid_s){
		$voclass = $this->VOclass;
		foreach($voclass::$keys as $attrkey => $attrname){

			if(is_array($colid_s)) {					
				// all the cols in the $colid_s list
				if( array_search( $attrkey, $colid_s )){ 
					// create and return tableCol object
					$this->setCol(new genericTableCol($attrkey, $attrname) );
				}
			}
			else {
				//only one col
				if($attrkey == $colid_s ){ 
					// create and return tableCol object
					$this->setCol(new genericTableCol($attrkey, $attrname) );
					break;
				}
			}
		}
		
	}

	// get array from VO according with declared Cols
	function getColsFromVO($vo_obj) {
		$row = array();
		foreach($this->cols as $col) {
			$row[$ol->getId()] = $col->getCol($vo_obj);
		}
		return $row;
	}



	//
	//	Table process
	//

	// get VO array into table and set rest of parameters
	function generateTable(){

		// Set Cols
		$cols = array();
		if(is_array( $this->cols )) {
			foreach($this->cols as $col){
				$cols[] = array('id'=> $col->getId(), 'name'=> $col->getName(), 'ord'=> $col->getOrder() );

			}
		}

		// Set DATA
		$data = array();
		if(is_array( $this->dataVOarray )) {
			foreach ($this->dataVOarray as $each_vo_obj) {
				$data[] = $this->getColsFromVO(each_vo_obj);
			}
		}

		// asign on array  (sends to table encoded in json)
		return array(
				"cols" => $cols,
				"data" => $data,
				"totalrows" => $this->totalrows
			);
	} 

	// set all parameters from client table json
	function setClientTable($client_table){
		$this->clientTable = json_decode($client_table);
	}

	// execute table
	function exec(){
		$cmd = $this->clientTable->command;
		// if clientTable wants to execute an command and command_name() method exist...

		if( method_exists($this, 'command_'.$cmd->command_name) && $cmd->command_name != 'list' ) {
			eval('$this->command_'. $cmd->command_name .'();');
		}

		// calling  to list method in son object
		$data_list = $this->command_list(); //exec list command
		$this->dataVOarray = $data_list['data']; // set VO array
		$this->totalrows = $data_list['count']; // set total rows number


		// print json
		//header('Cache-Control: no-cache, must-revalidate');
		//header('Content-type: application/json');
		die( json_encode( 
				$this->generateTable() 
			));
			
	}

}