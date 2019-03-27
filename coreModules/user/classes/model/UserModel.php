<?php
Cogumelo::load('coreModel/VO.php');
Cogumelo::load('coreModel/Model.php');

define('LOGIN_FAILED', 0);
define('LOGIN_OK', 1);
define('LOGIN_BAN', 2);
define('LOGIN_USERDISABLED', 3);
define('LOGIN_USERUNKOWN', 4);


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
      'type' => 'VARCHAR',
      'size' => '255',
      'unique' => true
    ),
    'password'=> array(
      'type'=> 'VARCHAR',
      'size' => '255'
    ),
    'name'=> array(
      'type' => 'VARCHAR',
      'size' => '255'
    ),
    'surname'=> array(
      'type' => 'VARCHAR',
      'size' => '255'
    ),
    'email'=> array(
      'type' => 'VARCHAR',
      'size' => '255',
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
      'type' => 'BOOLEAN',
      'default' => 0
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
    ),
    'timeLastUpdate' => array(
      'type' => 'DATETIME'
    ),
    'hashUnknownPass'=> array(
      'type' => 'VARCHAR',
      'size' => '255'
    ),
    'hashVerifyUser'=> array(
      'type' => 'VARCHAR',
      'size' => '255'
    ),
    'loginTimeBan' => array(
      'type' => 'DATETIME'
    ),
    'loginFailAttempts'=> array(
      'type' => 'INT',
      'size' => '1'
    )
  );

  var $deploySQL = array(
    array(
      'version' => 'user#3',
      'sql'=> '
        ALTER TABLE user_user
        ADD COLUMN loginFailAttempts INT DEFAULT 0,
        ADD COLUMN loginTimeBan DATETIME;
      '
    ),
    array(
      'version' => 'user#1.9',
      'sql'=> '
        ALTER TABLE user_user
        ADD COLUMN hashUnknownPass VARCHAR(255),
        ADD COLUMN hashVerifyUser VARCHAR(255);
      '
    ),
    array(
      'version' => 'user#1.8',
      'sql'=> '
        ALTER TABLE user_user
        MODIFY COLUMN name VARCHAR(255),
        MODIFY COLUMN surname VARCHAR(255),
        MODIFY COLUMN email VARCHAR(255),
        MODIFY COLUMN login VARCHAR(255),
        MODIFY COLUMN password VARCHAR(255);
      '
    ),
    array(
      'version' => 'user#1.6',
      'sql'=> '
        ALTER TABLE user_user
        Add COLUMN timeLastUpdate DATETIME
      '
    ),
    array(
      'version' => 'user#1.5',
      'sql'=> '
        ALTER TABLE user_user
        MODIFY COLUMN verified BOOLEAN DEFAULT 0
      '
    ),
    array(
      'version' => 'user#1.2',
      'sql'=> '
        ALTER TABLE user_user
        Add COLUMN verified INT
      '
    )
  );

  static $extraFilters = array(
    'find' => "UPPER(user_user.surname)  LIKE CONCAT('%',UPPER(?),'%') OR user_user.login LIKE CONCAT('%', UPPER(?), '%')",
    'tableSearch' => " ( UPPER( user_user.name ) LIKE CONCAT( '%', UPPER(?), '%' ) OR UPPER( user_user.surname ) LIKE CONCAT( '%', UPPER(?), '%' ) OR UPPER( user_user.login ) LIKE CONCAT( '%', UPPER(?), '%' ) OR user_user.id = ? )",
    'idIn' => ' user_user.id IN (?) ',
    // 'login' => "login = CONCAT('', ? ,'')"
    // 'edadmax' => "edad <= ?",
    // 'edadmin' => "edad >= ?"
  );


  public function __construct( $datarray = array(), $otherRelObj = false ) {
    parent::__construct( $datarray, $otherRelObj );
  }

  public function isActive(){
    return $this->getter('active');
  }

  public function equalPassword( $password ) {
  	//return ($this->getter('password') === sha1($password));
  	return password_verify($password, $this->getter('password') );
  }

  public function setPassword( $password ){
    //$this->setter('password', sha1($password));
    $this->setter('password', password_hash($password, PASSWORD_BCRYPT));
  }

  public function authenticateUser( $login, $password ) {
    $userO = $this->listItems( array('filters' => array('login' => $login), 'affectsDependences' => array( 'UserPermissionModel') ))->fetch();
    $data = [];
    if( $userO ){
      $loginTimeBan = $userO->getter('loginTimeBan');
      $confTimeBan = 15; //minutes banned
      $diffTimeBan = (time()-strtotime($loginTimeBan))/60;

      if(!empty($loginTimeBan)){
        if( $diffTimeBan < $confTimeBan ){
          $data = [
            'status' => false,
            'advancedstatus' => LOGIN_BAN,
            'restTimeBan' => $diffTimeBan
          ];
        }else{
          $userO->setter('loginTimeBan', NULL);
          $userO->setter('loginFailAttempts', 0);
          $userO->save();
        }
      }

      if(empty($data)){
        if(!password_verify($password, $userO->getter('password'))){
          $this->userLoginFailed($userO);
          $data = [
            'status' => false,
            'advancedstatus' => LOGIN_FAILED,
            'restLoginAttempts' => (5 - $userO->getter('loginFailAttempts'))
          ];
        }
      }

      if(empty($data)){
        if($userO->getter('active') !== 1){
          $data = [
            'status' => false,
            'advancedstatus' => LOGIN_DISABLED
          ];
        }
      }
    }
    else{
      $data = [
        'status' => false,
        'advancedstatus' => LOGIN_USERUNKOWN
      ];
    }

    if(empty($data)) {

      $data = [
        'status' => true,
        'advancedstatus' => LOGIN_OK,
        'userdata' => $this->loginIsOk($userO)
      ];
    }
    else {
      Cogumelo::log("authenticateUser FAILED with login=".$login.". User NOT authenticated", "UserLog");
    }
    return $data;
  }

  public function authenticateUserOnlyLogin( $login ) {
    $userO = $this->listItems( array('filters' => array('login' => $login), 'affectsDependences' => array( 'UserPermissionModel') ))->fetch();
    if($userO) {

      $data = [
        'status' => true,
        'advancedstatus' => LOGIN_OK,
        'userdata' => $this->loginIsOk($userO)
      ];

    }
    else {
      Cogumelo::log("authenticateUser FAILED with login=".$login.". User NOT authenticated", "UserLog");
    }
    return $data;
  }

  public function userLoginFailed($userO){
    $loginFailAttempts = $userO->getter('loginFailAttempts');
    $loginFailAttempts++;
    if($loginFailAttempts > 4){
      $loginFailAttempts = 0;
      $userO->setter('loginTimeBan', date("Y-m-d H:i:s"));
    }
    $userO->setter('loginFailAttempts', $loginFailAttempts);
    $userO->save();
  }

  public function loginIsOk( $userO ) {
    Cogumelo::log("authenticateUser SUCCEED with login=".$userO->getter('login'), "UserLog");

    $userPermissionArray = $userO->getterDependence('id', 'UserPermissionModel');
    $uPermArray = array();
    if($userPermissionArray) {
      foreach( $userPermissionArray as $key => $uPerm ) {
        $uPermArray[] = $uPerm->getter('permission');
      }
    }
    $userO->setter('loginFailAttempts', 0);
    $userO->setter('timeLastLogin' , date("Y-m-d H:i:s", time()));
    $userO->save();
    $data = array();
    $userAllData = $userO->getAllData();
    unset($userAllData['data']['password']);

    $data['data'] = $userAllData['data'];
    $data['permissions'] = $uPermArray;

    return $data;
  }

  public function updatePassword( $id, $password ) {
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
