<?php

//
// Framework Path
//

define('COGUMELO_LOCATION', getcwd().'/../../cogumelo_trunk');


//
//	DB
//

define('DB_ENGINE', 	'mysql'); //supported Engines: ('Mysql', '')
define("DB_HOSTNAME" ,	"localhost");
define("DB_PORT",		"3306");
define("DB_USER" , 		"");
define("DB_PASSWORD", 	"");
define("DB_NAME",		"");

//
//	Url settings
//

if(isset($_SERVER['HTTPS']))
	define('SITE_PROTOCOL', 'https');
else
	define('SITE_PROTOCOL', 'http');
define('SITE_HOST', SITE_PROTOCOL.'://'.$_SERVER['HTTP_HOST']);  // solo HOST sin ('/')
define('SITE_URI', '/');  // SITE_URI STARTS AND ENDS WITH SLASH ('/')
define('SITE_URL', SITE_HOST . SITE_URI);
define('SITE_URL_HTTP', 'http://'.$_SERVER['HTTP_HOST'] . SITE_URI); 
define('SITE_URL_HTTPS', 'https://'.$_SERVER['HTTP_HOST'] . SITE_URI); 


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
// 	Smarty
//

define("TEMPLATE_DIR", 	SITE_PATH."templates");
define("SMARTY_CONFIG",	SITE_PATH."conf/smarty");
define("SMARTY_COMPILE",SITE_PATH."tmp/templates_c");
define("SMARTY_CACHE", 	SITE_PATH."tmp/cache");


//
//	i18n 
//

define("GETTEXT_UPDATE",  true); // update gettext files when working in localhost
define("LANG_DEFAULT",  'gl');
define("LANG_AVAILABLE", 'gl,es,en');


//
// 	Logs 
//
define("LOGDIR", 'log/');		//log files directory
define('LOG_RAW_SQL', false); 	// Log RAW all SQL ¡WARNING! application passwords will dump into log files 
define("DEBUG", true); 			// Set Debug mode to display errors on screen (only for development)
