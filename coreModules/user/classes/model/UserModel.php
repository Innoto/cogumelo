<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');


define( 'USER_STATUS_ACTIVE', 1 );
define( 'USER_STATUS_WAITING', 2 );
define( 'USER_STATUS_LOCKED', 3 );


class UserModel extends Model
{

  static $tableName = 'user_user';
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
    'timeLastLogin' => array(
      'name' => 'Último acceso',
      'type'=>'DATETIME'
    ),
    'avatar'=> array(
      'name' => 'Avatar',
      'type'=>'FOREIGN',
      'vo' => 'FiledataModel',
      'key' => 'id'
    ),
    'timeCreateUser' => array(
      'name' => 'Fechas de creación',
      'type' => 'DATETIME'
    )


  );



  function __construct( $datarray= array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

  function isActive(){
    return $this->getter('status') === USER_STATUS_ACTIVE;
  }
  function isWaiting(){
    return $this->getter('status') === USER_STATUS_WAITING;
  }
  function isLocked(){
    return $this->getter('status') === USER_STATUS_LOCKED;
  }

}