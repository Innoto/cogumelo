<?php

//
// User Access Controller
//

user::load('controller/UserSessionController.php');
user::load('model/UserModel.php');

class UserAccessController
{
  //
  // Constructor
  //
  function UserAccesscontroller()
  {
    $this->setSessioncontrol( new UserSessionController() );
    $this->setSessiondata($this->sessioncontrol->getUser());
  }



  //
  // Login an Admin User
  //
  function userLogin($login, $password)
  {
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
  // Logout a User
  //
  function userLogout()
  {
    if($currentuser = $this->sessioncontrol->getUser())
    {
      $this->sessioncontrol->delUser();
      Cogumelo::log("User ".$currentuser['data']['login']." Logged out", 'UserLog');
      return true;
    }
    else
    {
      Cogumelo::log("Unable to Logout", 'UserLog');
      return false;
    }
  }

  function checkPermissions( $permissions = false, $specialPerm = false )
  {
    if( !is_array($permissions) && $permissions)
    {
      $permissions = array($permissions);
    }
    if( !is_array($specialPerm) && $specialPerm)
    {
      $specialPerm = array($specialPerm);
    }

    $user = $this->getSessiondata();
    $res = false;
/*
Cogumelo::console($user);
Cogumelo::console($permissions);
*/
    if( in_array( 'user:superAdmin' , $user['permissions']) ){
      $res = true;
    }
    else{
      if(is_array($specialPerm)) {
        //Si tiene permisos especiales
        $res = false;
        foreach ($specialPerm as $key => $perm){
          if(in_array( $perm, $user['permissions'] )){
            $res = true;
            break;
          }
        }
      }
      if(is_array($permissions) && (!$res)){
        $res = true;
        foreach ($permissions as $key => $perm){
          if(!in_array( $perm, $user['permissions'] )){
            $res = false;
            break;
          }
        }
      }
    }

    return $res;
  }

  //
  // Is current User Loged?
  //
  function isLogged()
  {
    if($this->sessiondata) {
      return true;
    }
    else {
      return false;
    }
  }


  function setSessioncontrol($sessioncontrol) {
    $this->sessioncontrol = $sessioncontrol;
  }

  function getSessioncontrol() {
    // set session data
    return $this->sessioncontrol;
  }

  function setSessiondata($sessiondata){
    $this->sessiondata = $sessiondata;
  }

  function getSessiondata(){
    return $this->sessiondata;
  }

}
