<?php

/**
* i18n Class
*/

class I18nController {

	/**
    * Prepare the enviroment to localize the project
    */

	function __($text) {

		
		$domain = 'messages';
		$locale ="gl_GL";
		$directory = I18N_LOCALE;

		bind_textdomain_codeset($domain, 'UTF-8');

		// Configurate language
		//putenv('LC_MESSAGES='.$locale);
		setlocale( LC_ALL,"");
		setlocale( LC_MESSAGES, $locale);

		// Set the translations table path
		bindtextdomain($domain, $directory);

		// Select domain
		textdomain($domain);


		/* Cosas en prueba */
/*		putenv('LANGUAGE='.$locale);
	    putenv('LANG='.$locale);
	    putenv('LC_ALL='.$locale);*/
		
		//setlocale(LC_ALL,$locale);
		
		textdomain($domain);
		


		$t = new Gettext\Translator();
		__currentTranslator($t);

		echo __($text);
	    

		/*$translations = Gettext\Extractors\Po::extract(I18N_LOCALE.'gl_GL/LC_MESSAGES/cogumelo_php.po');

		$translation = $translations->find(null, 'Hola soy la cadena a traducir al gallego ');

		var_dump($translation);

		$t = new Gettext\Translator();
	    __currentTranslator($t);
	    echo __($text);

	    echo $t->getTranslation('cogumelo', $context, $original);*/
	}

	function getLang($url) {
		$m= self::processUrl($url);
		
		if(array_key_exists(1,$m))
			return str_replace('/', '',$m[1]);
		else
			return LANG_DEFAULT;
	}
	
	function extractUrl($url) {
		$m= self::processUrl($url);
		
		if(array_key_exists(2,$m))
			return $m[2];
		else
			return $url;
	}
	
	function processUrl($url) {
		foreach(explode(',', 'gl,es,en') as $lng) {
			if(preg_match('^('.$lng.'/)(.*)^', $url, $m))
				break;
		}
		return $m;
	}

}