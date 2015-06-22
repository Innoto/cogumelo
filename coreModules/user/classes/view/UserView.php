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
  * @return bool : true -> Access allowed
  */
  public function accessCheck() {
    return true;
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
   **/
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
   **/

  public function loginFormDefine() {

    $form = new FormController( 'loginForm', '/user/sendloginform' ); //actionform
    $form->setField( 'userLogin', array( 'placeholder' => 'Login' ));
    $form->setField( 'userPassword', array( 'type' => 'password', 'placeholder' => 'Contraseña') );
    $form->setField( 'loginSubmit', array( 'type' => 'submit', 'value' => 'Entrar' ) );
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

    if( $form->existErrors() ) {
      echo $form->jsonFormError();
    }
    else {
      echo $form->jsonFormOk();
    }
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
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recibidos.', 'formError' );
    }

    //Si tod0 esta OK!
    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();
      $userAccessControl = new UserAccessController();
      $res = $userAccessControl->userLogin($valuesArray['userLogin'], $valuesArray['userPassword']);

      if(!$res){
        $form->addFieldRuleError('userLogin', 'cogumelo', '');
        $form->addFieldRuleError('userPassword', 'cogumelo', '');
        $form->addFormError('El login y/o contraseña son erróneos');
      }
    }

    return $form;
  }









  /**
   * Example register form
   *
   * @return void
   **/
  public function registerForm() {
    $form = $this->registerFormDefine();
    $registerHtml = $this->registerFormGet( $form );

    $this->template->assign('registerHtml', $registerHtml);

    $this->template->setTpl('registerFormExample.tpl', 'user');
    $this->template->exec();
  } // function loadForm()





  /**
   * Update user form
   *
   * @param request(id)
   *
   * @return Form Html
   **/
  public function userUpdateFormDefine( $request ){

    $user = new UserModel();
    $dataVO = $user->listItems( array(
      'filters' => array('id' => $request[1] ),
      'affectsDependences' => array( 'FiledataModel')
    ))->fetch();
    if(!$dataVO){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    foreach( $dataVO->getKeys() as $keyVO ) {
      $dataArray[ $keyVO ] = $dataVO->getter( $keyVO );
    }
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

    $form->setSuccess( 'accept', 'Bienvenido' );
    $form->setSuccess( 'redirect', '/' );

    $form->setField( 'id', array( 'type' => 'reserved', 'value' => null ) );

    $form->setField( 'login', array( 'placeholder' => 'Login' ) );
    //Esto es para verificar si es un create
    if(!isset($data) || $data == ''){
      $form->setField( 'password', array( 'id' => 'password', 'type' => 'password', 'placeholder' => 'Contraseña' ) );
      $form->setField( 'password2', array( 'id' => 'password2', 'type' => 'password', 'placeholder' => 'Repite contraseña' ) );
    }

    $form->setField( 'name', array( 'placeholder' => 'Nombre' ) );
    $form->setField( 'surname', array( 'placeholder' => 'Apellidos' ) );
    $form->setField( 'email', array( 'placeholder' => 'Email' ) );

    $form->setField( 'description', array( 'type' => 'textarea', 'placeholder' => 'Descripción' ) );
    $form->setField( 'avatar', array( 'type' => 'file', 'id' => 'inputFicheiro',
      'placeholder' => 'Escolle un ficheiro', 'label' => 'Colle un ficheiro',
      'destDir' => '/users' ) );

    $form->setField( 'submit', array( 'type' => 'submit', 'value' => 'Save' ) );

    /******************************************************************************************** VALIDATIONS */
    $form->setValidationRule( 'login', 'required' );
    $form->setValidationRule( 'email', 'required' );

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


    $form->loadArrayValues( $data );

    return $form;
  }

  /**
   * Update user password form
   *
   * @param request(id)
   *
   * @return Form Html
   **/
  public function userChangePasswordFormDefine( $request ){

    $user = new UserModel();
    $dataVO = $user->listItems( array('filters' => array('id' => $request[1] )))->fetch();


    if(!$dataVO){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    $form = new FormController( 'changePasswordForm', '/user/sendchangepasswordform' ); //actionform

    $form->setSuccess( 'accept', 'Contraseña cambiada con exito.' );
    $form->setSuccess( 'redirect', '/' );

    $form->setField( 'id', array( 'type' => 'reserved', 'value' => $dataVO->getter('id') ));

    $form->setField( 'passwordOld', array( 'id' => 'passwordOld', 'type' => 'password', 'placeholder' => 'Contraseña antigua' ) );
    $form->setField( 'password', array( 'id' => 'password', 'type' => 'password', 'placeholder' => 'Contraseña nueva' ) );
    $form->setField( 'password2', array( 'id' => 'password2', 'type' => 'password', 'placeholder' => 'Repite contraseña' ) );

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
  public function userRolesFormDefine( $request ){

    $userModel = new UserModel();
    $user = $userModel->listItems( array('filters' => array('id' => $request[1] )))->fetch();
    $roleModel = new RoleModel();
    $roles = $roleModel->listItems()->fetchAll();
    $userRoleModel = new UserRoleModel();
    $userRoles = $userRoleModel->listItems( array('filters' => array('user' => $request[1] )))->fetchAll();

    $rolesCheck = array();
    foreach( $roles as $key => $rol ) {
      $rolesCheck[$rol->getter('id')] = $rol->getter('name');
    }

    $activeRolesCheck = array();
    foreach( $userRoles as $key => $rol ) {
      array_push( $activeRolesCheck, $rol->getter('role'));
    }

    if(!$user){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    $form = new FormController( 'userRoleForm', '/user/assignroleform' ); //actionform
    $form->setSuccess( 'redirect', '/' );
    $form->setField( 'user', array( 'type' => 'reserved', 'value' => $user->getter('id') ));
    $form->setField( 'checkroles', array( 'type' => 'checkbox', 'label' => 'Selecciona los roles para este usuario', 'value' => $activeRolesCheck,
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
    $form->saveToSession();

    $this->template->assign("userFormOpen", $form->getHtmpOpen());
    $this->template->assign("userFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("userFormClose", $form->getHtmlClose());
    $this->template->assign("userFormValidations", $form->getScriptCode());

    $this->template->setTpl('userForm.tpl', 'user');

    return $this->template->execToString();
  }


  /**
   * Returns necessary html form
   *
   * @param $form
   *
   * @return string
   **/
  public function userChangePasswordFormGet( $form ) {
    $form->saveToSession();

    $this->template->assign("userChangePasswordFormOpen", $form->getHtmpOpen());
    $this->template->assign("userChangePasswordFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("userChangePasswordFormClose", $form->getHtmlClose());
    $this->template->assign("userChangePasswordFormValidations", $form->getScriptCode());

    $this->template->setTpl('userChangePasswordForm.tpl', 'user');

    return $this->template->execToString();
  }


  /**
   * Returns necessary html form
   *
   * @param $form
   *
   * @return string
   **/
  public function userRolesFormGet( $form ) {
    $form->saveToSession();

    $this->template->assign("userRolesFormOpen", $form->getHtmpOpen());
    $this->template->assign("userRolesFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("userRolesFormClose", $form->getHtmlClose());
    $this->template->assign("userRolesFormValidations", $form->getScriptCode());

    $this->template->setTpl('userRolesForm.tpl', 'user');

    return $this->template->execToString();
  }


  /**
   * Example of an external action register
   *
   * @return void
   **/
  public function sendUserForm() {
    $form = $this->actionUserForm();
    $this->registerOk($form);

    if( $form->existErrors() ) {
      echo $form->jsonFormError();
    }
    else {
      echo $form->jsonFormOk();
    }
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
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recibidos.', 'formError' );
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();
      //Validaciones extra
      $userControl = new UserModel();
      // Donde diferenciamos si es un update o un create para validar el login
      $loginExist = $userControl->listItems( array('filters' => array('login' => $form->getFieldValue('login'))) )->fetch();

      if( isset($valuesArray['id']) && $valuesArray['id'] ){
        $user = $userControl->listItems( array('filters' => array('id' => $valuesArray['id'])) )->fetch();
        if($valuesArray['login'] !== $user->getter('login')){
          if($loginExist){
            $form->addFieldRuleError('login', 'cogumelo', 'El campo login específicado ya esta en uso.');
          }
        }
      }
      else{
        // Create: comprobamos si el login existe y si existe mostramos error.
        if($loginExist){
          $form->addFieldRuleError('login', 'cogumelo', 'El campo login específicado ya esta en uso.');
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
      $form->addFormError( 'Ha sucedido un problema con los ficheros adjuntos. Puede que sea necesario subirlos otra vez.', 'formError' );
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();
      $valuesArray['active'] = 0;

       // Donde diferenciamos si es un update o un create
      if( !isset($valuesArray['id']) || !$valuesArray['id'] ){
        $password = $valuesArray['password'];
        unset($valuesArray['password']);
        unset($valuesArray['password2']);
        $valuesArray['timeCreateUser'] = date("Y-m-d H:i:s", time());
        $asignRole = true;
      }

      $user = new UserModel( $valuesArray );

      if(isset($password)){
        $user->setPassword( $password );
      }

Cogumelo::console($valuesArray['avatar']['values']);
exit();
      if($valuesArray['avatar']['values']){
        $user->setterDependence( 'avatar', new FiledataModel( $valuesArray['avatar']['values'] ) );
      }




      $user->save( array( 'affectsDependences' => true ));

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
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recibidos.', 'formError' );
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();
      //Validaciones extra
      $userControl = new UserModel();
      // Donde diferenciamos si es un update o un create para validar el login
      $user = $userControl->listItems( array('filters' => array('id' => $valuesArray['id'])) )->fetch();


      if( !isset($valuesArray['id']) && !$user ){
        $form->addFieldRuleError('id', 'cogumelo', 'Error usuario no identificado.');
      }
      elseif( !$user->equalPassword($valuesArray['passwordOld']) ){
        $form->addFieldRuleError('passwordOld', 'cogumelo', 'La contraseña antigua no coincide.');
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
    if( $form->loadPostInput() ) {
      $form->validateForm();
    }
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recibidos.', 'formError' );
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      if( !isset($valuesArray['user'])){
        $form->addFieldRuleError('id', 'cogumelo', 'Error usuario no identificado.');
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
