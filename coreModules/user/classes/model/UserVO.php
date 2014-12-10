<?php

Cogumelo::load('coreModel/VO.php');

define( 'USER_STATUS_ACTIVE', 1 );
define( 'USER_STATUS_WAITING', 2 );
define( 'USER_STATUS_LOCKED', 3 );


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
      'size' => '30',
      'unique' => true
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
    'status'=> array(
      'name' => 'Estado',
      'type' => 'INT',
      'size' => '10'
    ),
    'avatar'=> array(
      'desc' => 'File',
      'type'=>'FOREIGN',
      'vo' => 'FiledataVO',
      'key' => 'id'
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

  function isActive(){
    return $this->status === USER_STATUS_ACTIVE;
  }
  function isWaiting(){
    return $this->status === USER_STATUS_WAITING;
  }
  function isLocked(){
    return $this->status === USER_STATUS_LOCKED;
  }

}