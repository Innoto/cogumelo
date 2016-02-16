<?php

//
// User Access Controller
//

class DetectMobileController
{
  private $detect;
  //
  // Constructor
  //
  function __construct()
  {
    $this->detect = new Mobile_Detect;
  }

  function getDetectMobile(){
    return $this->detect;
  }
}
