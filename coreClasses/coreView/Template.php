<?php

Cogumelo::load('coreController/ModuleController.php');

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
    else
    if($module == 'vendor/bower') {
      $base_path = MEDIASERVER_HOST.'vendor/bower/';
    }
    else
    if($module == 'vendor/manual') {
      $base_path = MEDIASERVER_HOST.'vendor/manual/';
    }
    else {
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/module/'.$module.'/';
    }


    $include_chain = "\n".'<script type="text/javascript" src="'.$base_path.$file_path.'"></script>';

    if( $is_autoinclude ){

        $this->js_autoincludes .= $include_chain;

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
    else
    if($module == 'vendor/bower') {
      $base_path = MEDIASERVER_HOST.'vendor/bower/';
    }
    else
    if($module == 'vendor/manual') {
      $base_path = MEDIASERVER_HOST.'vendor/manual/';
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
      //if($module == 'vendor'){
      //  $this->css_autoincludes = $include_chain.$this->css_autoincludes;
      //}
      //else{
        $this->css_autoincludes .= $include_chain;
      //}
    }
    else {
      $this->css_includes .= $include_chain;
    }
  }

  function setTpl($file_name, $module = false) {
    $this->tpl = ModuleController::getRealFilePath('classes/view/templates/'.$file_name, $module );
  }



  function execToString() {
    ob_start();
    $this->exec();
    $result = ob_get_clean();
    return $result;
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

      // conf Variables
      $lessConfInclude = '';
      $jsConfInclude = '<script type="text/javascript" src="'.MEDIASERVER_HOST.MOD_MEDIASERVER_URL_DIR.'/jsConfConstants.js'.'"></script>'."\n";

      if( MEDIASERVER_COMPILE_LESS == false ){
        $lessConfInclude = '<link rel="stylesheet/less" type="text/css" href="'.MEDIASERVER_HOST.MOD_MEDIASERVER_URL_DIR.'/lessConfConstants.less'.'">'."\n";
      }

      // assign
      $this->assign('css_includes', $lessConfInclude.$this->css_autoincludes. $this->css_includes );
      $this->assign('js_includes', $jsConfInclude.$this->lessClientCompiler() . $this->js_autoincludes . $this->js_includes );

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
              '<script type="text/javascript" src="/vendor/bower/less/dist/less.min.js"></script>';
    }

    return $ret;
  }
}

