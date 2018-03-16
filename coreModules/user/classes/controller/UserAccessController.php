<?php

//
// User Access Controller
//
//
user::load('controller/UserSessionController.php');
user::load('model/UserModel.php');

class UserAccessController {

  //
  // Constructor
  //
  public function __construct() {
    $this->setSessioncontrol( new UserSessionController() );
    $this->setSessiondata($this->sessioncontrol->getUser());
  }



  //
  // Login an Admin User
  //
  public function userLogin( $login, $password ) {

    $usermodel = new UserModel();
    $result = $usermodel->authenticateUser( $login, $password );

    if( $result['status'] === true ){
      $this->sessioncontrol->setUser($result['userdata']);
      Cogumelo::log( 'userLogin: Accepted User authentication: user '.$login.' is logged', 'UserLog' );
    }
    else {
      Cogumelo::log( 'userLogin: Failed User authentication: user '.$login, 'UserLog' );
      error_log( 'Cogumelo user module: ERROR LOGIN USER ('.$login.') - userLogin' );
    }

    return $result;
  }

  //
  // Login an Admin User
  //

  public function userAutoLogin( $login ) {
    $result = false;

    $usermodel= new UserModel();

    if( $logeduser = $usermodel->authenticateUserOnlyLogin( $login ) ) {
      $this->sessioncontrol->setUser( $logeduser['userdata'] );
      Cogumelo::log( 'userAutoLogin: Accepted User authentication: user '.$login.' is logged', 'UserLog' );
      $result = true;
    }
    else {
      Cogumelo::log( 'userAutoLogin: Failed User authentication: user '.$login, 'UserLog');
      error_log( 'Cogumelo user module: ERROR LOGIN USER ('.$login.') - userLogin' );
    }

    return $result;
  }


  //
  // Logout a User
  //
  public function userLogout() {
    if($currentuser = $this->sessioncontrol->getUser()) {
      $this->sessioncontrol->delUser();
      Cogumelo::log("User ".$currentuser['data']['login']." Logged out", 'UserLog');
      return true;
    }
    else {
      Cogumelo::log("Unable to Logout", 'UserLog');
      return false;
    }
  }

  public function checkPermissions( $permissions = false, $specialPerm = false ) {

    $permissions = ( !is_array($permissions) && $permissions ) ? array($permissions) : $permissions;
    $specialPerm = ( !is_array($specialPerm) && $specialPerm ) ? array($specialPerm) : $specialPerm;

    $user = $this->getSessiondata();
    $res = false;
/*
    Cogumelo::console($user);
    Cogumelo::console($permissions);
*/

    if( $user ) {
      if( in_array( 'user:superAdmin' , $user['permissions']) ){
        $res = true;
      }
      else{
        if(is_array($specialPerm)) {
          //Si tiene permisos especiales
          $res = false;
          foreach( $specialPerm as $key => $perm ) {
            if(in_array( $perm, $user['permissions'] )){
              $res = true;
              break;
            }
          }
        }
        if(is_array($permissions) && (!$res)){
          $res = true;
          foreach( $permissions as $key => $perm ) {
            if(!in_array( $perm, $user['permissions'] )){
              $res = false;
              break;
            }
          }
        }
      }
    }

    return $res;
  }

  //
  // Is current User Loged?
  //
  public function isLogged() {
    return ($this->sessiondata) ? true : false;
  }


  public function setSessioncontrol( $sessioncontrol ) {
    $this->sessioncontrol = $sessioncontrol;
  }

  public function getSessioncontrol() {
    // set session data
    return $this->sessioncontrol;
  }

  public function setSessiondata( $sessiondata ) {
    $this->sessiondata = $sessiondata;
  }

  public function getSessiondata() {
    return $this->sessiondata;
  }

}
