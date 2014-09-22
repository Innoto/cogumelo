<?php

Cogumelo::load('c_controller/DataController');
Cogumelo::load('model/LostVO');


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
