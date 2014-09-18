<?php

Cogumelo::load('c_vendor/Smarty/libs/Smarty.class.php');
Cogumelo::load('c_vendor/jsmin/jsmin.php');
Cogumelo::load('c_vendor/cssmin/cssmin.php');
Cogumelo::load('c_controller/ModuleController');

//
//  Template Class (Extends smarty library)
//

Class Template extends Smarty
{
  var $tpl;
  var $base_dir;
  var $css_includes = '';
  var $js_includes = '';


  public function __construct($base_dir)
  {
    parent::__construct();

    $this->base_dir = $base_dir;
    $this->config_dir = SMARTY_CONFIG;
    $this->compile_dir = SMARTY_COMPILE;
    $this->cache_dir = SMARTY_CACHE;
  }

  function addJs($file_path, $module = false)  {

    if($module)
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/module/'.$module.'/';
    else
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/';

    $this->js_includes .= "\n".'<script type="text/javascript" src="'.$base_path.$file_path.'"></script>';
  }


  function addCss($file_path, $module = false) {
    if($module)
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/module/'.$module.'/';
    else
      $base_path = '/'.MOD_MEDIASERVER_URL_DIR.'/';

    $this->css_includes .= "\n".'<link rel="stylesheet" type="text/css" href="'.$base_path.$file_path.'">';
  }

  function setTpl($file_name, $module = false) {
    $this->tpl = ModuleController::getRealFilePath('classes/view/templates/'.$file_name, $module );
  }


  function exec() {
    if($this->tpl) {

      $this->assign('css_includes', $this->css_includes);
      $this->assign('js_includes', $this->js_includes);

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

