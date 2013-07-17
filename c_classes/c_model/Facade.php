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
	var $develMode = false;
	
	function __construct($entity, $module = false)
	{		
		$this->dao = DAO::Factory($entity, $module );
	}
	
	public function openConnection()
	{
		$this->connectioncontrol = Connection::Factory($this->develMode);
		$this->connection = $this->connectioncontrol->db;
	}
	
	public function closeConnection()
	{
		$this->connectioncontrol->Close();
	}

	public function develMode($user, $password) {
		$this->develMode['DB_USER'] = $user;
		$this->develMode['DB_PASSWORD'] = $password;
		//$this->develMode['DB_NAME'] = 'mysql';
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
