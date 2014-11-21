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



  function loginForm() {

    $form = new FormController( 'loginForm', '/user/sendloginform' ); //actionform
    $form->setField( 'userLogin', array( 'placeholder' => 'Login' ));
    $form->setField( 'userPassword', array( 'type' => 'password', 'placeholder' => 'Contraseña') );
    $form->setField( 'loginSubmit', array( 'type' => 'submit', 'value' => 'Entrar' ) );

    /************************************************************** VALIDATIONS */
    $form->setValidationRule( 'userLogin', 'required' );
    $form->setValidationRule( 'userPassword', 'required' );

    $form->saveToSession();

    $this->template->assign("loginFormOpen", $form->getHtmpOpen());
    $this->template->assign("loginFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("loginFormClose", $form->getHtmlClose());
    $this->template->assign("loginFormValidations", $form->getJqueryValidationJS());

    $this->template->setTpl('loginForm.tpl', 'user');
    $this->template->exec();

  } // function loadForm()


  function sendLoginForm() {

    $formError = false;
    $postData = null;

    $postDataJson = file_get_contents('php://input');
    //error_log( $postDataJson );
    if( $postDataJson !== false && strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
    }
    //error_log( print_r( $postData, true ) );
    // Creamos un objeto recuperandolo de session y añadiendo los datos POST
    $form = new FormController();

    // Leemos el input del navegador y recuperamos FORM de sesion añadiendole los datos enviados
    if( $form->loadPostInput() ) {
      // Creamos un objeto con los validadores y lo asociamos
      $form->setValidationObj( new FormValidators() );

      // $form->setValidationRule( 'input2', 'maxlength', '10' ); // CAMBIANDO AS REGLAS
      $form->validateForm();

      //$form->addFieldRuleError( 'check1', 'cogumelo', 'Un mensaxe de error de campo' );
      //$form->addFormError( 'Ola meu... ERROR porque SI ;-)' );
    }
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recividos.', 'formError' );
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

    if( $form->existErrors() ) {
      echo $form->jsonFormError();
    }
    else {
      echo $form->jsonFormOk();
    }

  }

  //END Login

  function registerForm() {

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

    $form->saveToSession();

    $this->template->assign("registerFormOpen", $form->getHtmpOpen());
    $this->template->assign("registerFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("registerFormClose", $form->getHtmlClose());
    $this->template->assign("registerFormValidations", $form->getJqueryValidationJS());

    $this->template->setTpl('registerForm.tpl', 'user');
    $this->template->exec();

  } // function loadForm()


  function sendRegisterForm() {

    $formError = false;
    $postData = null;

    $postDataJson = file_get_contents('php://input');
    //error_log( $postDataJson );
    if( $postDataJson !== false && strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
    }
    //error_log( print_r( $postData, true ) );

    // Creamos un objeto recuperandolo de session y añadiendo los datos POST
    $form = new FormController();
    // Leemos el input del navegador y recuperamos FORM de sesion añadiendole los datos enviados
    if( $form->loadPostInput() ) {
      // Creamos un objeto con los validadores y lo asociamos
      $form->setValidationObj( new FormValidators() );

      // $form->setValidationRule( 'input2', 'maxlength', '10' ); // CAMBIANDO AS REGLAS
      $form->validateForm();

      //$form->addFieldRuleError( 'check1', 'cogumelo', 'Un mensaxe de error de campo' );
      //$form->addFormError( 'Ola meu... ERROR porque SI ;-)' );
    }
    else {
      $form->addFormError( 'El servidor no considera válidos los datos recividos.', 'formError' );
    }

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

      //$res = $userControl->createFromArray($valuesArray);
      $res = $userControl->createRelTmp($valuesArray);
    }

    if( $form->existErrors() ) {
      echo $form->jsonFormError();
    }
    else {
      echo $form->jsonFormOk();
    }


  }

}

