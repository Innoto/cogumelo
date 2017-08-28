<?php

Cogumelo::load( 'coreController/Module.php' );


class form extends Module {

  public $name = 'form';
  public $version = 1.0;
  public $dependences = array(
    array(
      'id' => 'jquery-validation',
      'params' => array( 'jquery-validate#1.14' ),
      'installer' => 'bower',
      'includes' => [ 'dist/jquery.validate.min.js', 'dist/additional-methods.min.js' ]
    ),
    array(
      'id' =>'ckEditorFix',
      'params' => array( 'ckEditorFix' ),
      'installer' => 'manual',
      'includes' => array( 'ckEditorFix.js' ),
    ),
    array(
      'id' =>'ckeditor',
      'params' => array( 'ckeditor#full/stable' ),
      'installer' => 'bower',
      'includes' => array( 'ckeditor.js' ),
      'autoinclude' => false
    )
  );


  public $includesCommon = array(
    'controller/FormController.php',
    'controller/FormValidators.php',
    // 'js/jquery.cogumeloFormController.js', // js controller V1
    'js/formController.js', // js controller V2
    'js/jquery.serializeFormToObject.js',
    'js/formValidators.js',
    'js/formValidatorsExtender.js',
    'styles/form.less'
  );


  public function __construct() {
    $this->addUrlPatterns( '#^cgml-form-htmleditor-config.js#', 'view:FormConnector::customCkeditorConfig' );
    $this->addUrlPatterns( '#^cgml-form-file-upload$#', 'view:FormConnector::fileUpload' );
    $this->addUrlPatterns( '#^cgml-form-group-element$#', 'view:FormConnector::execCommand' );
    $this->addUrlPatterns( '#^cgml-form-command$#', 'view:FormConnector::execCommand' );
  }
}
