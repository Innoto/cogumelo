<?php

Cogumelo::load('coreView/View.php');
common::autoIncludes();
Cogumelo::autoIncludes();

/**
* Clase Master to extend other application methods
*/
class MasterView extends View
{

  function __construct($baseDir){
    parent::__construct($baseDir);
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  function master($urlPath=''){

/*
    $dependencesControl = new DependencesController();
    $dependencesControl->loadModuleIncludes('devel');
*/

    $this->common();
    $this->template->exec();
  }

  function common() {
    $this->template->addClientScript('js/default.js');
    $this->template->setTpl('default.tpl');
  }

  function testdata(){
    

    echo "<pre>";


    user::load('model/UserModel.php');
    
    $user = new UserModel();

    $u = $user->listItems( array('value'=>13, 'affectsDependences'=>true ) );
    $u = $u->fetch();
    //$u->fetch();
   // $u->setter('surname', 'Mamón');
//    $u->save();
    echo $u->getter('name'). ' '.$u->getter('surname'). ' Con rol tipo:'. $u->getterDependence('id')[0]->getterDependence('role')->getter('name');


/*
    $users = $user->listItems( 
      array(
        'filters'=>array('find'=>'pablo'),
        'affectsDependences' => true 
      ) 
    );

    $u  = $users->fetch();
    $u->setter('name', 'Blanco');
    $u->save();
*/
    //$u->delete(array( 'affectsDependences' => true ) );

/*    while ($u  = $users->fetch() ) {
      //$u->setter('name', 'novonome');
      //var_dump( $u->getAllData() );
      var_dump($u->getDepInLinearArray($u));
      //$u->delete();  
    }
*/


/*
    $u  = $users->fetch();
    $usD = $u->getDepInLinearArray($u);
    $usD[2]['ref']->setter('description', 'SUPERDOMINATOR');
    //var_dump($u->getAllData());
*/

    
  //  $u  = $users->fetch();
//    var_dump($u);


    //var_dump($u->getAllData());
    //$u->delete( array('affectsDependences'=>true) );
    //$usD = $u->getDepInLinearArray($u);


    

  }

  function page404() {
    echo 'PAGE404: Recurso non atopado';
  }

}

