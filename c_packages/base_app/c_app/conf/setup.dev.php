<?php

//
// Framework Path
//

define('COGUMELO_LOCATION', '/home/proxectos/cogumelo');

//
//  DB
//

define('DB_ENGINE', 	'mysql'); //supported Engines: ('mysql', '')
define("DB_HOSTNAME" ,	"localhost");
define("DB_PORT",		"3306");
define("DB_USER" , 		"base_app");
define("DB_PASSWORD", 	"q7w8e9r");
define("DB_NAME",		"base_app");


// allow cache with memcached
define('DB_ALLOW_CACHE', true);
require_once(SITE_PATH.'/conf/memcached.setup.php');  //memcached options


//
//  Url settings
//

define('SITE_PROTOCOL', (isset($_SERVER['HTTPS']))? 'https' : 'http');
define('SITE_HOST', SITE_PROTOCOL.'://'.$_SERVER['HTTP_HOST']);  // solo HOST sin ('/')
define('SITE_FOLDER', '/');  // SITE_FOLDER STARTS AND ENDS WITH SLASH ('/')
define('SITE_URL', SITE_HOST . SITE_FOLDER);
define('SITE_URL_HTTP', 'http://'.$_SERVER['HTTP_HOST'] . SITE_FOLDER);
define('SITE_URL_HTTPS', 'https://'.$_SERVER['HTTP_HOST'] . SITE_FOLDER);


if(SITE_PROTOCOL == 'https')
  define('SITE_URL_CURRENT', SITE_URL_HTTPS);
else
  define('SITE_URL_CURRENT', SITE_URL_HTTP);


//
//  Sendmail
//

define('SMTP_HOST', 'localhost');
define('SMTP_PORT', '25');
define('SMTP_AUTH', false);
define('SMTP_USER', '');
define('SMTP_PASS', '');

define('SYS_MAIL_FROM_NAME',    'Cogumelo Sender');
define('SYS_MAIL_FROM_EMAIL',   'cogumelo@cogumelo.org');


//
//  Templates
//

define('SMARTY_CONFIG', SITE_PATH.'conf/smarty');
define('SMARTY_COMPILE',SITE_PATH.'tmp/templates_c');
define('SMARTY_CACHE',  SITE_PATH.'tmp/cache');


//
//	Media server
//
define('MEDIASERVER_REFRESH_CACHE', true); // false for best performance in final server
define('MEDIASERVER_HOST', '/');
define('MEDIASERVER_MINIMIFY_FILES', true); // minimify js and css files
define('MEDIASERVER_TMP_CACHE_PATH', SITE_PATH.'tmp/mediaCache');
define('MEDIASERVER_FINAL_CACHE_PATH', 'mediaCache');
define('MEDIASERVER_COMPILE_LESS', false);

//
//  Modules
//
global $C_ENABLED_MODULES;
global $C_INDEX_MODULES;

$C_ENABLED_MODULES = array('mediaserver', 'i18nGetLang', 'testmodule', 'common', 'devel', 'form', 'table', 'user');
$C_INDEX_MODULES  = array('mediaserver', 'i18nGetLang', 'user', 'devel'); 			// before c_app/Cogumelo.php execution


//
//  Logs
//

define('BCK', SITE_PATH.'backups/');    //backups directory
define('LOGDIR', SITE_PATH.'log/');   //log files directory
define('LOG_RAW_SQL', false);   // Log RAW all SQL ¡WARNING! application passwords will dump into log files
define('DEBUG', true); // Set Debug mode to log debug messages on log
define('ERRORS', true); // Display errors on screen. If you use devel module, you might disable it


//
//  Devel Mod
//

//global $DEVEL_ALLOWED_HOSTS;
//$DEVEL_ALLOWED_HOSTS = array( '127.0.0.1','10.77.1.36', '55.7.8.7' );
if( IS_DEVEL_ENV || in_array( $_SERVER["REMOTE_ADDR"], array( '127.0.0.1','10.77.1.36', '55.7.8.7' ) ) ){
  define( 'MOD_DEVEL_ALLOW_ACCESS', true );
}
else {
  define( 'MOD_DEVEL_ALLOW_ACCESS', false );
}
define('MOD_DEVEL_URL_DIR', 'devel');
define( 'DEVEL_PASSWORD', 'develpassword' );


//
//  i18n
//

define('GETTEXT_UPDATE', true); // update gettext files when working in localhost
define('LANG_DEFAULT', 'gl');
define('LANG_AVAILABLE', 'gl,es,en');


