<?php


//
// DAO Superclass
//

class DAO
{
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
?>