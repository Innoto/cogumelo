<?php

/**
* i18nScriptController Class
*/

require_once(DEPEN_MANUAL_PATH.'/Gettext/src/autoloader.php');
require_once(DEPEN_MANUAL_PATH.'/Gettext/src/Translator.php');

class i18nScriptController {

	var $dir_path;
	var $dir_modules_c;
	var $dir_modules;
	var $textdomain;
	var $modules;
	var $lc_1;
	var $dir_lc = array();
	
	function __construct()
	{
		$this->dir_path = I18N_LOCALE;
	    $this->dir_modules_c = COGUMELO_LOCATION.'/coreModules/';
	    $this->dir_modules = SITE_PATH.'modules/';
	    $this->textdomain="messages";
	    global $C_ENABLED_MODULES, $LANG_AVAILABLE;
	    $this->modules = $C_ENABLED_MODULES;
	    $this->lang = $LANG_AVAILABLE;

	    foreach ($LANG_AVAILABLE as $l => $lang){
	    	$this->dir_lc[$l] = $this->dir_path.$lang['i18n'].'/LC_MESSAGES';
	    }
	}

	/**
    * Prepare the enviroment to localize the project
    */
	function setEnviroment() {
	    $locale= $this->lang[LANG_DEFAULT]['i18n'];

	    putenv('LANGUAGE='.$locale);
	    putenv('LANG='.$locale);
	    putenv('LC_ALL='.$locale);
	    putenv('LC_MESSAGES='.$locale);

	    setlocale(LC_ALL,$locale);
	    setlocale(LC_CTYPE,$locale);

	    bindtextdomain($this->textdomain, $this->dir_path);
	    bind_textdomain_codeset($this->textdomain, 'UTF-8');
	    textdomain($this->textdomain);
	}

	function checkTranslations($l){
		$exist = false;
		if (is_dir($l)){
	        $handle = opendir($l);
	        while ($file = readdir($handle)) {
	            if ($file==$this->textdomain.'.po'){
	              $exist = true;
	            }  
	          }
	       }

    	return $exist;
	}

	/**
    * Get all the text to be translated and update or create a file.po if not exists
    */
	function c_i18n_gettext(){

		error_reporting(E_ALL ^ E_NOTICE);

    	/******************************/
	    /************ PHP *************/
	    /******************************/

	    // get all the .php files unless files into modules folder
	    $all_files_php = CacheUtilsController::listFolderFiles(COGUMELO_LOCATION, array('php'), false);
	    foreach($all_files_php as $i => $dir){
	      if (strpos($dir,'/coreModules/')===false && strpos($dir,'/modules/')===false
	          && strpos($dir,'/vendorServer/')===false && strpos($dir,'/tmp/')===false){ // We exclude files from modules, tmp and vendorServer
	            $files_php[$i] = $dir->getRealPath();
	      }
	    }

	    // get the .php files into modules folder
	    $files_module_php = CacheUtilsController::listFolderFiles($this->dir_modules, array('php'), false);
	    foreach ($this->modules as $i => $dir) {
	      foreach ($files_module_php as $k => $file) {
	        $parts = explode('/'.$dir.'/',$file);
	        if (sizeof($parts)==2){
	          //$parts[1] = (string) ereg_replace('[[:space:]]+','',$parts[1]);
	          $array_php_module[$k] = ModuleController::getRealFilePath($parts[1], $dir);// Array of files with gettext strings
	        }
	      }
	    }

	    // get the .php files into coreModules folder
	    $files_module_php_c = CacheUtilsController::listFolderFiles($this->dir_modules_c, array('php'), false);
	    foreach ($this->modules as $i => $dir) {
	      foreach ($files_module_php_c as $k => $file) {
	        $parts = explode('/'.$dir.'/',$file);
	        if (sizeof($parts)==2){
	          $array_php_module_c[$k] = ModuleController::getRealFilePath($parts[1], $dir);// Array of files with gettext strings
	        }
	      }
	    }

	    // We combine all the arrays that we've got in an only array
	    $array_php = array_merge($array_php_module, $array_php_module_c, $files_php);

	    //Now save the php.po file with the result
	    foreach ($this->dir_lc as $l){
	      if ($this->checkTranslations($l)){ //merge
		      //Scan the php code to find the latest gettext entries
		      $entries_php = Gettext\Extractors\PhpCode::fromFile($array_php);

		      //Get the translations of the code that are stored in a po file
		      $oldEntries = Gettext\Extractors\Po::fromFile($l.'/'.$this->textdomain.'.po');

		      //Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
		      $entries_php->mergeWith($oldEntries); //now $entries has all the values
		    }
		    else{ //create
		      $entries_php = Gettext\Extractors\PhpCode::fromFile($array_php);
		    }
		    
	      //Gettext\Generators\Po::generateFile($entries_php, $l.'/'.$this->textdomain.'.po');
	    }

	    /********** END PHP **********/

        /******************************/
        /************* JS *************/
        /******************************/

        // get all the .js files unless files into modules folder
        $all_files_js = CacheUtilsController::listFolderFiles(COGUMELO_LOCATION, array('js'), false);
        foreach($all_files_js as $i => $dir){
          if (strpos($dir,'/coreModules/')===false && strpos($dir,'/modules/')===false
            && strpos($dir,'/vendor/')===false && strpos($dir,'/vendorServer/')===false
            && strpos($dir,'/pluggins/')===false && strpos($dir,'/jquery-validation/')===false
            && strpos($dir,'/tmp/')===false){ // We exclude files from modules, tmp and vendorServer
              $files_js[$i] = $dir->getRealPath();
          }
        }

        // get the .js files into modules folder
        $files_module_js = CacheUtilsController::listFolderFiles($this->dir_modules, array('js'), false);
        foreach ($this->modules as $i => $dir) {
          foreach ($files_module_js as $k => $file) {
            $parts = explode('/'.$dir.'/',$file);
            if (sizeof($parts)==2){
              $array_js_module[$k] = ModuleController::getRealFilePath($parts[1], $dir);// Array of files with gettext strings
            }
          }
        }

        // get the .js files into coreModules folder
        $files_module_js_c = CacheUtilsController::listFolderFiles($this->dir_modules_c, array('js'), false);
        foreach ($this->modules as $i => $dir) {
          foreach ($files_module_js_c as $k => $file) {
            $parts = explode('/'.$dir.'/',$file);
            if (sizeof($parts)==2){
              $array_js_module_c[$k] = ModuleController::getRealFilePath($parts[1], $dir);// Array of files with gettext strings
            }
          }
        }

        // We combine all the arrays that we've got in an only array
        $array_js = array_merge($array_js_module, $array_js_module_c, $files_js);

        //Now save the .po file with the result
        foreach ($this->dir_lc as $l){
      	  if ($this->checkTranslations($l)){ //merge

	        //Scan the php code to find the latest gettext entries
	        $entries_js = Gettext\Extractors\JsCode::fromFile($array_js);

	        //Get the translations of the code that are stored in a po file
	        $oldEntries = Gettext\Extractors\Po::fromFile($l.'/'.$this->textdomain.'.po');

	        //Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
	        $entries_js->mergeWith($oldEntries); //now $entries has all the values
	      }
	      else{ //create
	        $entries_js = Gettext\Extractors\JsCode::fromFile($array_js);
	      }

          //Gettext\Generators\Po::generateFile($entries, $l.'/'.$this->textdomain.'.po');
          $entries_js->mergeWith($entries_php);

          Gettext\Generators\Po::toFile($entries_js, $l.'/'.$this->textdomain.'.po');
        }

        /*********** END JS **********/ 	    

        /******************************/
    	/************* TPL *************/
	    /******************************/

	    $smartygettext = DEPEN_COMPOSER_PATH.'/smarty-gettext/smarty-gettext/tsmarty2c.php';
	    exec( 'chmod 700 '.$smartygettext );

	    // We will use the smarty-gettext pluggin to extract the strings to translate from .tpl files
	    
	    // get all the .tpl files unless files into modules folder
	    $all_files_tpl = CacheUtilsController::listFolderFiles(COGUMELO_LOCATION, array('tpl'), false);
	    foreach($all_files_tpl as $i => $dir){
	      if (strpos($dir,'/coreModules/')===false && strpos($dir,'/modules/')===false
	          && strpos($dir,'/vendor/')===false && strpos($dir,'/vendorServer/')===false
	          && strpos($dir,'/pluggins/')===false && strpos($dir,'/jquery-validation/')===false
	          && strpos($dir,'/tmp/')===false){ // We exclude files from modules, tmp and vendorServer
	            $files_tpl[$i] = $dir->getRealPath();
	      }
	    }

	    // get the .tpl files into modules folder
	    $files_module_tpl = CacheUtilsController::listFolderFiles($this->dir_modules, array('tpl'), false);
	    foreach ($this->modules as $i => $dir) {
	      foreach ($files_module_tpl as $k => $file) {
	        $parts = explode('/'.$dir.'/',$file);
	        if (sizeof($parts)==2){
	          $array_tpl_module[$k] = ModuleController::getRealFilePath($parts[1], $dir);// Array of files with gettext strings
	        }
	      }
	    }

	    // get the .tpl files into coreModules folder
	    $files_module_tpl_c = CacheUtilsController::listFolderFiles($this->dir_modules_c, array('tpl'), false);
	    foreach ($this->modules as $i => $dir) {
	      foreach ($files_module_tpl_c as $k => $file) {
	        $parts = explode('/'.$dir.'/',$file);
	        if (sizeof($parts)==2){
	          $array_tpl_module_c[$k] = ModuleController::getRealFilePath($parts[1], $dir);// Array of files with gettext strings
	        }
	      }
	    }

	    // We combine all the arrays that we've got in an only array
	    $array_tpl = array_merge($array_tpl_module, $array_tpl_module_c, $files_tpl);

	    //temos o listado de todos os arquivos no array_tpl, falta saber cómo executalo
	    // copiamos os ficheiros nun dir temporal
	    foreach ($array_tpl as $a){
	      exec('cp '.$a.' '.TPL_TMP);
	    }


	    foreach ($this->dir_lc as $l){
	      exec($smartygettext.' -o '.$l.'/'.$this->textdomain.'_tpl.po '.TPL_TMP);
	      // Now we have to combine this PO file with the PO file we had previusly and discard the tmp file
	      exec('msgmerge '.$l.'/'.$this->textdomain.'.po '.$l.'/'.$this->textdomain.'_tpl.po');
	      exec ('rm '.$l.'/'.$this->textdomain.'_tpl.po');

	      error_reporting(E_ALL);
	    }



	    /*********** END TPL **********/

		/*if($_SERVER["REMOTE_ADDR"] == "127.0.0.1")
		{
			shell_exec('xgettext --from-code=UTF-8 -o '.SITE_PATH.'conf/i18n/en/LC_MESSAGES/messages.po '.SITE_PATH.'../*.php');
			print('xgettext --from-code=UTF-8 -o '.SITE_PATH.'conf/i18n/en/LC_MESSAGES/messages.po '.SITE_PATH.'../*.php');
			// Buscamos todos os textos en ficheiros .php
			//exec('find '.COGUMELO_LOCATION.'/. ../app/. -iname "*.php" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o ../app/i18n/c_project.pot -L PHP');
			foreach(explode(',', 'gl,es,en') as $lng) {
				shell_exec('xgettext --from-code=UTF-8 -o /home/proxectos/cogumelo/c_packages/sampleApp/httpdocs/../app/conf/i18n/en/LC_MESSAGES/messages_'.$lng.'.po /home/proxectos/cogumelo/c_packages/sampleApp/httpdocs/../app/../*.php');
			}
	
		}*/
	}

	/**
  	* Compile files.po to get the translations ready to be used
  	*/
	function c_i18n_compile() {

		foreach ($this->dir_lc as $l){
			echo
	      exec('msgfmt -c -v -o '.$l.'/'.$this->textdomain.'.mo '.$l.'/'.$this->textdomain.'.po');
	    }
	}

	/**
  	* Translate files.po into .json to be used in client
  	*/
	function c_i18n_json() {
		foreach ($this->lc_1 as $l){
			$myarray = explode('_',$l);
	    	$lang = $myarray[0];
			exec('i18next-conv -l '.$this->textdomain.' -s '.I18N_LOCALE.$l.'/LC_MESSAGES/'.$this->textdomain.'.po -t '.COGUMELO_LOCATION.'/packages/sampleApp/httpdocs/locales/'.$lang.'/translation.json');
	    }
	}

	/* Non se está usando */
	function c_i18n_update() {
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
	
	
}