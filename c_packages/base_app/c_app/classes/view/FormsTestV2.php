<?php


Cogumelo::load('c_view/View.php');
Cogumelo::load('c_controller/FormControllerV2.php');
Cogumelo::load('c_controller/FormValidators.php');


class FormsTestV2 extends View
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



  function loadForm() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'FormsTestV2: loadForm');
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $form = new FormControllerV2( 'probaPorto', '/actionformV2' ); //actionform

    $form->setField( 'input1', array( 'placeholder' => 'Mete 1 valor', 'value' => '5' ) );
    $form->setField( 'input2', array( 'id' => 'meu2', 'label' => 'Meu 2', 'value' => 'valor888' ) );
    $form->setField( 'select1', array( 'type' => 'select', 'label' => 'Meu Select',
      'value' => array( '1', '2' ),
      'options'=> array( '0' => 'Zero', '1' => 'Opcion 1', '2' => 'Posto 2', 'asdf' => 'asdf' ),
      'multiple' => 'multiple'
      ) );
    $form->setField( 'check1', array( 'type' => 'checkbox', 'label' => 'Meu checkbox',
      'value' => array( '1', 'asdf' ),
      'options'=> array( '0' => 'Zero', '1' => 'Opcion 1', '2' => 'Posto 2', 'asdf' => 'asdf' )
      ) );
    $form->setField( 'radio1', array( 'type' => 'radio', 'label' => 'Meu radio', 'value' => '2',
      'options'=> array( '' => 'Vacio', '1' => 'Opcion 1', '2' => 'Posto 2', 'asdf' => 'asdf' )
      ) );
    $form->setField( 'submit', array( 'type' => 'submit', 'label' => 'Pulsa para enviar', 'value' => 'Manda' ) );

    $form->setValidationRule( 'input1', 'required' );
    //$form->setValidationRule( 'input1', 'numberEU' );
    //$form->setValidationRule( 'input1', 'regex', '^\d+$' );

    $form->setValidationRule( 'input2', 'required' );
    //$form->setValidationRule( 'input2', 'minlength', '8' );

    $form->setValidationRule( 'select1', 'required' );

    //$form->setValidationRule( 'select1', 'equalTo', '#input1' );
    //$form->setValidationRule( 'check1', 'required' );

    $form->saveToSession();

    print '<!DOCTYPE html>'."\n".
    '<html>'."\n".
    '<head>'."\n".
    '  <title>FORMs con Cogumelo</title>'."\n".
    '  <script src="/js/jquery.min.js"></script>'."\n".
    '  <script src="/js/jquery-cogumelo-forms.js"></script>'."\n".
    '  <script src="/js/jquery.serializeFormToObject.js"></script>'."\n".
    '  <script src="/js/jquery-validation/jquery.validate.js"></script>'."\n".
    '  <script src="/js/jquery-validation/additional-methods.min.js"></script>'."\n".
    '  <script src="/js/jquery-validation/inArray.js"></script>'."\n".
    '  <script src="/js/jquery-validation/regex.js"></script>'."\n".
    '  <script src="/js/jquery-validation/numberEU.js"></script>'."\n".
    '  <!-- script>$.validator.setDefaults( { submitHandler: function(){ alert("submitted!"); } } );</script -->'."\n".
    '  <style>div { border:1px dashed; margin:5px; } label.error, .formError{ color:red; border:2px solid red; }</style>'."\n".
    '</head>'."\n".
    '<body>'."\n".

    $form->getHtmpOpen()."\n".
    $form->getHtmlFields()."\n".

//$form->getHtmlFieldArray( 'check1' )['options']['2']['text'].$form->getHtmlFieldArray( 'check1' )['options']['2']['input']."\n".
'<div id="JQVMC-meu2-error">errores meu2... </div>'."\n".
'<div id="JQVMC-ungrupo-error">errores ungrupo... </div>'."\n".
'<div id="JQVMC-manual">errores manuales... </div>'."\n".
'<div class="JQVMC-formError">errores formError... </div>'."\n".

    $form->getHtmlClose()."\n".
    $form->getJqueryValidationJS()."\n".

    '</body>'."\n".
    '</html>'."\n";
  } // function loadForm()



  function actionForm() {
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( 'FormsTestV2: actionForm');
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
      $form = new FormControllerV2( false, false, $postData[ 'cgIntFrmId' ], $postData );
      // Creamos un objeto con los validadores
      $validator = new FormValidators();
      // y lo asociamos
      $form->setValidationObj( $validator );


      // CAMBIANDO AS REGLAS
      //$form->setValidationRule( 'input1', 'required' );
      //$form->setValidationRule( 'input2', 'required' );

      $form->setValidationRule( 'input1', 'numberEU' );
      $form->setValidationRule( 'input1', 'minlength', '3' );
      $form->setValidationRule( 'input2', 'maxlength', '3' );
      $form->setValidationRule( 'select1', 'required' );
      $form->setValidationRule( 'check1', 'required' );


      $form->validateForm();

      //$form->addJVError( 'manual', 'Ola meu... ERROR ;-)' );

      $jvErrors = $form->getJVErrors();


      if( sizeof( $jvErrors ) > 0 ) {
        $form->addJVError( 'formError', 'El servidor no considera válidos los datos. NO SE HAN GUARDADO.' );
        $form->addJVError( 'sinSitioDefinido', 'Error a lo loco :D' );
        $jvErrors = $form->getJVErrors();
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



  function phpinfo() {
    phpinfo();
  }




} // class FormsTestV2 extends View
