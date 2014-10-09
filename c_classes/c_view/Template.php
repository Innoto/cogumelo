<?php

Cogumelo::vendorLoad('smarty/smarty/distribution/libs/Smarty.class.php');
Cogumelo::load('c_controller/ModuleController');

//
//  Template Class (Extends smarty library)
//

Class Template extends Smarty
{
  var $tpl;
  var $base_dir;

  var $css_autoincludes = '';
  var $css_includes = '';
  var $js_autoincludes = '';  
  var $js_includes = '';


  public function __construct($base_dir)
  {
    parent::__construct();

    $this->base_dir = $base_dir;
    $this->config_dir = SMARTY_CONFIG;
    $this->compile_dir = SMARTY_COMPILE;
    $this->cache_dir = SMARTY_CACHE;
  }

  function addClientScript($file_path, $module = false, $vendor=false, $is_autoinclude = false)  {

    if($module == false){
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/';
    }
    else 
    if($module == 'vendor') {
      $base_path = MEDIASERVER_HOST.'vendor/';
    }
    else {
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/module/'.$module.'/';
    }


    if( $is_autoinclude ){
      $this->js_autoincludes .= "\n".'<script type="text/javascript" src="'.$base_path.$file_path.'"></script>';
    }
    else {
      $this->js_includes .= "\n".'<script type="text/javascript" src="'.$base_path.$file_path.'"></script>';
    }

  }


  function addClientStyles( $file_path, $module = false, $vendor=false, $is_autoinclude = false ) {

    if($module == false){
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/';
    }
    else 
    if($module == 'vendor') {
      $base_path = MEDIASERVER_HOST.'vendor/';
    }
    else {
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/module/'.$module.'/';
    }



    if( $is_autoinclude ){
      $this->css_autoincludes .= "\n".'<link rel="stylesheet" type="text/css" href="'.$base_path.$file_path.'">';
    }
    else {
      $this->css_includes .= "\n".'<link rel="stylesheet" type="text/css" href="'.$base_path.$file_path.'">';
    }
  }

  function setTpl($file_name, $module = false) {
    $this->tpl = ModuleController::getRealFilePath('classes/view/templates/'.$file_name, $module );
  }


  function exec() {
    if($this->tpl) {

      global $cogumeloIncludesCSS;
      global $cogumeloIncludesJS;

      if( is_array( $cogumeloIncludesCSS ) ){
        foreach( $cogumeloIncludesCSS as $fileCss){
          $this->addClientStyles( $fileCss['src'], $fileCss['module'] );
        }
      }

      if( is_array( $cogumeloIncludesJS ) ){
        foreach( $cogumeloIncludesJS as $fileJs ){
          $this->addClientScript( $fileJs['src'],  $fileJs ['module']);
        }
      }

      $this->assign('css_includes', $this->css_autoincludes . $this->css_includes );
      $this->assign('js_includes', $this->js_autoincludes . $this->js_includes );

      if( file_exists($this->tpl) ) {
        $this->display($this->tpl);
        Cogumelo::debug('Template class displays tpl '.$this->tpl);
      }
      else {
        Cogumelo::error('Template not found: '.$this->tpl );
      }
    }
    else {
      Cogumelo::error('Template: no tpl file defined');
    }
  }
}

