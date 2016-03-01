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
      'includes' => array( 'dist/jquery.validate.js', 'dist/additional-methods.js' )
    ),
    array(
      'id' =>'ckEditorFix',
      'params' => array( 'ckEditorFix' ),
      'installer' => 'manual',
      'includes' => array( 'ckEditorFix.js' ),
    ),
    array(
      'id' =>'ckeditor',
      'params' => array( 'ckeditor#standard/stable' ),
      'installer' => 'bower',
      'includes' => array( 'ckeditor.js' ),
      'autoinclude' => false
    )

  );


  public $includesCommon = array(
    'controller/FormController.php',
    'controller/FormValidators.php',
    'js/jquery.cogumeloFormController.js',
    'js/jquery.serializeFormToObject.js',
    'js/formValidators.js',
    'js/formValidatorsExtender.js',
    'styles/form.less'
  );


  public function __construct() {
    $this->addUrlPatterns( '#^cgml-form-htmleditor-config.js#', 'view:FormConnector::customCkeditorConfig' );
    $this->addUrlPatterns( '#^cgml-form-file-upload$#', 'view:FormConnector::fileUpload' );
    $this->addUrlPatterns( '#^cgml-form-group-element$#', 'view:FormConnector::groupElement' );
  }


}
