<?php
Cogumelo::load('c_model/Connection.php');
Cogumelo::load('c_model/DAO.php');

define( 'COGUMELO_ERROR', 'cogumelo_error_2203b5b6531bc7251a85e3af3b8dca09');

//
// Facade Superclass
//

class Facade
{
	var $connectioncontrol;
	var $connection;
	var $dao;
	var $develModeData = false;


	function __construct($entity, $module=false)
	{
		$this->dao = DAO::Factory($entity, $module);
	}

	public function openConnection()
	{
		$this->connectioncontrol = Connection::Factory($this->develModeData);
	}

	public function develMode($user, $password, $DB=false) {
		$this->develModeData = array();

		$this->develModeData['DB_USER'] = $user;
		$this->develModeData['DB_PASSWORD'] = $password;
		$this->develModeData['DB_NAME'] = $DB;
	}

	function __call($name, $args){

		// set arguments as string
		$args_str = '';
		foreach($args as $akey =>$arg){
			$args_str .= (', $args['. $akey .']');
		}

		Cogumelo::debug("TRANSACTION START: ".$name);
		$this->OpenConnection();
		eval('$data = $this->dao->'.$name. '($this->connectioncontrol'. $args_str . '); ');

		if($data !== COGUMELO_ERROR) Cogumelo::debug("TRANSACTION COMPLETED: ".$name);
		else Cogumelo::error("TRANSACTION NOT COMPLETED: ".$name);

		return $data;
	}

}
