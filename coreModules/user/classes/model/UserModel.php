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

  var $filters = array(
      'find' => "UPPER(surname)  LIKE CONCAT('%',UPPER(?),'%') OR login LIKE CONCAT('%', UPPER(?), '%')"
//      'edadmax' => "edad <= ?",
//      'edadmin' => "edad >= ?"
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


  /**
  * undocumented function
  *
  * @return void
  * @author
  **/
  function authenticateUser($login, $password)
  {
    $userO = $this->listItems( array('filters' => array('login' => $login)) )->fetch();

    if( $userO ){
      $data = ($userO->getter('password') == sha1($password)) ? true : false;
    }
    else{
      $data = false;
    }

    if($data) {
      Cogumelo::log("authenticateUser SUCCEED with login=".$login, "UserLog");
      $userO->setter('timeLastLogin' , date("Y-m-d H:i:s", time()));
      $userO->save();
      $data = $userO;
    }
    else {
      Cogumelo::log("authenticateUser FAILED with login=".$login.". User NOT authenticated", "UserLog");
    }

    return $data;
  }
  /**
  * undocumented function
  *
  * @return void
  * @author
  **/
  function updatePassword($id, $password)
  {
    $data = $this->data->updatePassword($id, $password);
    if($data) {
      Cogumelo::log("UpdatePassword SUCCEED with ID=".$id, "UserLog");
    }
    else{
      Cogumelo::log("UpdatePassword FAILED with ID=".$id, "UserLog");
    }
    return $data;
  }
}

