<?php

Cogumelo::load( 'coreController/Module.php' );


class form extends Module {

  public $name = 'form';
  public $version = 1.0;
  public $dependences = [

    [
      'id' => 'jquery-validation',
      'params' => array( 'jquery-validation@1.14.0' ),
      'installer' => 'yarn',
      'includes' => array( 'dist/jquery.validate.js', 'dist/additional-methods.js' )
    ],
    [
      'id' =>'ckEditorFix',
      'params' => [ 'ckEditorFix' ],
      'installer' => 'manual',
      'includes' => [ 'ckEditorFix.js' ],
    ],
    /*[
      'id' =>'ckeditor',
      'params' => [ 'ckeditor#full/stable' ],
      'installer' => 'bower',
      'includes' => [ 'ckeditor.js' ],
      'autoinclude' => false
    ],*/
    [
      'id' =>'ckeditor',
      'params' => [ 'ckeditor' ],
      'installer' => 'yarn',
      'includes' => [ 'ckeditor.js' ],
      'autoinclude' => false
    ],
    [
      'id' =>'grapesjs',
      'params' => [ 'grapesjs@0.14.15' ],
      'installer' => 'yarn',
      'includes' => [ 'dist/grapes.min.js', 'dist/css/grapes.min.css' ],
      'autoinclude' => false
    ],
    [
      'id' =>'grapesjsPresetWebpage',
      'params' => [ 'grapesjs-preset-webpage' ],
      'installer' => 'manual',
      'includes' => [ 'dist/grapesjs-preset-webpage.min.js', 'dist/grapesjs-preset-webpage.min.css' ],
      'autoinclude' => false
    ],
  ];


  public $includesCommon = [
    'controller/FormController.php',
    'controller/FormValidators.php',
    'view/FormConnectorFiles.php',
    // 'js/jquery.cogumeloFormController.js', // js controller V1
    'js/formController.js', // js controller V2
    'js/jquery.serializeFormToObject.js',
    'js/formValidators.js',
    'js/formValidatorsExtender.js',
    'styles/masterForm.less'
  ];


  public function __construct() {
    $this->addUrlPatterns( '#^cgml-form-htmleditor-config.js#', 'view:FormConnector::customCkeditorConfig' );
    $this->addUrlPatterns( '#^cgml-form-file-upload$#', 'view:FormConnector::fileUpload' );
    $this->addUrlPatterns( '#^cgml-form-group-element$#', 'view:FormConnector::execCommand' );
    $this->addUrlPatterns( '#^cgml-form-command$#', 'view:FormConnector::execCommand' );
  }
}
