<?php

class I18nController {
	/**
	* Prepare the enviroment to localize the project
	*/
	static function setLang($url_path = false) {
		
		global $c_lang, $lang_available;

	    if ($url_path)
	       $c_lang = $url_path[1];
	    else
	       $c_lang = LANG_DEFAULT;

		$domain = 'messages';
		$locale = $lang_available[$c_lang].'.utf8';
		$locale_dir = I18N_LOCALE;

		setlocale(LC_ALL, $locale);
		putenv("LC_ALL=$locale");
		bindtextdomain($domain, $locale_dir);
		bind_textdomain_codeset($domain, 'utf8');
		textdomain($domain);
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
