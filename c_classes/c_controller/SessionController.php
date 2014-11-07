<?php

//
// Session controller (Superclass).
//

abstract class SessionController
{
  protected $session_id;    // Session ID String

  //
  // Constructor
  //
  function __construct() {}

  //
  // Set data in the session
  //
  public function setSession($data)
  {
    if ( !isset($_SESSION[$this->session_id]) )
      unset($_SESSION[$this->session_id]);

    $_SESSION[$this->session_id] = serialize($data);
  }

  //
  // Remove data from the session. Session is not set.
  //
  public function delSession()
  {
    if( isset($_SESSION[$this->session_id]) )
      unset($_SESSION[$this->session_id]);
  }

  //
  // Get current data information from session
  //
  public function getSession()
  {
    if( isset($_SESSION[$this->session_id]) )
    {
      $data = $_SESSION[$this->session_id];
      return unserialize($data);
    }
    else return false;
  }

  //
  // Check if the session is set.
  //
  public function isSession()
  {
    if( isset($_SESSION[$this->session_id]) )
      return true;
    else return false;
  }

  public function getSessionId()
  {
    return $this->session_id;
  }
}

