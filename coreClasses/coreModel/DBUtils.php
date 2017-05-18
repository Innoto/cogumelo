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


     $className = ucfirst(Cogumelo::getSetupValue( 'db:engine' )) ."DBUtils";
     $classFile = 'coreModel/'. Cogumelo::getSetupValue( 'db:engine' ) . '/'. ucfirst(Cogumelo::getSetupValue( 'db:engine' )) ."DBUtils";
     Cogumelo::load($classFile.'.php');


     return $className::$method( $params );
   }
}
