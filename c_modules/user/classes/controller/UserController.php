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
    $data = $this->data->authenticateUser($login, $password);

    if($data) {
      Cogumelo::log("authenticateUser SUCCEED with login=".$login, "UserLog");
    }
    else {
      Cogumelo::log("authenticateUser FAILED with login=".$login.". Useradmin NOT authenticated", "UserLog");
    }
    return $data;
  }
}
