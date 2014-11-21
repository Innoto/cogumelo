<?php

Cogumelo::load('coreController/DataController.php');
filedata::load('model/FiledataVO.php');


//
// User Controller Class
//
class  FiledataController extends DataController
{
  var $data;

  function __construct()
  {
    $this->data = new Facade("Filedata", "filedata"); //In module user
    $this->voClass = 'FiledataVO';
  }
}
