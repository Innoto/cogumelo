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
	    $this->dir_modules_dist = COGUMELO_DIST_LOCATION;
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

		$filesAll = $filesModules = $filesArray = array();

		$cogumeloFiles = CacheUtilsController::listFolderFiles(COGUMELO_LOCATION, array('php','js','tpl'), false);
		$cogumeloFilesModule = CacheUtilsController::listFolderFiles(COGUMELO_LOCATION, array('php','js','tpl'), false);
		$cogumeloFilesModuleC = CacheUtilsController::listFolderFiles($this->dir_modules_c, array('php','js','tpl'), false);
		$appFiles = CacheUtilsController::listFolderFiles(COGUMELO_DIST_LOCATION, array('php','js','tpl'), false);

		$files = array_merge($cogumeloFiles, $appFiles);
		$filesMod = array_merge($cogumeloFilesModule, $cogumeloFilesModuleC);

		// get all the files unless files into modules folder, excluding tmp and vendor folders
		if ($files){
		    foreach($files as $i => $dir){
		      	if (strpos($dir,'/coreModules/')===false && strpos($dir,'/modules/')===false 
		      	&& strpos($dir,'/vendor/')===false && strpos($dir,'/vendorPackages/')===false
		        && strpos($dir,'/vendorServer/')===false && strpos($dir,'/tmp/')===false){ 
		      		$parts = explode('.',$dir->getRealPath());
				    switch($parts[1]){
				      case 'php':
				        $filesAll['php'][$i] = $dir->getRealPath();
				        break;
				      case 'js':
				        $filesAll['js'][$i] = $dir->getRealPath();
				        break;	
				      case 'tpl':
				        $filesAll['tpl'][$i] = $dir->getRealPath();
				        break;	
				    }
				}
		    }
		}

		// get the .php files into modules folder
	    if ($filesMod && $this->modules){
			foreach ($this->modules as $i => $dir) {
			    foreach ($filesMod as $k => $file) {
				    $outMod = explode('/'.$dir.'/',$file);
				    if (sizeof($outMod)==2){
				    	if(ModuleController::getRealFilePath($outMod[1], $dir)){
  		        			$parts = explode('.',$file);
  		        			switch($parts[1]){
						      case 'php':
						        $filesModules['php'][$i] = ModuleController::getRealFilePath($outMod[1], $dir);
						        break;
						      case 'js':
						        $filesModules['js'][$i] = ModuleController::getRealFilePath($outMod[1], $dir);
						        break;	
						      case 'tpl':
						        $filesModules['tpl'][$i] = ModuleController::getRealFilePath($outMod[1], $dir);
						        break;	
						    }
				    	}
				    } 
				}
			}
		}

	    // We combine all the arrays that we've got in an only array
	    $filesArray = array_merge_recursive($filesAll, $filesModules);

	    /************************** PHP e JS *******************************/

	    //Now save the php.po file with the result
	    foreach ($this->dir_lc as $l){
	      if ($this->checkTranslations($l)){ //merge
		      //Scan the php code to find the latest gettext entries
		      $entriesPhp = Gettext\Extractors\PhpCode::fromFile($filesArray['php']);
		      $entriesJs = Gettext\Extractors\JsCode::fromFile($filesArray['js']);

		      $entriesJs->mergeWith($entriesPhp);

		      //Get the translations of the code that are stored in a po file
		      $oldEntries = Gettext\Extractors\Po::fromFile($l.'/'.$this->textdomain.'.po');

		      //Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
		      $entriesJs->mergeWith($oldEntries); //now $entries has all the values
		    }
		    else{ //create
		      $entriesPhp = Gettext\Extractors\PhpCode::fromFile($filesArray['php']);
		      $entriesJs = Gettext\Extractors\JsCode::fromFile($filesArray['js']);
		      $entriesJs->mergeWith($entriesPhp);
		    }
		  Gettext\Generators\Po::toFile($entriesJs, $l.'/'.$this->textdomain.'_prev.po');
	    }

	    /************************** TPL *******************************/

	    $smartygettext = DEPEN_COMPOSER_PATH.'/smarty-gettext/smarty-gettext/tsmarty2c.php';
	    exec( 'chmod 700 '.$smartygettext );

	    // copiamos os ficheiros nun dir temporal
	    foreach ($filesArray['tpl'] as $a){
	      exec('cp '.$a.' '.TPL_TMP);
	    }

	    foreach ($this->dir_lc as $l){
	      exec($smartygettext.' -o '.$l.'/'.$this->textdomain.'_tpl.po '.TPL_TMP);
	      // Now we have to combine this PO file with the PO file we had previusly and discard the tmp file
	      
	      exec ('msgcat --use-first '.$l.'/'.$this->textdomain.'_prev.po '.$l.'/'.$this->textdomain.'_tpl.po > '.$l.'/'.$this->textdomain.'.po'); 
	      exec ('rm '.$l.'/'.$this->textdomain.'_tpl.po');
	      exec ('rm '.$l.'/'.$this->textdomain.'_prev.po');
	    }

	    exec ('rm '.TPL_TMP.'/*.tpl');
		error_reporting(E_ALL);
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