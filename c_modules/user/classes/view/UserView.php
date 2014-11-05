<?php
Cogumelo::load('c_view/View.php');
user::load('controller/UserController.php');
user::load('model/UserVO.php');

common::autoIncludes();
form::autoIncludes();


class UserView extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
    return true;
  }

  function main() {

  }

  function loginForm() {

    $form = new FormController( 'loginForm', '/user/loginForm' ); //actionform


    $form->setField( 'userLogin', array( 'placeholder' => 'Login' ));
    $form->setField( 'userPassword', array( 'placeholder' => 'Contraseña') );

    $form->setField( 'loginSubmit', array( 'type' => 'submit', 'value' => 'Entrar' ) );

    /******************************************************************************************** VALIDATIONS */
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
    if( isset( $postData[ 'cgIntFrmId' ] ) ) {
      // Creamos un objeto recuperandolo de session y añadiendo los datos POST
      $form = new FormController( false, false, $postData[ 'cgIntFrmId' ], $postData );
      // Creamos un objeto con los validadores
      $validator = new FormValidators();

      // y lo asociamos
      $form->setValidationObj( $validator );

      $form->validateForm();
      $jvErrors = $form->getJVErrors();

      //Si todo esta OK!
      if( sizeof( $jvErrors ) == 0 ){

        $valuesArray = $form->getValuesArray();
        $userControl = new UserController();
        $res = $userControl->authenticateUser($valuesArray['userLogin'], $valuesArray['userPassword']);
      }

      if( sizeof( $jvErrors ) > 0 ) {
        echo json_encode(
          array(
            'success' => 'error',
            'jvErrors' => $jvErrors,
            'formError' => 'El servidor no considera válidos los datos. NO SE HAN GUARDADO.'
          )
        );
      }
      else {
        echo json_encode( array( 'success' => 'success') );
      }

    } //if( isset( $postData[ 'cgIntFrmId' ] ) )
    else {
      echo json_encode(
        array(
          'success' => 'error',
          'error' => 'Los datos del formulario no han llegado bien al servidor. NO SE HAN GUARDADO.'
        )
      );
    }
  }
  //END Login

  function registerForm() {

    $form = new FormController( 'registerForm', '/user/sendregisterform' ); //actionform


    $form->setField( 'userLogin', array( 'placeholder' => 'Login' ) );
    $form->setField( 'userPassword', array( 'placeholder' => 'Contraseña' ) );
    $form->setField( 'userPassword2', array( 'placeholder' => 'Repite contraseña' ) );

    $form->setField( 'userName', array( 'placeholder' => 'Nombre' ) );
    $form->setField( 'userSurname', array( 'placeholder' => 'Apellidos' ) );
    $form->setField( 'userEmail', array( 'placeholder' => 'Email' ) );

    $form->setField( 'userRole', array( 'placeholder' => 'Rol' ) );

    $form->setField( 'userDescription', array( 'type' => 'textarea', 'placeholder' => 'Descripción' ) );
    $form->setField( 'userAvatar', array( 'placeholder' => 'Avatar' ) );

    $form->setField( 'registerSubmit', array( 'type' => 'submit', 'value' => 'Registrar' ) );

    /******************************************************************************************** VALIDATIONS */
    $form->setValidationRule( 'userLogin', 'required' );
    $form->setValidationRule( 'userPassword', 'required' );
    $form->setValidationRule( 'userPassword2', 'required' );

    $form->setValidationRule( 'userPassword', 'equalTo', '#userPassword2' );

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
    if( isset( $postData[ 'cgIntFrmId' ] ) ) {
      // Creamos un objeto recuperandolo de session y añadiendo los datos POST
      $form = new FormController( false, false, $postData[ 'cgIntFrmId' ], $postData );
      // Creamos un objeto con los validadores
      $validator = new FormValidators();

      // y lo asociamos
      $form->setValidationObj( $validator );

      $form->validateForm();
      $jvErrors = $form->getJVErrors();

      //Si todo esta OK!
      if( sizeof( $jvErrors ) == 0 ){

        $valuesArray = $form->getValuesArray();
        $userControl = new UserController();
        $res = $userControl->authenticateUser($valuesArray['registerLogin'], $valuesArray['registerPassword']);
      }

      if( sizeof( $jvErrors ) > 0 ) {
        echo json_encode(
          array(
            'success' => 'error',
            'jvErrors' => $jvErrors,
            'formError' => 'El servidor no considera válidos los datos. NO SE HAN GUARDADO.'
          )
        );
      }
      else {
        echo json_encode( array( 'success' => 'success') );
      }

    } //if( isset( $postData[ 'cgIntFrmId' ] ) )
    else {
      echo json_encode(
        array(
          'success' => 'error',
          'error' => 'Los datos del formulario no han llegado bien al servidor. NO SE HAN GUARDADO.'
        )
      );
    }
  }

}

