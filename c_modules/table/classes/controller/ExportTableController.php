<?php

/**
* ExportTableController Class
*
* This abstract class have a batery of methods to export tables from TableController
*
* @author: pablinhob
*/

abstract class ExportTableController {

  abstract function headers($fileName);
  abstract function data($tableControl, $dataDAOResult);
 
}
