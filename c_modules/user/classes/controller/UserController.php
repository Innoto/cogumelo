<?php

Cogumelo::load('c_controller/DataController.php');
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
    $this->data = new Facade("User", "user"); //In module user
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

    Cogumelo::console( $user );

    $filedataControl = new FiledataController();
    $idfiledata = $filedataControl->create();
    $data = $this->data->create($user);
  }
}
