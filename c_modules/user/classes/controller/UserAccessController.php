<?php

//
// User Access Controller
//

user::load('UserSessionController.php');
user::load('UserController.php');
user::load('UserVO.php');

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
    $usercontrol= new UserController();
    if($logeduser = $usercontrol->authenticateUser( $login, $password ));
    {
      $this->sessioncontrol->setUser($logeduser);
      Cogumelo::log("Accepted User authentication: user ".$login." is logged", 'UserLog');
      return true;
    }
    else
    {
      Cogumelo::log("Failed UserAdmin authentication: user ".$login, 'UserLog');
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
      Cogumelo::log("User ".$currentuser->getter('login')." Logged out", 'UserLog');
      return true;
    }
    else
    {
      Cogumelo::log("Unable to Logout", 'UserLog');
      return false;
    }
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