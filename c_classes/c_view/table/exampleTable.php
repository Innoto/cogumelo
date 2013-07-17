<?php


////   reference
/*
		$this->clientTable['cols'] // rows in cols
		$this->clientTable['rowssno'] // rows in page
		$this->clientTable['status']['page'] // current page;
		$this->clientTable['command']['command_name'] // current page;		
		$this->clientTable['command']['val'] // value;	
	*/

Cogumelo::Load('genericTableCol');
Cogumelo::Load('abstractTable');
Cogumelo::LoadModule('app');
app::Load('UseradminVO');
app::Load('UseradminController');

class exampleTable extends abstractTable  {

	function __construct($table_client_json) {

		// set initial controllers and client data
		$this->setVOclass(UseradminVO);
		$this->setDataControllerClass(UseradminController);
		$this->setClientTable($table_client_json);

		// Example 1: 
		$this->autoSetcollist(
			array(
				'id',
				'login',
				'name'
				)
		);



//		var_dump($this->cols);
/*
		// Example 2:
		$this->autoSetcol('id');
		$this->autoSetcol('auth');
		$this->setCol(new tableCol(
				'nome',  // col Id
				$this->VO::$keys['nome'], // col Name
				$this->getClientOrderCol('nome') // col Order as client table
			)) ;
*/

		// execute table
		$this->exec();
	}

	//
	// commands: method name must be 'command_name' 
	//

	function command_list() { 
		return $this->dataController->ListUseradmins(false, $this->getOrderArray(), ); 
	}

	function command_disable() {
		//return $this->dataController->delete($this->clientTable['command']['val']); 
	}
	function command_enable(){
		return $this->dataController->delete($this->clientTable['command']['val']); 
	}
	function command_delete() {
		//return $this->dataController->delete($this->clientTable['command']['val']); 
	}
}