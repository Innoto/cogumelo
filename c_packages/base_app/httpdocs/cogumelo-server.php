<?php
// We check that the conexion comes from localhost
if ($_SERVER['REMOTE_ADDR']=='127.0.0.1'){

  // Project location
  define('SITE_PATH', getcwd().'/../c_app/');

  // Include cogumelo core Location
  set_include_path('.:'.SITE_PATH);

  require_once('conf/setup.php');



  require_once(COGUMELO_LOCATION.'/c_classes/CogumeloClass.php');
  require_once(COGUMELO_LOCATION.'/c_classes/c_controller/DependencesController.php');
  require_once(SITE_PATH.'/Cogumelo.php');

  $par = $_GET['q'];
  switch ($par){
    case 'rotate_logs':
      $dir = SITE_PATH.'log/';
      $handle = opendir($dir);
      while ($file = readdir($handle)){
          if (is_file($dir.$file)){
              $file = $dir.$file;
              $pos = strpos($file, 'gz');
              if ($pos==false){
                  $gzfile = $file.'-'.date('Ymd-Hms').'.gz';
                  $fp = gzopen($gzfile, 'w9');
                  gzwrite ($fp, file_get_contents($file));
                  gzclose($fp);
              }
          }
      }
      break;
    case 'flush':
      $dir = SITE_PATH.'tmp/templates_c/';
      $handle = opendir($dir);
      while ($file = readdir($handle)){
          if (is_file($dir.$file)){
              unlink($dir.$file);
          }
      }
      break;
    case 'client_caches':
      Cogumelo::load('c_controller/ModuleController.php');
      require_once( ModuleController::getRealFilePath( 'mediaserver.php',  'mediaserver') );
      mediaserver::autoIncludes();
      CacheUtilsController::generateAllCaches();
      
  }
}
else{
    exit;
}
