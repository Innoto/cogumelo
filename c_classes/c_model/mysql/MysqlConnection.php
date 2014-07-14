<?php


Cogumelo::load('c_model/Connection');

//
// Mysql connection class
//

class MysqlConnection extends Connection
{
    var $db = false;
    var $stmt = false;

    var $DB_USER;
    var $DB_PASSWORD;
    var $DB_NAME;

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


    /*
     *  Only starts the db connection if doesn't exist 
     */
    function connect() {


        if($this->db == false) {
            @$this->db = new mysqli(DB_HOSTNAME ,$this->DB_USER , $this->DB_PASSWORD, $this->DB_NAME,  DB_PORT);


            if ($this->db->connect_error)
                Cogumelo::debug(mysqli_connect_error());
            else
                Cogumelo::debug("MYSQLI: Connection Stablished to ".DB_HOSTNAME);
            
            @mysqli_query($this->db ,"START TRANSACTION;");
        }

    }
    
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

?>