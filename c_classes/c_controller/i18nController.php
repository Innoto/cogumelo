<?php
C_ClassLoader::Load('c_vendor/gettext/gettext.php');

class C_i18n {
	
	static function setLang($url) {
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
	}

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
	
	static function c_i18n_update() {
		if(GETTEXT_UPDATE != true)
			return;
		else if($_SERVER["REMOTE_ADDR"] == "127.0.0.1")
		{
			shell_exec('find '.COGUMELO_LOCATION.'/. ../c_app/. -iname "*.php" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o ../c_app/i18n/c_project.pot -L PHP');
				
			foreach(explode(',', 'gl,es,en') as $lng) {
				shell_exec('msgmerge -U ../c_app/i18n/c_project_'.$lng.'.po ../c_app/i18n/c_project.pot');
			}
	
		}
	}
	
	static function c_i18n_compile() {
		foreach(explode(',', 'gl,es,en') as $lng)
			shell_exec('msgfmt -c -v -o ../c_app/i18n/'.$lng.'/LC_MESSAGES/c_project.mo ../c_app/i18n/c_project_'.$lng.'.po');
	}
}