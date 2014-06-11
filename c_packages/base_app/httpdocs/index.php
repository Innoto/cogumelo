<?php

// Project location
define('SITE_PATH', getcwd().'/../c_app/');

// cogumelo core Location
set_include_path('.:'.SITE_PATH);


if ( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ) {
	require_once("conf/setup.dev.php"); 
}
else {
	require_once("conf/setup.final.php"); 
}

require_once(COGUMELO_LOCATION."/c_classes/CogumeloClass.php");
require_once(SITE_PATH."/Cogumelo.php");

// error & warning handlers
set_error_handler('Cogumelo::warningHandler');
register_shutdown_function('Cogumelo::errorHandler');
if(!ERRORS) {
	ini_set("display_errors", 0); 
}


global $_C;
$_C =Cogumelo::get();
$_C->exec();
