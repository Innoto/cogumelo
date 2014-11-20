<?php
/**
 * Previamente ya se definen los siguientes valores:
 *
 * WEB_BASE_PATH - Apache DocumentRoot (declarado en index.php)
 *
 * APP_BASE_PATH - App Path (declarado en index.php)
 * SITE_PATH - App Path (declarado en index.php)
 *
 * IS_DEVEL_ENV - Indica si estamos en el entorno de desarrollo (declarado en setup.php)
 *
 *
 * Normas de estilo:
 *
 * * Nombres:
 * - Inicia por MOD_NOMBREMODULO_ para modulos
 * - Finalizan en _PATH para rutas
 *
 * * Valores:
 * - Las rutas no finalizan en /
 * - Las URL no finalizan en /
 *
 */







//
//  APP
//

define( 'APP_TMP_PATH', APP_BASE_PATH.'/tmp' );


//
// Framework Path
//

define( 'COGUMELO_LOCATION', '/home/proxectos/cogumelo' );


//
//  DB
//

define( 'DB_ENGINE', 'mysql' );
define( 'DB_HOSTNAME', 'localhost');
define( 'DB_PORT', '3306');
define( 'DB_USER', 'base_app');
define( 'DB_PASSWORD', 'q7w8e9r');
define( 'DB_NAME', 'base_app');


// allow cache with memcached
define( 'DB_ALLOW_CACHE', true );
require_once( APP_BASE_PATH.'/conf/memcached.setup.php' );  //memcached options


//
//  Url settings
//

// TODO: Cuidado porque no se admite un puerto
define( 'SITE_PROTOCOL', isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' );
define( 'SITE_HOST', SITE_PROTOCOL.'://'.$_SERVER['HTTP_HOST']);  // solo HOST sin ('/')
define( 'SITE_FOLDER', '/' );  // SITE_FOLDER STARTS AND ENDS WITH SLASH ('/')
define( 'SITE_URL', SITE_HOST . SITE_FOLDER );
define( 'SITE_URL_HTTP', 'http://'.$_SERVER['HTTP_HOST'] . SITE_FOLDER );
define( 'SITE_URL_HTTPS', 'https://'.$_SERVER['HTTP_HOST'] . SITE_FOLDER );
define( 'SITE_URL_CURRENT', SITE_PROTOCOL == 'http' ? SITE_URL_HTTP : SITE_URL_HTTPS );


//
//  Sendmail
//

define( 'SMTP_HOST', 'localhost' );
define( 'SMTP_PORT', '25' );
define( 'SMTP_AUTH', false );
define( 'SMTP_USER', '' );
define( 'SMTP_PASS', '' );

define( 'SYS_MAIL_FROM_NAME', 'Cogumelo Sender' );
define( 'SYS_MAIL_FROM_EMAIL', 'cogumelo@cogumelo.org' );


//
//  Templates
//

define( 'SMARTY_CONFIG', APP_BASE_PATH.'/conf/smarty' );
define( 'SMARTY_COMPILE', APP_TMP_PATH.'/templates_c' );
define( 'SMARTY_CACHE', APP_TMP_PATH.'/cache' );


//
//	Media server
//

global $MEDIASERVER_LESS_CONSTANTS;
global $MEDIASERVER_JAVASCRIPT_CONSTANTS;

$MEDIASERVER_LESS_CONSTANTS = array('variable1' =>1,  'variable2'=>'red', 'variable3'=>'blue;' );
$MEDIASERVER_JAVASCRIPT_CONSTANTS = array('variable1' =>5,  'variable2'=>'red', 'variable3'=>'blue;' );
define( 'MEDIASERVER_REFRESH_CACHE', true ); // false for best performance in final server
define( 'MEDIASERVER_HOST', '/' );
define( 'MEDIASERVER_MINIMIFY_FILES', false ); // minimify js and css files
define( 'MEDIASERVER_TMP_CACHE_PATH', APP_TMP_PATH.'/mediaCache' );
define( 'MEDIASERVER_FINAL_CACHE_PATH', 'mediaCache' );
define( 'MEDIASERVER_COMPILE_LESS', false );



//
//  Module load
//

global $C_ENABLED_MODULES;
global $C_INDEX_MODULES;

$C_ENABLED_MODULES = array( 'mediaserver', 'i18nGetLang', 'common', 'devel', 'admin', 'filedata', 'form', 'table', 'user' );
// before c_app/Cogumelo.php execution
$C_INDEX_MODULES  = array( 'mediaserver', 'i18nGetLang', 'user', 'form', 'admin', 'devel' ); // DEVEL SIEMPRE DE ULTIMO!!!


//
//  Logs
//

define( 'LOGDIR', APP_BASE_PATH.'/log/' ); //log files directory
define( 'LOG_RAW_SQL', false ); // Log RAW all SQL ¡WARNING! application passwords will dump into log files
define( 'DEBUG', true ); // Set Debug mode to log debug messages on log
define( 'ERRORS', true ); // Display errors on screen. If you use devel module, you might disable it


//
// Backups
//

define( 'BCK', APP_BASE_PATH.'/backups/' ); //backups directory


//
//  Devel Mod
//

define( 'MOD_DEVEL_ALLOW_ACCESS', true );
define( 'MOD_DEVEL_URL_DIR', 'devel' );
define( 'MOD_DEVEL_PASSWORD', 'develpassword' );


//
//  i18n
//

define( 'GETTEXT_UPDATE', true ); // update gettext files when working in localhost
define( 'LANG_DEFAULT', 'gl' );
define( 'LANG_AVAILABLE', 'gl,es,en' );


//
//  Form Mod
//

define( 'MOD_FORM_CSS_PRE', 'cgmMForm' );
define( 'MOD_FORM_FILES_TMP_PATH', APP_TMP_PATH.'/formFiles' );
define( 'MOD_FORM_FILES_APP_PATH', APP_BASE_PATH.'/../formFiles' );
//define( 'FORM_FILES_APP_PATH', WEB_BASE_PATH.'/formFiles' );


//
// Dependences PATH
//
define( 'DEPEN_COMPOSER_PATH', WEB_BASE_PATH.'/vendor/composer' );
define( 'DEPEN_BOWER_PATH', WEB_BASE_PATH.'/vendor/bower' );
define( 'DEPEN_MANUAL_PATH', WEB_BASE_PATH.'/vendor/manual' );

define( 'DEPEN_MANUAL_REPOSITORY', COGUMELO_LOCATION.'/c_packages/vendorPackages' );
