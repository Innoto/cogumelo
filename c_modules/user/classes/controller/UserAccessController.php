<?php


//
// UserAdmin Access Controller
//

Cogumelo::Load('AccessController');

app::Load('UseradminSessionController');
app::Load('UseradminController');
app::Load('UseradminVO');

class UserAccessController extends AccessController
{
  //
  // Constructor
  //
  function UserAccesscontroller()
  {
    $this->setSessioncontrol( new UserAdminSessionController() );
    $this->setSessiondata($this->sessioncontrol->getUser());
  }

  //
  // Is current User a Loged Administrator?
  //
  function isAdmin()
  {
    if($this->sessiondata)
       return true;
    else return false;
  }

  //
  // Login an Admin User
  //
  function UserAdminLogin($username, $passwd)
  {
    $usercontrol= new UserAdminController();

    $user=new UserAdminVO();
    $user->setter('login', $username);
    $user->setter('passwd', $passwd);

    if($logeduser = $usercontrol->AuthenticateUseradmin($user))
    {
      $this->sessioncontrol->setUser($logeduser);
      Cogumelo::Log("Accepted UserAdmin authentication: user ".$username." is logged", 2);
      return true;
    }
    else
    {
      Cogumelo::Log("Failed UserAdmin authentication: user ".$username, 2);
      return false;
    }
  }

  //
  // Logout a User
  //
  function Logout()
  {
    if($currentuser = $this->sessioncontrol->getUser())
    {
      $this->sessioncontrol->delUser();
      Cogumelo::Log("UserAdmin ".$currentuser->getter('login')." Logged out", 2);
      return true;
    }
    else
    {
      Cogumelo::Log("Unable to Logout", 2);
      return false;
    }
  }

  //
  // Is current User Loged?
  //
  function isLoged()
  {
    if($this->sessiondata) return true;
    else return false;
  }

  function setSessioncontrol($sessioncontrol) {
    $this->sessioncontrol = $sessioncontrol;
  }

  function getSessioncontrol() {
    // set session data
    Cogumelo::setUserInfo($this->sessiondata->getTableName().' id: '. $this->sessiondata->getter( $this->sessiondata->getKeyId()) );

    return $this->sessioncontrol;
  }

  function setSessiondata($sessiondata){
    $this->sessiondata = $sessiondata;
  }

  function getSessiondata(){
    return $this->sessiondata;
  }
}