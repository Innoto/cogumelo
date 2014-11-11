<?php

Cogumelo::load('c_model/VO.php');

class UserVO extends VO
{
  static $tableName = 'user';
  static $cols = array(
    'id' => array(
      'type' => 'INT',
      'primarykey' => true,
      'autoincrement' => true
    ),
    'login' => array(
      'name' => 'Login',
      'type' => 'CHAR',
      'size' => '10'
    ),
    'password'=> array(
      'name' => 'Contraseña',
      'type'=>'CHAR',
      'size' => '200'
    ),
    'name'=> array(
      'name' => 'Nombre',
      'type' => 'CHAR',
      'size' => '50'
    ),
    'surname'=> array(
      'name' => 'Apellidos',
      'type' => 'CHAR',
      'size' => '100'
    ),
    'email'=> array(
      'name' => 'Email',
      'type' => 'CHAR',
      'size' => '50'
    ),
    'role'=> array(
      'name' => 'Rol',
      'type' => 'INT',
      'size' => '10'
    ),
    'description'=> array(
      'name' => 'Descripción',
      'type' => 'TEXT',
      'size' => '300'
    ),
    'avatar'=> array(
      'name' => 'Descripción',
      'type' => 'INT',
      'size' => '10'
    ),
    'timeLastLogin' => array(
      'name' => 'Último acceso',
      'type'=>'DATETIME'
    ),
    'timeCreateUser' => array(
      'name' => 'Fechas de creación',
      'type' => 'DATETIME'
    )
  );



  function __construct($datarray = array())
  {
    parent::__construct($datarray);
  }

}