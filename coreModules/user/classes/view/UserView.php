<?php
Cogumelo::load('coreView/View.php');

common::autoIncludes();
form::autoIncludes();
user::autoIncludes();


class UserView extends View
{


  function __construct($base_dir = false){
    parent::__construct($base_dir);
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  /**
   *
   * Example login form
   * @return void
   *
   **/

  function loginForm() {

    $form = $this->loginFormDefine();
    $loginHtml = $this->loginFormGet( $form );

    $this->template->assign('loginHtml', $loginHtml);

    $this->template->setTpl('loginFormExample.tpl', 'user');
    $this->template->exec();

  } // function loadForm()



  /**
   *
   * Create form fields and validations
   * @return object
   *
   **/

  function loginFormDefine() {

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
   *
   * Returns necessary html form
   * @param $form
   * @return string
   *
   **/

  function loginFormGet( $form ) {

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
   *
   * Example of an external action login
   *
   * @return void
   *
   **/

  function sendLoginForm() {

    $form = $this->actionLoginForm();

    if( $form->existErrors() ) {
      echo $form->jsonFormError();
    }
    else {
      echo $form->jsonFormOk();
    }

  }

  /**
   *
   * Assigns the forms validations
   * @return $form
   *
   **/

  function actionLoginForm(){
    $form = new FormController();

    if( $form->loadPostInput() ) {
      $form->validateForm();
    }
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recibidos.', 'formError' );
    }

    //Si todo esta OK!
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
   *
   * Example register form
   * @return void
   *
   **/
  function registerForm() {
    $form = $this->registerFormDefine();
    $registerHtml = $this->registerFormGet( $form );

    $this->template->assign('registerHtml', $registerHtml);

    $this->template->setTpl('registerFormExample.tpl', 'user');
    $this->template->exec();


  } // function loadForm()





  /**
  *
  * Update user form
  * @param request(id)
  * @return Form Html
  *
  **/
  function userUpdateFormDefine( $request ){

    $user = new UserModel();
    $dataVO = $user->listItems( array('filters' => array('id' => $request[1] )))->fetch();

    if(!$dataVO){
      Cogumelo::redirect( SITE_URL.'404' );
    }

    $form = $this->userFormDefine( $dataVO );
    return $form;
  }



  /**
   *
   * Create form fields and validations
   * @return object
   *
   **/

  function userFormDefine( $dataVO = '' ) {

    $form = new FormController( 'userForm', '/user/senduserform' ); //actionform

    $form->setSuccess( 'accept', 'Bienvenido' );
    $form->setSuccess( 'redirect', '/' );

    $form->setField( 'id', array( 'type' => 'reserved', 'value' => null ) );

    $form->setField( 'login', array( 'placeholder' => 'Login' ) );
    //Esto es para verificar si es un create
    if(!isset($dataVO) || $dataVO == ''){
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
    if(!isset($dataVO) || $dataVO == ''){
      $form->setValidationRule( 'password', 'required' );
      $form->setValidationRule( 'password2', 'required' );
      $form->setValidationRule( 'password', 'equalTo', '#password2' );
    }
    $form->setValidationRule( 'avatar', 'minfilesize', 1024 );
    $form->setValidationRule( 'avatar', 'accept', 'image/png' );
    //$form->setValidationRule( 'avatar', 'required' );


    $form->setValidationRule( 'email', 'email' );

    $form->loadVOValues( $dataVO );

    return $form;
  }

  /**
  *
  * Update user password form
  * @param request(id)
  * @return Form Html
  *
  **/
  function userChangePasswordFormDefine( $request ){

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
   *
   * Returns necessary html form
   * @param $form
   * @return string
   *
   **/
  function userFormGet($form) {
    $form->saveToSession();

    $this->template->assign("userFormOpen", $form->getHtmpOpen());
    $this->template->assign("userFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("userFormClose", $form->getHtmlClose());
    $this->template->assign("userFormValidations", $form->getScriptCode());

    $this->template->setTpl('userForm.tpl', 'user');

    return $this->template->execToString();
  }
  /**
   *
   * Returns necessary html form
   * @param $form
   * @return string
   *
   **/
  function userChangePasswordFormGet($form) {
    $form->saveToSession();

    $this->template->assign("userChangePasswordFormOpen", $form->getHtmpOpen());
    $this->template->assign("userChangePasswordFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("userChangePasswordFormClose", $form->getHtmlClose());
    $this->template->assign("userChangePasswordFormValidations", $form->getScriptCode());

    $this->template->setTpl('userChangePasswordForm.tpl', 'user');

    return $this->template->execToString();
  }

  /**
   *
   * Example of an external action register
   *
   * @return void
   *
   **/
  function sendUserForm() {

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
   *
   * Assigns the forms validations
   * @return $form
   *
   **/
  function actionUserForm() {
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

      }else{
        // Create: comprobamos si el login existe y si existe mostramos error.
        if($loginExist){
          $form->addFieldRuleError('login', 'cogumelo', 'El campo login específicado ya esta en uso.');
        }
      }
    }

    return $form;
  }


  function userFormOk( $form ) {
    //Si todo esta OK!

    if( !$form->processFileFields() ) {
      $form->addFormError( 'Ha sucedido un problema con los ficheros adjuntos. Puede que sea necesario subirlos otra vez.', 'formError' );
    }

    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();
      $valuesArray['status'] = USER_STATUS_WAITING;
      $valuesArray['role'] = ROLE_USER;

       // Donde diferenciamos si es un update o un create
      if( !isset($valuesArray['id']) || !$valuesArray['id'] ){
        $valuesArray['password'] = sha1($valuesArray['password']);
        unset($valuesArray['password2']);
        $valuesArray['timeCreateUser'] = date("Y-m-d H:i:s", time());
      }

      $user = new UserModel( $valuesArray );

var_dump($valuesArray);

      $user->save();
    }
    return $user;
  }




  /**
   *
   * Assigns the forms validations
   * @return $form
   *
   **/
  function actionChangeUserPasswordForm() {
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
      elseif( sha1($valuesArray['passwordOld']) !==  $user->getter('password') ){
        $form->addFieldRuleError('passwordOld', 'cogumelo', 'La contraseña antigua no coincide.');
      }
    }

    return $form;
  }


  function changeUserPasswordFormOk( $form ) {
    //Si todo esta OK!


    if( !$form->existErrors() ){
      $valuesArray = $form->getValuesArray();

      $valuesArray['password'] = sha1($valuesArray['password']);
      unset($valuesArray['password2']);

      $user = new UserModel( $valuesArray );
      $user->save();
    }
    return $user;
  }


}

