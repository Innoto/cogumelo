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
      Cogumelo::load('coreModel/'.DB_ENGINE.'/'.ucfirst(DB_ENGINE).'AutogeneratorDAO.php');
    
      eval('$daoObjReturn = new '.ucfirst(DB_ENGINE).'AutogeneratorDAO( $voObj );');
    }
    else {
      
      $classPath = 'model/'. DB_ENGINE . '/'. ucfirst(DB_ENGINE) .$entity.'DAO';

      // check if entity is in module or is in main project
      if($module) {
        eval($module.'::load("'. $classPath .'.php");');
      }
      else
      {
        Cogumelo::load($classPath.'.php');
      }
      
      eval('$daoObjReturn = new '.ucfirst(DB_ENGINE).$entity.'DAO( );');
    }

    return $daoObjReturn;
  }
}