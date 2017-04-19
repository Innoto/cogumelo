<?php
Cogumelo::load('coreView/View.php');

common::autoIncludes();
form::autoIncludes();
filedata::autoIncludes();
user::autoIncludes();


class UserView extends View
{

  public function __construct( $baseDir = false ){
    parent::__construct( $baseDir );
  }

  /**
   * Evaluate the access conditions and report if can continue
   *
   * @return bool : true -> Access allowed
   */
  public function accessCheck() {
    return true;
  }
  /**
  * Set User setUserSetup
  **/
  public function setUserSetup(){
    $useraccesscontrol = new UserAccessController();
    $user = $useraccesscontrol->getSessiondata();
    Cogumelo::setSetupValue( 'user:session', $user );
  }

  /**
   * Example login form
   **/
  public function loginForm() {
    $form = $this->loginFormDefine();

    $loginHtml = $this->loginFormGet( $form );

    $this->template->assign('loginHtml', $loginHtml);

    $this->template->setTpl('loginFormExample.tpl', 'user');
    $this->template->exec();
  } // function loadForm()


  /**
   * Example login form
   */
  public function loginFormBlock() {
    $template = new Template( $this->baseDir );

    $form = $this->loginFormDefine();
    $loginHtml = $this->loginFormGet( $form );

    $template->assign( 'loginHtml', $loginHtml );

    $template->setTpl( 'loginFormExample.tpl', 'user' );

    return $template;
  } // function loadFormBlock()


  /**
   * Create form fields and validations
   *
   * @return object
   */
  public function loginFormDefine() {

    $form = new FormController( 'loginForm', '/user/sendloginform' ); //actionform
    $form->setField( 'userLogin', array( 'placeholder' => __('Email') ));
    $form->setField( 'userPassword', array( 'type' => 'password', 'placeholder' => __('Password') ) );
    $form->setField( 'loginSubmit', array( 'type' => 'submit', 'value' => __('Sign in' ) ) );
    /************************************************************** VALIDATIONS */
    $form->setValidationRule( 'userLogin', 'required' );
    $form->setValidationRule( 'userPassword', 'required' );

    return $form;
  } // function loginFormDefine()



  /**
   * Returns necessary html form
   *
   * @param $form
   *
   * @return string
   **/
  public function loginFormGet( $form ) {

    $form->saveToSession();

    $this->template->assign("loginFormOpen", $form->getHtmpOpen());
    $this->template->assign("loginFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("loginFormClose", $form->getHtmlClose());
    $this->template->assign("loginFormValidations", $form->getScriptCode());

    $this->template->setTpl('loginForm.tpl', 'user');
    $loginHtml = $this->template->execToString();

    return $loginHtml;
  } // function loginFormGet()


  /**
   * Example of an external action login
   *
   * @return void
   **/
  public function sendLoginForm() {

    $form = $this->actionLoginForm();
    $form->sendJsonResponse();
  }

  /**
   * Assigns the forms validations
   *
   * @return $form
   **/
  public function actionLoginForm(){
    $form = new FormController();

    if( $form->loadPostInput() ) {
      $form->validateForm();
    }

    //Si tod0 esta OK!
    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();
      $userAccessControl = new UserAccessController();
      $res = $userAccessControl->userLogin($valuesArray['userLogin'], $valuesArray['userPassword']);

      if(!$res){
        $form->addFormError('El login y/o contraseña son erróneos');
      }
    }

    return $form;
  }


  /**
   * Update user form
   *
   * @param request(id)
   *
   * @return Form Html
   **/
  public function userUpdateFormDefine( $id ){

    $user = new UserModel();
    $dataVO = $user->listItems( array(
      'filters' => array('id' => $id ),
      'affectsDependences' => array( 'FiledataModel')
    ))->fetch();

    if(!$dataVO){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    $dataArray = $dataVO->getAllData('onlydata');

    $fileDep = $dataVO->getterDependence( 'avatar' );
    if( $fileDep !== false ) {
      foreach( $fileDep as $fileModel ) {
        $fileData = $fileModel->getAllData();
        $dataArray[ 'avatar' ] = $fileData[ 'data' ];
      }
    }

    $form = $this->userFormDefine( $dataArray );
    return $form;
  }



  /**
   * Create form fields and validations
   *
   * @return object
   **/
  public function userFormDefine( $data = '' ) {


    $form = new FormController( 'userForm', '/user/senduserform' ); //actionform
    $form->setSuccess( 'redirect', '/' );


    $fieldsInfo = array(
      'id' => array(
        'params' => array( 'type' => 'reserved', 'value' => null )
      ),
      'avatar' => array(
        'params' => array(
          'type' => 'file',
          'id' => 'inputFicheiro',
          'placeholder' => __('Upload a file'),
          'label' => __('Upload a file'),
          'destDir' => '/users'
        )
      ),
      'login' => array(
        'params' => array( 'type' => 'reserved', 'placeholder' => __('Login') ),
        'rules' => array( 'required' => true )
      ),
      'email' => array(
        'params' => array( 'label' => __( 'Email' ), 'id' => 'email', 'placeholder' => __('Email') ),
        'rules' => array( 'required' => true )
      ),
      'repeatEmail' => array(
        'params' => array( 'label' => __( 'Repeat email' ), 'id' => 'repeatEmail', 'placeholder' => __('Repeat email') ),
        'rules' => array( 'required' => true )
      ),
      'name' => array(
        'params' => array( 'label' => __( 'Name' ), 'placeholder' => __('Name') ),
      ),
      'surname' => array(
        'params' => array( 'label' => __( 'Surname' ), 'placeholder' => __('Surname') ),
      ),
      'active' => array(
        'params' => array( 'type' => 'checkbox', 'class' => 'switchery', 'options'=> array( '1' => __('Active') ))
      ),
      'description' => array(
        'params' => array( 'label' => __( 'Description' ), 'type' => 'textarea', 'placeholder' => __('Description')),
        'translate' => true
      )
    );

    $form->definitionsToForm( $fieldsInfo );

    //Esto es para verificar si es un create
    if(!isset($data) || $data == ''){
      $form->setField( 'password', array( 'id' => 'password', 'type' => 'password', 'placeholder' => __('New password') ) );
      $form->setField( 'password2', array( 'id' => 'password2', 'type' => 'password', 'placeholder' => __('Repeat password') ) );
    }

    $form->setField( 'submit', array( 'type' => 'submit', 'value' => 'Save' ) );

    //Esto es para verificar si es un create
    if(!isset($data) || $data == ''){
      $form->setValidationRule( 'password', 'required' );
      $form->setValidationRule( 'password2', 'required' );
      $form->setValidationRule( 'password', 'equalTo', '#password2' );
    }
    $form->setValidationRule( 'avatar', 'minfilesize', 1024 );
    $form->setValidationRule( 'avatar', 'accept', 'image/jpeg' );
    //$form->setValidationRule( 'avatar', 'required' );
    $form->setValidationRule( 'email', 'email' );
    $form->setValidationRule( 'email', 'equalTo', '#repeatEmail' );

    if(!isset($data) || $data !== ''){
      $data['repeatEmail'] = $data['email'];
      $form->loadArrayValues( $data );
    }

    return $form;
  }

  /**
   * Update user password form
   *
   * @param request(id)
   *
   * @return Form Html
   **/
  public function userChangePasswordFormDefine( $id ){

    $user = new UserModel();
    $dataVO = $user->listItems( array('filters' => array('id' => $id )))->fetch();


    if(!$dataVO){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    $form = new FormController( 'changePasswordForm', '/user/sendchangepasswordform' ); //actionform

    $form->setSuccess( 'accept', __('Password changed successfully.') );
    $form->setSuccess( 'redirect', '/' );

    $form->setField( 'id', array( 'type' => 'reserved', 'value' => $dataVO->getter('id') ));

    $form->setField( 'passwordOld', array( 'id' => 'passwordOld', 'type' => 'password', 'placeholder' => __('Old password') ) );
    $form->setField( 'password', array( 'id' => 'password', 'type' => 'password', 'placeholder' => __('New password') ) );
    $form->setField( 'password2', array( 'id' => 'password2', 'type' => 'password', 'placeholder' => __('Repeat password') ) );

    $form->setField( 'submit', array( 'type' => 'submit', 'value' => 'Save' ) );

    /******************************************************************************************** VALIDATIONS */

    //Esto es para verificar si es un create
    $form->setValidationRule( 'passwordOld', 'required' );
    $form->setValidationRule( 'password', 'required' );
    $form->setValidationRule( 'password2', 'required' );

     $form->setValidationRule( 'password2', 'equalTo', '#password' );

    return $form;
  }

  /**
   * Update user roles form
   *
   * @param request(id)
   *
   * @return Form Html
   **/
  public function userRolesFormDefine( $id ){

    $userModel = new UserModel();
    $user = $userModel->listItems( array('filters' => array('id' => $id )))->fetch();
    $roleModel = new RoleModel();
    $roles = $roleModel->listItems()->fetchAll();
    $userRoleModel = new UserRoleModel();
    $userRoles = $userRoleModel->listItems( array('filters' => array('user' => $id )))->fetchAll();

    $rolesCheck = array();
    foreach( $roles as $key => $rol ) {
      $rolesCheck[$rol->getter('id')] = $rol->getter('name');
    }

    $activeRolesCheck = array();
    foreach( $userRoles as $key => $rol ) {
      array_push( $activeRolesCheck, $rol->getter('role'));
    }

    $useraccesscontrol = new UserAccessController();
    $onlySA = $useraccesscontrol->checkPermissions('only:SA');
    if(!$onlySA){
      if(($key = array_search('superAdmin', $rolesCheck)) !== false) {
        unset($rolesCheck[$key]);
      }
    }

    if(!$user){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    $form = new FormController( 'userRoleForm', '/user/assignroleform' ); //actionform
    $form->setSuccess( 'redirect', '/' );
    $form->setField( 'user', array( 'type' => 'reserved', 'value' => $user->getter('id') ));
    $form->setField( 'checkroles', array( 'type' => 'select', 'class' => 'gzzSelect2',
      'label' => __('Select the roles for this user'), 'value' => $activeRolesCheck, 'multiple' => true,
      'options'=> $rolesCheck
    ));
    $form->setValidationRule( 'checkroles', 'required' );
    $form->setField( 'submit', array( 'type' => 'submit', 'value' => 'Save' ) );

    return $form;
  }

  /**
   * Returns necessary html form
   *
   * @param $form
   *
   * @return string
   **/
  public function userFormGet( $form ) {

    return $this->userFormGetBlock($form)->execToString();
  }

  /**
   * Returns Block form
   *
   * @param $form
   *
   * @return template
   **/
  public function userFormGetBlock( $form ) {
    $form->saveToSession();

    $template = new Template( $this->baseDir );

    $template->assign("userFormOpen", $form->getHtmpOpen());
    $template->assign("userFormFields", $form->getHtmlFieldsArray());
    $template->assign("userFormClose", $form->getHtmlClose());
    $template->assign("userFormValidations", $form->getScriptCode());

    $template->setTpl('userForm.tpl', 'user');

    return $template;
  }


  /**
   * Returns necessary html form
   *
   * @param $form
   *
   * @return string
   **/
  public function userChangePasswordFormGet( $form ) {
    return $this->userChangePasswordFormGetBlock($form)->execToString();
  }

  /**
   * Returns Block form
   *
   * @param $form
   *
   * @return template
   **/
  public function userChangePasswordFormGetBlock( $form ) {
    $form->saveToSession();

    $template = new Template( $this->baseDir );

    $template->assign("userChangePasswordFormOpen", $form->getHtmpOpen());
    $template->assign("userChangePasswordFormFields", $form->getHtmlFieldsArray());
    $template->assign("userChangePasswordFormClose", $form->getHtmlClose());
    $template->assign("userChangePasswordFormValidations", $form->getScriptCode());

    $template->setTpl('userChangePasswordForm.tpl', 'user');

    return $template;
  }


  /**
   * Returns necessary html form
   *
   * @param $form
   *
   * @return string
   **/
  public function userRolesFormGet( $form ) {
    return $this->userRolesFormGetBlock($form)->execToString();
  }

  /**
   * Returns Block form
   *
   * @param $form
   *
   * @return template
   **/
  public function userRolesFormGetBlock( $form ) {
    $form->saveToSession();

    $template = new Template( $this->baseDir );

    $template->assign("userRolesFormOpen", $form->getHtmpOpen());
    $template->assign("userRolesFormFields", $form->getHtmlFieldsArray());
    $template->assign("userRolesFormClose", $form->getHtmlClose());
    $template->assign("userRolesFormValidations", $form->getScriptCode());

    $template->setTpl('userRolesForm.tpl', 'user');

    return $template;
  }


  /**
   * Example of an external action register
   *
   * @return void
   **/
  public function sendUserForm() {
    $form = $this->actionUserForm();
    $this->registerOk($form);
    $form->sendJsonResponse();
  }


  /**
   * Assigns the forms validations
   *
   * @return $form
   **/
  public function actionUserForm() {
    $form = new FormController();

    if( $form->loadPostInput() ) {
      $form->validateForm();
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      //Validaciones extra
      $userControl = new UserModel();
      // Donde diferenciamos si es un update o un create para validar el login
      $loginExist = $userControl->listItems( array('filters' => array('email' => $form->getFieldValue('email'))) )->fetch();

      if( isset($valuesArray['id']) && $valuesArray['id'] ){
        $user = $userControl->listItems( array('filters' => array('id' => $valuesArray['id'])) )->fetch();
        if($valuesArray['email'] !== $user->getter('email')){
          if($loginExist){
            $form->addFieldRuleError('email', 'cogumelo', __('The email specified field already in use'));
          }
        }
      }
      else{
        // Create: comprobamos si el login existe y si existe mostramos error.
        if($loginExist){
          $form->addFieldRuleError('email', 'cogumelo', __('The email specified field already in use'));
        }
      }
    }

    return $form;
  }


  /**
   * Edit/Create User
   *
   * @return $user
   **/
  public function userFormOk( $form ) {
    //Si tod0 esta OK!
    $asignRole = false;

    if( !$form->processFileFields() ) {
      $form->addFormError( __('It has happened a problem with attachments. It may be necessary to raise them again.'), 'formError' );
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      $password = false;

      if( array_key_exists('password', $valuesArray)){
        $password = $valuesArray['password'];
        unset($valuesArray['password']);
        unset($valuesArray['password2']);
      }
       // Donde diferenciamos si es un update o un create
      if( !isset($valuesArray['id']) || !$valuesArray['id'] ){
        $valuesArray['timeCreateUser'] = date("Y-m-d H:i:s", time());
        $asignRole = true;
      }
      $valuesArray['login'] = $valuesArray['email'];
      if( array_key_exists( 'avatar', $valuesArray) ){
        $userAvatar = $valuesArray['avatar'];
        unset($valuesArray['avatar']);
      }
      $user = new UserModel( $valuesArray );

      if(isset($password) && $password){
        $user->setPassword( $password );
      }

      $user->save();

      //var_dump( $user->getAllData() );

      if( isset($userAvatar) && $userAvatar ) {
        //var_dump( $userAvatar );
        /*
          if( $userAvatar['status'] === "DELETE"){
            //IMG DELETE
            //var_dump('delete');
            $user->deleteDependence( 'avatar', true );
          }
          elseif( $userAvatar['status'] === "REPLACE"){
            //IMG UPDATE
            //var_dump('replace');
            $user->deleteDependence( 'avatar', true);
            $user->setterDependence( 'avatar', new FiledataModel( $userAvatar['values'] ) );
          }else{
            //var_dump('else');
            //IMG CREATE
            $user->setterDependence( 'avatar', new FiledataModel( $userAvatar['values'] ) );
          }
        */


        $filedataCtrl = new FiledataController();
        $newFiledataObj = false;

        switch( $userAvatar['status'] ) {
          case 'LOADED':
            $userAvatarValues = $userAvatar['values'];
            $newFiledataObj = $filedataCtrl->createNewFile( $userAvatarValues );
            // error_log( 'To Model - newFiledataObj ID: '.$newFiledataObj->getter( 'id' ) );
            if( $newFiledataObj ) {
              // $user->setterDependence( 'avatar', $newFiledataObj );
              $user->setter( 'avatar', $newFiledataObj->getter( 'id' ) );
            }
            break;
          case 'REPLACE':
            // error_log( 'To Model - fileInfoPrev: '. print_r( $userAvatar[ 'prev' ], true ) );
            $userAvatarValues = $userAvatar['values'];
            $prevFiledataId = $user->getter( 'avatar' );
            $newFiledataObj = $filedataCtrl->createNewFile( $userAvatarValues );
            // error_log( 'To Model - newFiledataObj ID: '.$newFiledataObj->getter( 'id' ) );
            if( $newFiledataObj ) {
              // error_log( 'To Model - deleteFile ID: '.$prevFiledataId );
              // $user->deleteDependence( 'avatar', true );
              // $user->setterDependence( 'avatar', $newFiledataObj );
              $user->setter( 'avatar', $newFiledataObj->getter( 'id' ) );
              $filedataCtrl->deleteFile( $prevFiledataId );
            }
            break;
          case 'DELETE':
            if( $prevFiledataId = $user->getter( 'avatar' ) ) {
              // error_log( 'To Model - prevFiledataId: '.$prevFiledataId );
              $filedataCtrl->deleteFile( $prevFiledataId );
              // $user->deleteDependence( 'avatar', true );
              $user->setter( 'avatar', null );
            }
            break;
          case 'EXIST':
            $userAvatarValues = $userAvatar[ 'values' ];
            if( $prevFiledataId = $user->getter( 'avatar' ) ) {
              // error_log( 'To Model - UPDATE prevFiledataId: '.$prevFiledataId );
              $filedataCtrl->updateInfo( $prevFiledataId, $userAvatarValues );
            }
            break;
          default:
            // error_log( 'To Model: DEFAULT='.$userAvatar['status'] );
            break;
        } // switch( $userAvatar['status'] )



      } // if( $userAvatar )

      //echo "==========";
      //var_dump( $user->getAllData()  );

      $user->save();
      // $user->save( array( 'affectsDependences' => true ) );

      /*Asignacion de ROLE user*/
      if($asignRole){
        $roleModel = new RoleModel();
        $role = $roleModel->listItems( array('filters' => array('name' => 'user') ))->fetch();
        $userRole = new UserRoleModel();
        if( $role ){
          $userRole->setterDependence( 'role', $role );
        }
        $userRole->setterDependence( 'user', $user );
        $userRole->save(array( 'affectsDependences' => true ));
      }

    }
    return $user;
  }


  /**
   * Assigns the forms validations
   *
   * @return $form
   **/
  public function actionChangeUserPasswordForm() {
    $form = new FormController();

    if( $form->loadPostInput() ) {
      $form->validateForm();
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();
      //Validaciones extra
      $userControl = new UserModel();
      // Donde diferenciamos si es un update o un create para validar el login
      $user = $userControl->listItems( array('filters' => array('id' => $valuesArray['id'])) )->fetch();


      if( !isset($valuesArray['id']) && !$user ){
        $form->addFieldRuleError('id', 'cogumelo', __('Unknow user'));
      }
      elseif( $form->getFieldParam('passwordOld', 'type') !=='reserved' && !$user->equalPassword($valuesArray['passwordOld']) ){
        $form->addFieldRuleError('passwordOld', 'cogumelo', __('The old password does not match.'));
      }
    }

    return $form;
  }


  /**
   * Change Password
   *
   * @return $user
   **/
  public function changeUserPasswordFormOk( $form ) {
    //Si tod0 esta OK!
    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      $password = $valuesArray['password'];
      unset($valuesArray['password']);
      unset($valuesArray['password2']);

      $user = new UserModel( $valuesArray );
      $user->setPassword( $password );
      $user->save();
    }

    return $user;
  }


  /**
   * Assigns the forms validations
   *
   * @return $form
   **/
  public function actionUserRolesForm() {
    $form = new FormController();
    $valuesArray = false;

    if( $form->loadPostInput() ) {
      $form->validateForm();
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      if( !isset($valuesArray['user']) ) {
        $form->addFieldRuleError('id', 'cogumelo', __('User Error unidentified'));
      }
    }

    if( !$form->existErrors() ) {
      $useraccesscontrol = new UserAccessController();
      $isSA = $useraccesscontrol->checkPermissions('only:SA');

      $roleModel = new RoleModel();
      $roleList = $roleModel->listItems( array('filters' => array( 'name' => 'superAdmin' ) ) );
      $roleSaObj = ( gettype( $roleList ) === 'object' ) ? $roleList->fetch() : false;
      $roleSaId = ( gettype( $roleSaObj ) === 'object' ) ? $roleSaObj->getter('id') : false;

      if( !$isSA && $roleSaId !== false && in_array( $roleSaId, $valuesArray['checkroles'] ) ) {
        $form->addFieldRuleError('id', 'cogumelo', __(' Error unknown role'));
      }
    }

    return $form;
  }

  /**
   * Save UserRoles
   *
   * @return $array userRoles
   **/
  public function userRolesFormOk( $form ) {
    //Si tod0 esta OK!
    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      $userRoleModel = new UserRoleModel();
      $userRoles = $userRoleModel->listItems( array('filters' => array( 'user' => $valuesArray['user'])) );
      if( $userRoles ){
        while( $userRole = $userRoles->fetch() ){
          $userRole->delete();
        }
      }

      if( is_array($valuesArray['checkroles']) && count($valuesArray['checkroles']) > 0) {
        foreach( $valuesArray['checkroles'] as $key => $checkrol ) {
          # code...
          $userRoleModel = new UserRoleModel( array( 'role' => $checkrol, 'user' => $valuesArray['user'] ) );
          $userRoleModel->save();
        }
      }else{
        $userRoleModel = new UserRoleModel( array( 'role' => $valuesArray['checkroles'], 'user' => $valuesArray['user'] ) );
        $userRoleModel->save();
      }

    }

    return $userRoleModel->listItems( array('filters' => array( 'user' => $valuesArray['user'])))->fetchAll();
  }


}
