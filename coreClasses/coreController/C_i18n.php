<?php

/*
	
	Esta clase non funciona ainda!
	
*/

Cogumelo::Load('c_vendor/gettext/gettext.inc');
Cogumelo::Load('c_vendor/PHPMailer/class.phpmailer.php');

class C_i18n {
	
	static function setLang() {
		$textdomain="c_cogumelo";
		
		$locale='es_ES';
		
		global $c_lang;
		
		putenv('LANGUAGE='.$locale);
		putenv('LANG='.$locale);
		putenv('LC_ALL='.$locale);
		putenv('LC_MESSAGES='.$locale);
		
		T_setlocale(LC_ALL,$locale);
		T_setlocale(LC_CTYPE,$locale);

		T_bindtextdomain($textdomain, SITE_PATH.'conf/i18n');
		T_bind_textdomain_codeset($textdomain, 'UTF-8'); 
		T_textdomain($textdomain);

		self::c_i18n_gettext();
		//self::c_i18n_compile();
	}

	/*static function setLang($url) {
		$textdomain="c_project";
		
		$locale=self::GetLang($url);
		
		global $c_lang;
		
		putenv('LANGUAGE='.$locale);
		putenv('LANG='.$locale);
		putenv('LC_ALL='.$locale);
		putenv('LC_MESSAGES='.$locale);
		
		T_setlocale(LC_ALL,$locale);
		T_setlocale(LC_CTYPE,$locale);
		
		T_bindtextdomain($textdomain, SITE_PATH.'/i18n');
		T_bind_textdomain_codeset($textdomain, 'UTF-8'); 
		T_textdomain($textdomain);
		
		self::C_i18n_compile();
	}*/

	static function getLang($url) {
		$m= self::processUrl($url);
		
		if(array_key_exists(1,$m))
			return str_replace('/', '',$m[1]);
		else
			return LANG_DEFAULT;
	}
	
	static function extractUrl($url) {
		$m= self::processUrl($url);
		
		if(array_key_exists(2,$m))
			return $m[2];
		else
			return $url;
	}
	
	static function processUrl($url) {
		foreach(explode(',', 'gl,es,en') as $lng) {
			if(preg_match('^('.$lng.'/)(.*)^', $url, $m))
				break;
		}
		return $m;
	}

	static function c_i18n_gettext(){
		if($_SERVER["REMOTE_ADDR"] == "127.0.0.1")
		{
			shell_exec('xgettext --from-code=UTF-8 -o '.SITE_PATH.'conf/i18n/en/LC_MESSAGES/messages.po '.SITE_PATH.'../*.php');
			print('xgettext --from-code=UTF-8 -o '.SITE_PATH.'conf/i18n/en/LC_MESSAGES/messages.po '.SITE_PATH.'../*.php');
			// Buscamos todos os textos en ficheiros .php
			//exec('find '.COGUMELO_LOCATION.'/. ../app/. -iname "*.php" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o ../app/i18n/c_project.pot -L PHP');
			foreach(explode(',', 'gl,es,en') as $lng) {
				shell_exec('xgettext --from-code=UTF-8 -o /home/proxectos/cogumelo/c_packages/sampleApp/httpdocs/../app/conf/i18n/en/LC_MESSAGES/messages_'.$lng.'.po /home/proxectos/cogumelo/c_packages/sampleApp/httpdocs/../app/../*.php');
			}
	
		}
	}
	
	static function c_i18n_update() {
		if(GETTEXT_UPDATE != true)
			return;
		else if($_SERVER["REMOTE_ADDR"] == "127.0.0.1")
		{
			shell_exec('find '.COGUMELO_LOCATION.'/. ../app/. -iname "*.php" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o ../app/i18n/c_project.pot -L PHP');
				
			foreach(explode(',', 'gl,es,en') as $lng) {
				shell_exec('msgmerge -U ../app/i18n/c_project_'.$lng.'.po ../app/i18n/c_project.pot');
			}
	
		}
	}
	
	static function c_i18n_compile() {
		//exec('msgfmt -c -v -o conf/i18n/'.$lng.'/LC_MESSAGES/c_project.mo conf/i18n/c_project_'.$lng.'.po');
		foreach(explode(',', 'gl,es,en') as $lng)
			shell_exec('msgfmt -c -v -o conf/i18n/'.$lng.'/LC_MESSAGES/c_project.mo conf/i18n/c_project_'.$lng.'.po');
	}
}