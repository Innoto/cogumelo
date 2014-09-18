<?php

Cogumelo::load('c_model/VO');

// Predefine security access levels

class LostVO extends VO
{ 
  static $tableName = 'lost';
  static $cols = array(
    'id' => array(
      'type' => 'INT', 
      'primarykey' => true,
      'autoincrement' => true
    ),
    'lostName' => array(
      'name' => 'Name', 
      'type' => 'CHAR', 
      'size' => 20
    ),
    'lostSurname' => array(
      'name' => 'Surname', 
      'type' => 'CHAR', 
      'size' => 40
    ),
    'lostEmail' => array(
      'name' => 'Email', 
      'type' => 'CHAR', 
      'size' => 30
    ),
    'lostProvince' => array(
      'name' => 'Province', 
      'type' => 'CHAR', 
      'size' => 30
    ),
    'lostPassword'=> array(
      'name' => 'Contraseña', 
      'type' => 'CHAR',
      'size' => '10'
    ),
    'lostPhone'=> array(
      'name' => 'Telefono', 
      'type' => 'CHAR', 
      'size' => 12
    )
  );

  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }
  
}
?>