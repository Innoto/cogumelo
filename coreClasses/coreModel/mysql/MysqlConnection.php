<?php


Cogumelo::load('coreModel/Connection.php');




/**
 * Mysql connection class
 *
 * @package Cogumelo Model
 */
class MysqlConnection extends Connection
{
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

  }


  /**
   * Connect Mysql: Only when dbinstance doesn't exist 
   *
   * @return void
   */
  function connect() {
    if($this->db == false) {
        @$this->db = new mysqli(DB_HOSTNAME ,$this->DB_USER , $this->DB_PASSWORD, $this->DB_NAME,  DB_PORT);

        if ($this->db->connect_error) {
            Cogumelo::debug(mysqli_connect_error());
        }
        else {
            Cogumelo::debug("MYSQLI: Connection Stablished to ".DB_HOSTNAME);
        }
        
        @mysqli_query($this->db ,"START TRANSACTION;");
    }
  }
  

  /**
   * Close mysql connection
   *
   * @return void
   */
  function close()
  {
    // close stmt if exist
    if($this->stmt)
        $this->stmt->close();

    // close mysqli
    if($this->db){
        $this->db->close();
        Cogumelo::debug("MYSQLI: Connection closed");
    }
  }

}
