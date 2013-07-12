<?php



Cogumelo::load('c_model/Connection');
Cogumelo::load('c_model/DAO');

//
// Facade Superclass
//

class Facade 
{
	var $connectioncontrol;
	var $connection;
	var $dao;
	
	function __construct($entity, $module = false)
	{		
		$this->dao = DAO::Factory($entity, false );
	}
	
	public function openConnection()
	{
		$this->connectioncontrol = Connection::Factory();
		$this->connection = $this->connectioncontrol->db;
	}
	
	public function closeConnection()
	{
		$this->connectioncontrol->Close();
	}

	function __call($name, $args){

		// set arguments as string
		$args_str = '';
		foreach($args as $akey =>$arg){
			$args_str .= (', $args['. $akey .']');
		}

		Cogumelo::debug("TRANSACTION START: ".$name);
		$this->OpenConnection();
		eval('$data = $this->dao->'.$name. '($this->connection'. $args_str . '); ');
		$this->CloseConnection();
		if($data !== false) Cogumelo::debug("TRANSACTION COMPLETED: ".$name);
		else Cogumelo::error("TRANSACTION NOT COMPLETED: ".$name);
		return $data;
	}

}
