<?php
Cogumelo::load('c_view/View.php');
Cogumelo::load('controller/UserController.php');
Cogumelo::load('model/UserVO.php');

common::autoIncludes();
form::autoIncludes();


class UserForm extends View
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

    $form = new FormController( 'loginForm', '/loginForm' ); //actionform


    $form->setField( 'loginLogin', array( 'placeholder' => 'Login' ));
    $form->setField( 'loginPassword', array( 'placeholder' => 'Apellidos') );
    $form->setField( 'loginSubmit', array( 'type' => 'submit', 'value' => 'Entrar' ) );

    /******************************************************************************************** VALIDATIONS */
    $form->setValidationRule( 'loginLogin', 'required' );
    $form->setValidationRule( 'loginPassword', 'required' );

    $form->setValuesVO($dataVO);
    $form->saveToSession();

    $this->template->assign("loginFormOpen", $form->getHtmpOpen());
    $this->template->assign("loginFormFields", $form->getHtmlFieldsArray());
    $this->template->assign("loginFormClose", $form->getHtmlClose());
    $this->template->assign("loginFormValidations", $form->getJqueryValidationJS());

    $this->template->setTpl('lostForm.tpl');
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
        $res = $userControl->authenticateUser($valuesArray['loginLogin'], $valuesArray['loginPassword']);

        }
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

