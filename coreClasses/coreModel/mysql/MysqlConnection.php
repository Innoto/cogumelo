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
   * Fetch just one result
   *
   * @param array $db_devel_auth in case of cogumelo Script process
   *
   * @return object
   */
  public function __construct( $db_devel_auth = false ) {
    if($db_devel_auth) {
      $this->DB_USER = $db_devel_auth['DB_USER'];
      $this->DB_PASSWORD = $db_devel_auth['DB_PASSWORD'];
      $this->DB_NAME = $db_devel_auth['DB_NAME'];
    }
    else {

      $this->DB_USER = Cogumelo::getSetupValue( 'db:user' );
      $this->DB_PASSWORD = Cogumelo::getSetupValue( 'db:password' );
      $this->DB_NAME = Cogumelo::getSetupValue( 'db:name' );
    }
    $this->connect();
  }


  /**
   * Connect Mysql: Only when dbinstance doesn't exist
   *
   * @return void
   */
  public function connect() {
    if($this->db == false) {
      $this->db = new mysqli(Cogumelo::getSetupValue( 'db:hostname' ) ,$this->DB_USER , $this->DB_PASSWORD, $this->DB_NAME,  Cogumelo::getSetupValue( 'db:port' ));
      if ($this->db->connect_error) {
          Cogumelo::debug(mysqli_connect_error());
      }
      else {
          Cogumelo::debug("MYSQLI: Connection Stablished to ".Cogumelo::getSetupValue( 'db:hostname' ));
      }
    }
  }

  /**
   * Start transaction
   *
   * @return void
   */
  public function transactionStart() {
    Cogumelo::debug("DB TRANSACTION START");
    mysqli_query($this->db ,"START TRANSACTION;");
    mysqli_query($this->db ,"BEGIN;");
  }


  /**
   * Commit transaction
   *
   * @return void
   */
  public function transactionCommit() {
    mysqli_query($this->db ,"COMMIT;");
  }

  /**
   * Commit transaction
   *
   * @return void
   */
  public function transactionRollback() {
    mysqli_query($this->db ,"ROLLBACK;");
  }




}
