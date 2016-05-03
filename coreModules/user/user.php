<?php

Cogumelo::load("coreController/Module.php");
require_once APP_BASE_PATH.'/conf/inc/geozzyRolesPermissions.php';

define('MOD_USER_URL_DIR', 'user');

class user extends Module
{
  public $name = "user";
  public $version = 1.5;
  public $dependences = array(

  );

  public $includesCommon = array(
    /*'controller/UserController.php',*/
    'controller/UserAccessController.php',
    'view/UserView.php',
    'view/RoleView.php',
    'model/UserModel.php',
    'model/RoleModel.php'

  );

  function __construct() {
    $this->addUrlPatterns( '#^'.MOD_USER_URL_DIR.'/loginform$#', 'view:UserView::loginForm' );
    $this->addUrlPatterns( '#^'.MOD_USER_URL_DIR.'/sendloginform$#', 'view:UserView::sendLoginForm' );
    $this->addUrlPatterns( '#^'.MOD_USER_URL_DIR.'/registerform$#', 'view:UserView::userForm' );
    $this->addUrlPatterns( '#^'.MOD_USER_URL_DIR.'/senduserform$#', 'view:UserView::sendUserForm' );
    //$this->addUrlPatterns( '#^()(.*)$#', 'noendview:UserView::setUserSetup' );
  }

  public function moduleRc() {
    user::load('model/RoleModel.php');
    user::load('model/RolePermissionModel.php');

    global $CGMLCONF;
    /**
    Create roles & permissions
    */
    $roleData = array(
      'name' => 'superAdmin',
      'description' => 'SuperAdmin'
    );
    $roleSprAdmin = new RoleModel($roleData);
    $roleSprAdmin->save();

    $permArray = array();
    $permArray['role'] = $roleSprAdmin->getter('id');
    $permArray['permission'] = 'user:superAdmin';

    $rolePermissionModel = new RolePermissionModel( $permArray );
    $rolePermissionModel->save();

    $roleData = array(
      'name' => 'user',
      'description' => 'User'
    );
    $role = new RoleModel($roleData);
    $role->save();

    if( count( $CGMLCONF['user']['roles'] ) > 0 ) {
      foreach( $CGMLCONF['user']['roles'] as $rol ) {

        $roleModel = new RoleModel( $rol );
        $roleModel->save();

        if( isset($rol['permissions']) && count( $rol['permissions']) > 0 ) {
          foreach( $rol['permissions'] as $perm ) {
            $permArray = array();
            $permArray['role'] = $roleModel->getter('id');
            $permArray['permission'] = $perm;

            $rolePermissionModel = new RolePermissionModel( $permArray );
            $rolePermissionModel->save();
          }
        }
      }
    }
  }
}
