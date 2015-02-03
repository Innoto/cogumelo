<?php
Cogumelo::load('coreModel/Connection.php');
Cogumelo::load('coreModel/DAO.php');

define( 'COGUMELO_ERROR', 'cogumelo_error_2203b5b6531bc7251a85e3af3b8dca09');

/**
 * Facade for Data
 *
 * @package Cogumelo Model
 */
class Facade
{
	var $connectioncontrol;
	var $connection;
	var $dao;
	var $develModeData = false;

	/**
	 *
   * @param object $voObj vo for the autogenerator 
   * @param string $entity name to use a handmade DAO
   * @param string $module when DAO is handmade, specify module name
   *
   * @return object
   */
	function __construct( $voObj, $entity = false, $module=false )
	{
		$this->dao = DAO::Factory($voObj, $entity, $module);
	}

	/**
	 * get DDBB connection from Factory
   *
   * @return void
   */
	public function openConnection()
	{
		$this->connectioncontrol = Connection::Factory($this->develModeData);
	}

	/**
	 * devel Mode is used when want to execute database with other user
   *
	 * @param string $user DDBB username
	 * @param string $password DDBB password
   *
   * @return void
   */
	public function develMode($user, $password, $DB=false) {
		$this->develModeData = array();

		$this->develModeData['DB_USER'] = $user;
		$this->develModeData['DB_PASSWORD'] = $password;
		$this->develModeData['DB_NAME'] = $DB;
	}


	/**
	 * Interface for any facade method
   *
	 * @param string $name the called method
	 * @param array $args arguments
   *
   * @return void
   */
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
