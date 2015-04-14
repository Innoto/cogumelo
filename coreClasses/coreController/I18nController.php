<?php

/**
* i18n Class
*
* 
* @author: pablinhob
*/

 require_once(COGUMELO_LOCATION."/packages/sampleApp/httpdocs/vendor/composer/gettext/gettext/Gettext/autoloader.php");
 require_once(COGUMELO_LOCATION."/packages/sampleApp/httpdocs/vendor/composer/gettext/gettext/Gettext/translator_functions.php");

class I18nController {

	/**
    * Prepare the enviroment to localize the project
    */

	function __($text) {

		$directory = I18N_LOCALE;
		$domain = 'cogumelo';
		$locale ="gl_GL";

		//putenv("LANG=".$locale); //not needed for my tests, but people say it's useful for windows

		putenv('LC_MESSAGES=gl_GL');
		setlocale( LC_MESSAGES, $locale);
		bindtextdomain($domain, $directory);
		textdomain($domain);
		bind_textdomain_codeset($domain, 'UTF-8');

		$t = new Gettext\Translator();
	    __currentTranslator($t);

	    echo $domain;

	    echo gettext($text);

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