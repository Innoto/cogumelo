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
    //$u = $user->listItems( array('affectsDependences'=> true ))->fetch();
    $u = $user->listItems( array('affectsDependences'=>array('UserRoleModel', 'RoleModel', 'FiledataModel') ))->fetch();

    var_dump( $u->getAllData());


/*
  user::load('model/UserModel.php');
  $user = new UserModel( ['login'=>'olasdfteu', 'email'=>'ola@teu.com', 'surname'=>'pablo', 'name'=>'blanco' ] );
  
  $user->setterDependence( 'id', new FileDataModel( ['name'=>'blabla', 'originalName'] ) );

  $user->setterDependence( 'id', new UserRoleModel( ) )->setterDependence( 'role', new RoleModel( ['nome' => 'fukee/*r', 'description'=>'A motherfuker'] ) )->setterDependence('id', new RolePermissionModel() )->setterDependence('permission', new PermissionModel(['name'=>'fukinpermission']) ) ;
  
  $user->save(['affectsDependences' =>true]);
  var_dump($user->getAllData() );
*/

/*
  user::load('model/UserModel.php');
  $user = (new UserModel())->listItems(['affectsDependences' =>true])->fetch();
  var_dump($user->getAllData() );
*/
/*
      $user = new UserModel();
      $user->setterDependence(  new FiledataModel() );
      var_dump($user->depData);
*/
      //$user->save( array( 'affectsDependences' => true ));
  







    //$user->setter('name','tal')->setter('surname', 'cual')->setter('login', 'puto')->save();

    //var_dump($user->getAllData());


  /*  $user->setter('id', 13);
    $user->setter('name', 'blanco');
    echo "<br><br><br>";
    var_dump( $user->exist() );*/

    //$u = $user->listItems( array('filters'=>array('login'=>'pablo'), 'affectsDependences'=>true ) )->fetch();

/*
    //$u->setter('surname', 'Pablo');
    //$u->save();
    //$u->setterDependence( new UserRoleModel( ) )->setterDependence( new RoleModel( array('nome' => 'fuker') ) )->setterDependence( new RolePermissionModel() )->setterDependence( new PermissionModel() ) ;

    $u->getterDependence('id')[0]->getterDependence('role')->setter('name', 'Usuariomierda');
    $u->save(array('affectsDependences' =>true));
    print_r( $u->getAllData() );
*/
/*
    $u->setter('name', 'olr')->setter('surname', 'OPPPRr');
    $u->save( array('affectsDependences'=>true) );
    print_r( $u->getAllData() );
*/

    //$u->fetch();
   // $u->setter('surname', 'Mamón');
//    $u->save();
   // echo $u->getter('name'). ' '.$u->getter('surname'). ' Con rol tipo:'. $u->getterDependence('id')[0]->getterDependence('role')->getter('name');


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

