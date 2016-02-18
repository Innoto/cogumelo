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


     $className = ucfirst(cogumeloGetSetupValue( 'db:engine' )) ."DBUtils";
     $classFile = 'coreModel/'. cogumeloGetSetupValue( 'db:engine' ) . '/'. ucfirst(cogumeloGetSetupValue( 'db:engine' )) ."DBUtils";
     Cogumelo::load($classFile.'.php');


     return $className::$method( $params );
   }
}
