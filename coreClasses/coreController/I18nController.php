<?php


require_once(DEPEN_MANUAL_REPOSITORY.'/Gettext/src/autoloader.php');
require_once(DEPEN_MANUAL_REPOSITORY.'/Gettext/src/Translator.php');

class I18nController {
	/**
	* Prepare the enviroment to localize the project
	*/
	static function setLang($url) {
		global $locale;
		global $c_i18n_uri;

		$locale=self::GetLang($url);
		
		$c_i18n_uri = str_replace($locale.'/', '', $url);

		global $c_lang;
		/**
		* Prepare the enviroment to localize the project
		*/
		$domain = 'messages';
		$locale ="es_ES.utf8";
		$locale_dir = 'i18n/locale/';

		setlocale(LC_ALL, $locale);
		putenv("LC_ALL=$locale");
		bindtextdomain($domain, $locale_dir);
		bind_textdomain_codeset($domain, 'utf8');
		textdomain($domain);
	}


	static function t_($text) {

		/**
		* Prepare the enviroment to localize the project
		*/
		$domain = 'messages';
		$locale ="es_ES.utf8";
		$locale_dir = I18N_LOCALE;

		setlocale(LC_ALL, $locale);
		putenv("LC_ALL=$locale");
		bindtextdomain($domain, $locale_dir);
		bind_textdomain_codeset($domain, 'utf8');
		textdomain($domain);

		return _($text);
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
}
