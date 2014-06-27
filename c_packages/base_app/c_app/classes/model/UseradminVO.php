<?php

Cogumelo::load('c_model/VO');

// Predefine security access levels
define('USERADMIN_LEVEL_HIGH',3);
define('USERADMIN_LEVEL_MEDIUM',2);
define('USERADMIN_LEVEL_LOW',1);


class UseradminVO extends VO
{ 
  static $tableName = 'useradmin';
  static $cols = array(
    'id' => array(
      'type'=>'INT', 
      'primarykey'=>true,
      'autoincrement'=>true
    ),
    'login' => array(
      'name' => 'Alias', 
      'type'=>'CHAR', 
      'size'=>10
    ),
    'passwd'=> array(
      'name' => 'Contraseña', 
      'type'=>'CHAR',
      'size' => '30'
    ),
    'name'=> array(
      'name' => 'Nombre', 
      'type'=>'CHAR', 
      'size'=>50
    ),
    'time_lastlogin' => array(
      'name' => 'Último acceso', 
      'type'=>'DATETIME'
    ),
    'userdata_id' => array(
      'name' => 'Datos extendidos',
      'type' => 'INT',
      'foreign_key' => 'id'
    )
  );



  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }
  


  ///////////////////////////////////////
  /////// Aditional getters //////////
  /////////////////////////////////////

  function isLevelHigh()
  {
    return($this->getter('level') == USERADMIN_LEVEL_HIGH);
  }
  
  function isLevelMedium()
  {
    return($this->getter('level') == USERADMIN_LEVEL_MEDIUM);
  }
  
  function isLevelLow()
  {
    return($this->getter('level') == USERADMIN_LEVEL_LOW);
  }
  
  function getTimeLastLogin_print()
  {
    $date = explode( ' ', $this->getter('time_lastlogin'));
    $datepart = explode( '-', $date[0]);
    $day = $datepart[2]; $month = $datepart[1]; $year = $datepart[0];
    return($day."/".$month."/".$year);
  }
  
  ///////////////////////////////////////
  /////// Set Methods //////////////////
  ///////////////////////////////////// 

  function setLevelHigh()
  {
    $this->setter('level', USERADMIN_LEVEL_HIGH);
  }
  
  function setLevelMedium()
  {
    $this->setter('level', USERADMIN_LEVEL_MEDIUM);
  }
  
  function setLevelLow()
  {
    $this->setter('level', USERADMIN_LEVEL_LOW);
  }
  
  function setTimeLastLogin($timeLastLogin)
  {
    $this->setter('time_lastlogin', $timeLastLogin);
  }
}
?>