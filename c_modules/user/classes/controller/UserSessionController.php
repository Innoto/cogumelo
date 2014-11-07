<?php

//
// Session controller (UserAdmin).
//

Cogumelo::Load('SessionController');
app::Load('UseradminVO');

class UserAdminSessionController extends SessionController
{
  //
  // Constructor
  //
  function UserAdminSessionController()
  {
    parent::__construct();
    $this->session_id = "UserAdmin-Session";
  }

  //
  // Set userdata in the session from UserVO
  //
  public function setUser($data)
  {
    $this->setSession($data);
  }

  //
  // Remove userdata from the session. Session is not set.
  //
  public function delUser()
  {
    $this->delSession();
  }

  //
  // Get current userdata information from session
  //
  public function getUser()
  {
    return $this->getSession();
  }

  //
  // Check if the user session is set.
  //
  public function isUserSet()
  {
    return $this->isSession();
  }
}
?>