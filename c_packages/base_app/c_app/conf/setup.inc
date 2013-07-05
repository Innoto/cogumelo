<?php

//
// Framework Path
//

define('COGUMELO_LOCATION', getcwd().'/../../..');


//
//	DB
//

define('DB_ENGINE', 	'mysql'); //supported Engines: ('mysql', '')
define("DB_HOSTNAME" ,	"localhost");
define("DB_PORT",		"3306");
define("DB_USER" , 		"root");
define("DB_PASSWORD", 	"q7w8e9r");
define("DB_NAME",		"test");

//
//	Url settings
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
//	Sendmail 
//

define("SMTP_HOST", "localhost");
define("SMTP_PORT", "25");
define("SMTP_AUTH", false);
define("SMTP_USER", "");
define("SMTP_PASS", "");

define('SYS_MAIL_FROM_NAME',    'Cogumelo Sender');
define('SYS_MAIL_FROM_EMAIL',   'cogumelo@cogumelo.org');


//
// 	Smarty & Template
//

define("SMARTY_CONFIG",	SITE_PATH."conf/smarty");
define("SMARTY_COMPILE",SITE_PATH."tmp/templates_c");
define("SMARTY_CACHE", 	SITE_PATH."tmp/cache");
define("MINIMIFY_FILES", false);
define("MINIMIFY_CACHE_PATH", SITE_PATH.'tmp/minimify');



//
//	Modules
//

global $C_ENABLED_MODULES;
$C_ENABLED_MODULES = array('mediaserver', 'devel', 'testmodule');

//
// 	Logs 
//

define("LOGDIR", SITE_PATH."log/");		//log files directory
define('LOG_RAW_SQL', false); 	// Log RAW all SQL ¡WARNING! application passwords will dump into log files 
define("DEBUG", true); 			// Set Debug mode to log debug messages on log
define("ERRORS", true); 		// Set Debug mode to display errors on screen (only for development)

//
//	Devel Mod
//

global $DEVEL_ALLOWED_HOSTS;
$DEVEL_ALLOWED_HOSTS = array('10.77.1.200', '55.7.8.7');
define("DEVEL_PASSWORD", 'develpassword'); 	

//
//	i18n 
//

define("GETTEXT_UPDATE",  true); // update gettext files when working in localhost
define("LANG_DEFAULT",  'gl');
define("LANG_AVAILABLE", 'gl,es,en');