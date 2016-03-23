<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');



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
      'type' => 'CHAR',
      'size' => '30',
      'unique' => true
    ),
    'password'=> array(
      'type'=>'CHAR',
      'size' => '200'
    ),
    'name'=> array(
      'type' => 'CHAR',
      'size' => '50'
    ),
    'surname'=> array(
      'type' => 'CHAR',
      'size' => '100'
    ),
    'email'=> array(
      'type' => 'CHAR',
      'size' => '50',
      'unique' => true
    ),
    'description'=> array(
      'type' => 'TEXT',
      'size' => '300',
      'multilang' => true
    ),
    'active'=> array(
      'type' => 'INT',
      'size' => '1'
    ),
    'verified'=> array(
      'type' => 'INT',
      'size' => '1'
    ),
    'timeLastLogin' => array(
      'type'=>'DATETIME'
    ),
    'avatar'=> array(
      'type'=>'FOREIGN',
      'vo' => 'FiledataModel',
      'key' => 'id'
    ),
    'timeCreateUser' => array(
      'type' => 'DATETIME'
    )


  );

  static $extraFilters = array(
      'find' => "UPPER(surname)  LIKE CONCAT('%',UPPER(?),'%') OR login LIKE CONCAT('%', UPPER(?), '%')",
//      'login' => "login = CONCAT('', ? ,'')"
//      'edadmax' => "edad <= ?",
//      'edadmin' => "edad >= ?"
    );


  function __construct( $datarray= array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

  function isActive(){
    return $this->getter('active');
  }

  function equalPassword( $password ){
    return ($this->getter('password') === sha1($password));
  }

  function setPassword( $password ){
    $this->setter('password', sha1($password));
  }


  function authenticateUser($login, $password)
  {
    $userO = $this->listItems( array('filters' => array('login' => $login), 'affectsDependences' => array( 'UserPermissionModel') ))->fetch();
    if( $userO ){
      $data = (($userO->getter('password') == sha1($password)) && ($userO->getter('active') == 1)) ? true : false;
    }
    else{
      $data = false;
    }

    if($data) {
      $data = $this->loginIsOk($userO);
    }
    else {
      Cogumelo::log("authenticateUser FAILED with login=".$login.". User NOT authenticated", "UserLog");
    }
    return $data;
  }

  function authenticateUserOnlyLogin($login)
  {
    $userO = $this->listItems( array('filters' => array('login' => $login), 'affectsDependences' => array( 'UserPermissionModel') ))->fetch();
    if($userO) {
      $data = $this->loginIsOk($userO);
    }
    else {
      Cogumelo::log("authenticateUser FAILED with login=".$login.". User NOT authenticated", "UserLog");
    }
    return $data;
  }

  function loginIsOk( $userO ){
    Cogumelo::log("authenticateUser SUCCEED with login=".$userO->getter('login'), "UserLog");

    $userPermissionArray = $userO->getterDependence('id', 'UserPermissionModel');
    $uPermArray = array();
    if($userPermissionArray) {
      foreach ($userPermissionArray as $key => $uPerm) {
        $uPermArray[] = $uPerm->getter('permission');
      }
    }
    $userO->setter('timeLastLogin' , date("Y-m-d H:i:s", time()));
    $userO->save();

    $data = array();
    $data['data'] = $userO->data;
    $data['permissions'] = $uPermArray;

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
