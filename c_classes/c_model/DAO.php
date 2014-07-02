<?php


//
// DAO Superclass
//

class DAO
{
  public static function factory($entity, $module)
  {
    

    $classPath = 'model/'. DB_ENGINE . '/'. ucfirst(DB_ENGINE) .$entity.'DAO';

    // check if entity is in module or is in main project
    if($module) {
      eval($module.'::load("'. $classPath .'");');
    }
    else
    {
      Cogumelo::load($classPath);
    }



    $daoObj = ucfirst(DB_ENGINE).$entity;

    eval('$daoObjReturn = new '.$daoObj.'DAO();');
    return $daoObjReturn;
  }
}
?>