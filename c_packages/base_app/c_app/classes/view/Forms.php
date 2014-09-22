<?php


Cogumelo::load('c_view/View');
Cogumelo::load('c_controller/FormController');


class Forms extends View
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
    error_log( '--------------------------------' );error_log( '--------------------------------' );
    error_log( '--------------------------------' );error_log( '--------------------------------' );

    $form = new FormController( 'probaPorto', '/actionform' ); //actionform

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
    $form->setValidationRule( 'input1', 'numberEU' );
    //$form->setValidationRule( 'input1', 'regex', '^\d+$' );
    $form->setValidationRule( 'input2', 'required' );
    $form->setValidationRule( 'input2', 'minlength', '8' );
    //$form->setValidationRule( 'select1', 'equalTo', '#input1' );
    // Creamos ya la regla que controla el contenido
    // $this->setValidationRule( $field['name'], 'in', array_keys( $field['options'] ) );

    $form->saveToSession();

    print '<!DOCTYPE html>'."\n".
    '<html>'."\n".
    '<head>'."\n".
    '  <title>FORMs con Cogumelo</title>'."\n".
    '  <script src="/js/jquery.min.js"></script>'."\n".
    '  <script src="/js/jquery-cogumelo-forms.js"></script>'."\n".
    '  <script src="/js/jquery.serializeFormToObject.js"></script>'."\n".
    '  <script src="/js/jquery-validation/jquery.validate.min.js"></script>'."\n".
    '  <script src="/js/jquery-validation/additional-methods.min.js"></script>'."\n".
    '  <script src="/js/jquery-validation/inArray.js"></script>'."\n".
    '  <script src="/js/jquery-validation/regex.js"></script>'."\n".
    '  <script src="/js/jquery-validation/numberEU.js"></script>'."\n".
    '  <!-- script>$.validator.setDefaults( { submitHandler: function(){ alert("submitted!"); } } );</script -->'."\n".
    '  <style>div { border:1px dashed; margin:5px; } label.error{ color:red; }</style>'."\n".
    '</head>'."\n".
    '<body>'."\n".

    $form->getHtmlForm()."\n".
    $form->getHtmlField( 'check1', '2' )."\n".

    '</body>'."\n".
    '</html>'."\n";
  } // function loadForm()





  function ajaxUpload() {

    error_log( print_r( $_FILES, true ) );

    $fileName     = $_FILES["ajaxFileUpload"]["name"];// The file name
    $fileTmpLoc   = $_FILES["ajaxFileUpload"]["tmp_name"];// File in the PHP tmp folder
    $fileType     = $_FILES["ajaxFileUpload"]["type"];// The type of file it is
    $fileSize     = $_FILES["ajaxFileUpload"]["size"];// File size in bytes
    $fileErrorMsg = $_FILES["ajaxFileUpload"]["error"];// 0 for false... and 1 for true

    if (!$fileTmpLoc) { // if file not chosen
      die( "ERROR: Please browse for a file before clicking the upload button." );
    }

    if(move_uploaded_file($fileTmpLoc, $_SERVER['DOCUMENT_ROOT'].'test_upload/'.$fileName )) {
      die( "$fileName upload is complete" );
    }
    else {
      die( "move_uploaded_file function failed" );
    }
  } // function ajaxUpload() {


  function phpinfo() {
    phpinfo();
  }


}

