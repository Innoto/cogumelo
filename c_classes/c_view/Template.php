<?php

Cogumelo::load('c_controller/ModuleController.php');

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

    // Smarty Hack: http://www.smarty.net/forums/viewtopic.php?t=21352&sid=88c6bbab5fb1fd84d3e4f18857d3d10e
    Smarty::muteExpectedErrors();
  }

  function addClientScript($file_path, $module = false, $is_autoinclude = false)  {

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


    $include_chain = "\n".'<script type="text/javascript" src="'.$base_path.$file_path.'"></script>';

    if( $is_autoinclude ){
      if($module == 'vendor') {
        $this->js_autoincludes = $include_chain. $this->js_autoincludes;
      }
      else {
        $this->js_autoincludes .= $include_chain;
      }
    }
    else {
      $this->js_includes .= $include_chain;
    }

  }


  function addClientStyles( $file_path, $module = false, $is_autoinclude = false ) {

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


    if( !MEDIASERVER_COMPILE_LESS && substr($file_path, -5) == '.less' ) {
      $file_rel = "stylesheet/less";      
    }
    else {
      $file_rel = "stylesheet";
    }


    $include_chain = "\n".'<link rel="'.$file_rel.'" type="text/css" href="'.$base_path.$file_path.'">';

    if( $is_autoinclude ) {
      if($module == 'vendor'){
        $this->css_autoincludes = $include_chain.$this->css_autoincludes;  
      }
      else{
        $this->css_autoincludes .= $include_chain;  
      }
    }
    else {
      $this->css_includes .= $include_chain;  
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
          $this->addClientStyles( $fileCss['src'], $fileCss['module'], true );
        }
      }

      if( is_array( $cogumeloIncludesJS ) ){
        foreach( $cogumeloIncludesJS as $fileJs ){
          $this->addClientScript( $fileJs['src'],  $fileJs ['module'], true);
        }
      }

      $this->assign('css_includes', $this->css_autoincludes. $this->css_includes );
      $this->assign('js_includes', $this->lessClientCompiler() . $this->js_autoincludes . $this->js_includes );

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


  function lessClientCompiler() {
    $ret = "";
    if( !MEDIASERVER_COMPILE_LESS ){
      $ret =  "\n".'<script>less = { env: "development", async: false, fileAsync: false, poll: 1000, '.
              'functions: { }, dumpLineNumbers: "all", relativeUrls: true, errorReporting: "console" }; </script>'."\n".
              '<script type="text/javascript" src="/vendor/less/dist/less-1.7.5.min.js"></script>';
    }

    return $ret;
  }
}

