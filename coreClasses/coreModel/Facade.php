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
    if( $module != 'devel' ) {
      $this->getConnection();
    }
  }

  /**
   * get DDBB connection from Factory
   *
   * @return void
   */
  public function getConnection()
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

    $this->getConnection();
  }



  /**
   * Start transaction
   *
   * @return void
   */
  public function transactionStart()
  {
    $this->connectioncontrol->transactionStart();
  }

  /**
   * Commit transaction
   *
   * @return void
   */
  public function transactionCommit()
  {
    $this->connectioncontrol->transactionCommit();
  }

  /**
   * Rollback transaction
   *
   * @return void
   */
  public function transactionRollback()
  {
    $this->connectioncontrol->transactionRollback();
  }



  /**
   * Interface for any facade method
   *
   * @param string $name the called method
   * @param array $args arguments
   *
   * @return mixed
   */
  function __call($name, $args){

    // set arguments as string
    $args_str = '';
    foreach($args as $akey =>$arg){
      $args_str .= (', $args['. $akey .']');
    }


    eval('$data = $this->dao->'.$name. '($this->connectioncontrol'. $args_str . '); ');

    if($data === COGUMELO_ERROR) {
      Cogumelo::error('Error in facade calling : '.$name);
    }

    return $data;
  }

}
