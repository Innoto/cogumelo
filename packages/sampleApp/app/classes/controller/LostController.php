<?php

Cogumelo::load('coreController/DataController.php');
Cogumelo::load('model/LostVO.php');


//
// Lost Controller Class
//
class LostController extends DataController
{
  var $data;

  function __construct()
  {	
      $this->data = new Facade("Lost");
      $this->voClass = 'LostVO';
  }
}
