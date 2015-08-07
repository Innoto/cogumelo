<?php


/**
* DBUtils
*
* @package Cogumelo Model
*/
class DBUtils
{
  static function __callStatic($method, $params)
   {
     $className = ucfirst(DB_ENGINE) ."DBUtils";
     $classFile = 'coreModel/'. DB_ENGINE . '/'. ucfirst(DB_ENGINE) ."DBUtils";
     Cogumelo::load($classFile.'.php');


     return $className::$method( $method );
   }
}
