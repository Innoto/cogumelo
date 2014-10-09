<?php


Cogumelo::load('c_view/View.php');
Cogumelo::load('c_controller/FormController.php');
Cogumelo::load('c_controller/FormValidators.php');


class FormAction extends View
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



  function actionForm() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $formError = false;
    $postData = null;

    $postDataJson = file_get_contents('php://input');
    error_log( $postDataJson );
    if( $postDataJson !== false && strpos( $postDataJson, '{' )===0 ) {
      $postData = json_decode( $postDataJson, true );
    }

    error_log( print_r( $postData, true ) );

    if( isset( $postData[ 'cgIntFrmId' ] ) ) {
      // Creamos un objeto recuperandolo de session y añadiendo los datos POST
      $form = new FormController( false, false, $postData[ 'cgIntFrmId' ], $postData );
      // Creamos un objeto con los validadores
      $validator = new FormValidators();
      // y lo asociamos
      $form->setValidationObj( $validator );

      // CAMBIANDO AS REGLAS
      $form->setValidationRule( 'input1', 'minlength', '3' );
      $form->setValidationRule( 'input2', 'maxlength', '8' );

      // REGLAS:
      // 'input1', 'required' - 'numberEU'
      // 'input2', 'required' - 'minlength', '8'

      $form->validateForm();

      $jvErrors = $form->getJVErrors();

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



} // class FormAction extends View
