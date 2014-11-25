<?php
Cogumelo::load('coreView/View.php');

common::autoIncludes();
form::autoIncludes();
user::autoIncludes();


class UserView extends View
{

  function __construct($base_dir){
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
   * Create form fields and validations
   * @return object
   *
   **/

  function registerFormDefine() {

    $form = new FormController( 'registerForm', '/user/sendregisterform' ); //actionform

    $form->setSuccess( 'accept', 'Bienvenido' );
    $form->setSuccess( 'redirect', '/' );


    $form->setField( 'login', array( 'placeholder' => 'Login' ) );
    $form->setField( 'password', array( 'id' => 'password', 'type' => 'password', 'placeholder' => 'Contraseña' ) );
    $form->setField( 'password2', array( 'id' => 'password2', 'type' => 'password', 'placeholder' => 'Repite contraseña' ) );
    $form->setField( 'name', array( 'placeholder' => 'Nombre' ) );
    $form->setField( 'surname', array( 'placeholder' => 'Apellidos' ) );
    $form->setField( 'email', array( 'placeholder' => 'Email' ) );
    $form->setField( 'role', array( 'type' => 'reserved', 'value' => ROLE_USER ));

    $form->setField( 'description', array( 'type' => 'textarea', 'placeholder' => 'Descripción' ) );
    $form->setField( 'avatar', array( 'type' => 'file', 'id' => 'inputFicheiro',
      'placeholder' => 'Escolle un ficheiro', 'label' => 'Colle un ficheiro',
      'destDir' => '/users' ) );

    $form->setField( 'submit', array( 'type' => 'submit', 'value' => 'Registrar' ) );

    /******************************************************************************************** VALIDATIONS */
    $form->setValidationRule( 'login', 'required' );
    $form->setValidationRule( 'email', 'required' );
    $form->setValidationRule( 'password', 'required' );
    $form->setValidationRule( 'password2', 'required' );

    $form->setValidationRule( 'avatar', 'minfilesize', 1024 );
    $form->setValidationRule( 'avatar', 'accept', 'image/png' );
    $form->setValidationRule( 'avatar', 'required' );

    $form->setValidationRule( 'password', 'equalTo', '#password2' );
    $form->setValidationRule( 'email', 'email' );

    return $form;
  }

   /**
   *
   * Returns necessary html form
   * @param $form
   * @return string
   *
   **/
  function registerFormGet($form) {
    $form->saveToSession();

    $this->template->assign("registerFormOpen", $form->getHtmpOpen());
    $this->template->assign("registerFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("registerFormClose", $form->getHtmlClose());
    $this->template->assign("registerFormValidations", $form->getScriptCode());

    $this->template->setTpl('registerForm.tpl', 'user');

    return $this->template->execToString();
  }

  /**
   *
   * Example of an external action register
   *
   * @return void
   *
   **/
  function sendRegisterForm() {

    $form = $this->actionRegisterForm();
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
  function actionRegisterForm() {
    $form = new FormController();
    if( $form->loadPostInput() ) {
      $form->validateForm();
    }
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recibidos.', 'formError' );
    }
    return $form;
  }


  function registerOk( $form ) {
    //Si todo esta OK!
    if( !$form->existErrors() ){

      //Validaciones
      $valuesArray = $form->getValuesArray();

      $userControl = new UserController();
      $loginExist = $userControl->find($valuesArray['login'], 'login');

      if($loginExist){
        $form->addFieldRuleError('login', 'cogumelo', 'El campo login específicado ya esta en uso.');
      }

      $valuesArray['password'] = sha1($valuesArray['password']);
      unset($valuesArray['password2']);
      $valuesArray['timeCreateUser'] = date("Y-m-d H:i:s", time());

      Cogumelo::console($valuesArray);
      //$res = $userControl->createFromArray($valuesArray);
      $res = $userControl->createRelTmp($valuesArray);
    }

  }

}

