<?php

Cogumelo::load('c_controller/DataController');
user::load('model/UserVO');


//
// User Controller Class
//
class  UserController extends DataController
{
  var $data;

  function __construct()
  {
    $this->data = new Facade("User");
    $this->voClass = 'UserVO';
  }

  //
  //  Update User password.
  //
  function UpdatePassword($id, $password)
  {
      $data = $this->data->UpdatePassword($id, $password);
      if($data) {
        Cogumelo::log("UpdatePassword SUCCEED with ID=".$id, "UserLog");
      }
      else{
        Cogumelo::log("UpdatePassword FAILED with ID=".$id, "UserLog");
      }
    return $data;
  }

  function AuthenticateUser($user)
  {
    $data = $this->data->AuthenticateUser($user);

    if($data) {
      Cogumelo::log("AuthenticateUser SUCCEED with login=".$user->getter('login'), "UserLog");
    }
    else {
      Cogumelo::log("AuthenticateUser FAILED with login=".$user->getter('login').". Useradmin NOT authenticated", "UserLog");
    }
    return $data;
  }
}
