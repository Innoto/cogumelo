<?php


/**
 * Abstract data access object
 *
 * @package Cogumelo Model
 */
class DAO
{

  /**
  * Factory
  *
  * @param object $voObj vo for the autogenerator
  * @param string $entity name to use a handmade DAO
  * @param string $module when DAO is handmade, specify module name
  *
  * @return object
  */
  public static function factory($voObj, $entity, $module)
  {

    if($voObj != false) {
      Cogumelo::load('coreModel/'.cogumeloGetSetupValue( 'db:engine' ).'/'.ucfirst(cogumeloGetSetupValue( 'db:engine' )).'AutogeneratorDAO.php');

      eval('$daoObjReturn = new '.ucfirst(cogumeloGetSetupValue( 'db:engine' )).'AutogeneratorDAO( $voObj );');
    }
    else {

      $classPath = 'model/'. cogumeloGetSetupValue( 'db:engine' ) . '/'. ucfirst(cogumeloGetSetupValue( 'db:engine' )) .$entity.'DAO';

      // check if entity is in module or is in main project
      if($module) {
        eval($module.'::load("'. $classPath .'.php");');
      }
      else
      {
        Cogumelo::load($classPath.'.php');
      }

      eval('$daoObjReturn = new '.ucfirst(cogumeloGetSetupValue( 'db:engine' )).$entity.'DAO( );');
    }

    return $daoObjReturn;
  }
}
