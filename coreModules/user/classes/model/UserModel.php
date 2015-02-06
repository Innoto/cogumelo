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

}



/*
Cogumelo::load('coreController/DataController.php');
user::load('model/UserVO.php');
filedata::autoIncludes();

//
// User Controller Class
//
class  UserController extends DataController
{
  var $data;

  function __construct()
  {
    $this->data = new Facade(false, "User", "user"); //In module user
    $this->voClass = 'UserVO';
  }

  //
  //  Update User password.
  //
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

  function authenticateUser($login, $password)
  {
    $data = $this->data->authenticateUser($login, sha1($password));

    if($data) {
      Cogumelo::log("authenticateUser SUCCEED with login=".$login, "UserLog");
      $this->data->updateTimeLogin($data->getter('id'), date("Y-m-d H:i:s", time()));
    }
    else {
      Cogumelo::log("authenticateUser FAILED with login=".$login.". User NOT authenticated", "UserLog");
    }

    return $data;
  }

  function createRelTmp($user){

    $filedataControl = new FiledataController();
    $filedata = $filedataControl->create($user['avatar']['values']);

    if($filedata){
      $user['avatar'] = $filedata->getter('id');
    }
    else {
      $user['avatar'] = "";
    }

    $data = $this->create($user);
  }
}*/




/*

Cogumelo::load('coreModel/mysql/MysqlDAO.php');
user::load('model/UserVO.php');

//
//  Mysql useradmin DAO
//

class MysqlUserDAO extends MysqlDAO
{
  var $VO = "UserVO";

  var $filters = array(
      'find' => "name  LIKE CONCAT('%',?,'%') OR login LIKE CONCAT('%', ?, '%')",
      'edadmax' => "edad <= ?",
      'edadmin' => "edad >= ?"
    );


  //
  //  Authenticate user
  //
  //  Return: UserVO (null if 0 rows)
  function authenticateUser($connectionControl, $login, $password)
  {
    $objVO  = new $this->VO();

    // SQL Query
    $strSQL = "SELECT * FROM `".$objVO::$tableName."` WHERE `login` = ? and `password` = ? ;";

    if( $res = $this->execSQL($connectionControl, $strSQL, array($login, $password)) ) {

      if($res->num_rows == 1){
        return $this->find($connectionControl, $login, 'login');
      }
      else{
        return false;
      }
    }
    else{
      return COGUMELO_ERROR;
    }

  }

  //
  //  Use an UserVO to update User last login
  //
  function updateTimeLogin($connectionControl, $id, $date)
  {
    $objVO  = new $this->VO();
    $strSQL = "UPDATE `".$objVO::$tableName."` SET timeLastLogin = ? WHERE `id` = ? ;";
    $res = $this->execSQL($connectionControl, $strSQL, array($date, $id));
    return $res;
  }
}*/