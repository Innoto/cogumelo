<?php

Cogumelo::load('c_controller/DataController');
testmodule::load('model/CousaVO');

//
// Cousa Controller Class
//
class  CousaController extends DataController
{
  var $data;

  function __construct()
  {   
    $this->data = new Facade("Cousa", "testmodule");
    $this->voClass = 'CousaVO';
  }
}