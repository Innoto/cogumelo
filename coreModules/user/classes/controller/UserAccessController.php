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
  public function UserAccesscontroller() {
    $this->setSessioncontrol( new UserSessionController() );
    $this->setSessiondata($this->sessioncontrol->getUser());
  }



  //
  // Login an Admin User
  //
  public function userLogin( $login, $password ) {
    $usermodel= new UserModel();
    if($logeduser = $usermodel->authenticateUser( $login, $password )) {
      $this->sessioncontrol->setUser($logeduser);
      Cogumelo::log("Accepted User authentication: user ".$login." is logged", 'UserLog');
      return true;
    }
    else {
      //Cogumelo::log("Failed User authentication: user ".$login, 'UserLog');
      return false;
    }
  }

  //
  // Login an Admin User
  //
  public function userAutoLogin( $login ) {
    $usermodel= new UserModel();
    if($logeduser = $usermodel->authenticateUserOnlyLogin( $login )) {
      $this->sessioncontrol->setUser($logeduser);
      Cogumelo::log("Accepted User authentication: user ".$login." is logged", 'UserLog');
      return true;
    }
    else {
      //Cogumelo::log("Failed User authentication: user ".$login, 'UserLog');
      return false;
    }
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
