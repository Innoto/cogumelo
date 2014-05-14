#!/usr/bin/php -q
<?php
  //
  // CARGO COGUMELO, É PRECISO FACER UNS CANTOS APAÑOS 
  //

  // Project location
 define('SITE_PATH', getcwd().'/c_app/');
  $_SERVER['HTTPS'] = false;
  $_SERVER['HTTP_HOST'] = '';
  $_SERVER['REQUEST_URI'] = 'cogumelo shell script';
  $_SERVER['REMOTE_ADDR'] = "local_shell";

  // cogumelo core Location
 set_include_path('.:'.SITE_PATH);

  require_once("conf/setup.dev.php"); 

  require_once(COGUMELO_LOCATION."/c_classes/CogumeloClass.php");
  require_once(SITE_PATH."/Cogumelo.php");

  global $_C;
  $_C =Cogumelo::get();

 
 if ($argc>1){ //parameters handler
    switch($argv[1]){
      case 'flush': // borra temporais 
	exec('rm tmp/*');
	echo 'archivos temporales borrados';
        break; 
      case 'create_db': // crea a base de datos
        echo 'create database';
        break; 
      case 'generate_tables': // xenera as táboas da bd 
        echo 'generate db tables';
        break;  
      case 'bck': // fai backup da bd no ficheiro indicado
	if ($argc>2){
	  $bd = $argv[2];
	  if ($argc>3){ //nome do ficheiro de bck
            $file = $argv[3];
          }
          else{ //crearase un bck.sql co nome da bbdd
            $file = $bd.'.sql';
          }
	}
	else{
	  echo "you must specify a database";
	}
       
        echo $file;
        break;  
      case 'restore': // restaura a bd pasada
        if ($argc>2)
	  $bd = $argv[2];
	else
	  echo "you must specify a database";
        break;  
      default:
        echo 'invalid parameter';
        exit;     
    }//end switch 
}//end parameters handler
else{
  echo 'execución sen parámetros';
}

//
//  CARGO MÓDULO DEVEL E O DEVELDBCONTROLLER, QUE É ONDE ESTÁN OS MÉTODOS QUE NOS INTERESAN
//
require_once(COGUMELO_LOCATION."/c_modules/devel/devel.php");
require_once(COGUMELO_LOCATION."/c_modules/devel/classes/controller/DevelDBController.php");

//
//  UTILIZO A CLASE COMO FARÍA DENDE UN VIEW NORMAL
//

$develdbcontrol = new DevelDBController();
var_dump( $develdbcontrol->getTablesSQL() ); // este método, por exemplo, devolvenos todo o  SQL xerado para os VO's do proxecto

?>
