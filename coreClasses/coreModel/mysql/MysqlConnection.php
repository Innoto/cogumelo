<?php


Cogumelo::load('coreModel/Connection.php');


/**
 * Mysql connection class
 *
 * @package Cogumelo Model
 */
class MysqlConnection extends Connection
{
    var $transactStarted = false;
    var $transactError = false;
    var $db = false;
    var $stmt = false;

    var $DB_USER;
    var $DB_PASSWORD;
    var $DB_NAME;


  /**
   * fetch just one result
   *
   * @param array $db_devel_auth in case of cogumelo Script process
   *
   * @return object
   */
  function __construct($db_devel_auth = false){

    if($db_devel_auth) {
      $this->DB_USER = $db_devel_auth['DB_USER'];
      $this->DB_PASSWORD = $db_devel_auth['DB_PASSWORD'];
      $this->DB_NAME = $db_devel_auth['DB_NAME'];
    }
    else {

      $this->DB_USER = DB_USER;
      $this->DB_PASSWORD = DB_PASSWORD;
      $this->DB_NAME = DB_NAME;
    }


    $this->connect();
  }


  /**
   * Connect Mysql: Only when dbinstance doesn't exist 
   *
   * @return void
   */
  function connect() {

    if($this->db == false) {
      $this->db = new mysqli(DB_HOSTNAME ,$this->DB_USER , $this->DB_PASSWORD, $this->DB_NAME,  DB_PORT);
      if ($this->db->connect_error) {
          Cogumelo::debug(mysqli_connect_error());
      }
      else {
          Cogumelo::debug("MYSQLI: Connection Stablished to ".DB_HOSTNAME);
      }

    }
  }

  /**
   * Start transaction
   *
   * @return void
   */
  public function transactionStart()
  {
    if( ! $this->transactStarted ){
      $this->db->autocommit(false);
      Cogumelo::debug("TRANSACTION START");
      mysqli_query($this->db ,"START TRANSACTION;");
      $this->transactStarted = true;
    }

  }


  /**
   * End transaction
   *
   * @return void
   */
  public function transactionEnd()
  {
    if( $this->transactError ) {
      $this->transactionRollback();
      Cogumelo::debug("TRANSACTION ROLLBACK");
    }
    else {
      $this->transactionCommit();
      Cogumelo::debug("TRANSACTION COMMIT");
    }

    $this->transactStarted = false;
  }

  /**
   * Commit transaction
   *
   * @return void
   */
  public function transactionCommit()
  {
    $this->db->commit();
  }

  /**
   * Commit transaction
   *
   * @return void
   */
  public function transactionRollback()
  {
    $this->db->rollback();
  }


  public function transactionError() {
     $this->transactError = true;
  }

}
