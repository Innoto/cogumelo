<?php

/**
 * i18nScriptController Class
 */
require_once( Cogumelo::getSetupValue( 'dependences:manualPath' ).'/Gettext/src/autoloader.php' );
require_once( Cogumelo::getSetupValue( 'dependences:manualPath' ).'/Gettext/src/Translator.php' );

class i18nScriptController {

  var $dir_path;
  var $dir_modules_c;
  var $dir_modules;
  var $textdomain;
  var $modules;
  var $lc_1;
  var $dir_lc = array();
  var $lang = array();

  public function __construct() {

    global $C_ENABLED_MODULES;

    $this->dir_path = cogumelo::getSetupValue( 'i18n:localePath' );
    $this->dir_modules_c = COGUMELO_LOCATION.'/coreModules/';
    $this->dir_modules = APP_BASE_PATH.'/modules/';
    $this->dir_main = APP_BASE_PATH.'/classes/view/';
    $this->dir_modules_dist = COGUMELO_DIST_LOCATION.'/distModules/';
    $this->textdomain = 'messages';
    $this->modules = $C_ENABLED_MODULES;
    $this->lang = Cogumelo::getSetupValue( 'lang:available' );

    foreach( $this->lang as $l => $lang ) {
      $this->lang[$l] = $lang['i18n'];
      $this->dir_lc[$l] = $this->dir_path.'/'.$lang['i18n'].'/LC_MESSAGES';
    }
  }

  /**
    * Prepare the enviroment to localize the project
    */
  public function setEnviroment() {

      $locale= $this->lang[ Cogumelo::getSetupValue( 'lang:default' ) ];

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

  public function checkTranslations( $l ) {
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

  public function checkJsTranslations( $l ) {
    $exist = false;
    if (is_dir($l)){
      $handle = opendir($l);
      while ($file = readdir($handle)) {
        if ($file==$this->textdomain.'_js.po'){
          $exist = true;
        }
      }
    }

    return $exist;
  }

  /**
    * Get all the text to be translated and update or create a file.po if not exists
    */
  public function c_i18n_gettext(){

    error_reporting(E_ALL ^ E_NOTICE);

    $filesAll = $filesModules = $filesArray = array();

    $cogumeloFiles = CacheUtilsController::listFolderFiles(COGUMELO_LOCATION, array('php','js','tpl'), false);
    $cogumeloFilesModule = CacheUtilsController::listFolderFiles($this->dir_modules, array('php','js','tpl'), false);
    $cogumeloFilesModuleC = CacheUtilsController::listFolderFiles($this->dir_modules_c, array('php','js','tpl'), false);

    $appFilesModule = CacheUtilsController::listFolderFiles($this->dir_modules_dist, array('php','js','tpl'), false);
    $appFilesMain = CacheUtilsController::listFolderFiles($this->dir_main, array('php','js','tpl'), false);
    $appFiles = array_merge_recursive($appFilesModule, $appFilesMain);

    // get all the files unless files into modules folder, excluding tmp and vendor folders
    if ($appFiles){
        foreach($appFiles as $i => $dir){
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



    // get all the files into modules folder
    /* App modules (rTypes) */
    $filesAppModules = $this->getModuleFiles($cogumeloFilesModule, $this->dir_modules);
    /* Cogumelo core modules */
    $filesCoreModules = $this->getModuleFiles($cogumeloFilesModuleC, $this->dir_modules_c);
    /* Distribution modules */
    $filesDistModules = $this->getModuleFiles($appFiles, $this->dir_modules_dist);

    // We combine all the arrays that we've got in an only array
    $filesModules = array_merge_recursive($filesAppModules, $filesCoreModules, $filesDistModules);


    // get the .php files into modules folder
    if ($filesModules && $this->modules){
      foreach ($this->modules as $i => $dir) {
        foreach ($filesModules as $filesModule) {
          foreach ($filesModule as $k => $file) {
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
    }

    // We combine all the arrays that we've got in an only array
    $filesArray = array_merge_recursive($filesAll, $filesModules);


    /************************** JS *******************************/

    //Now save the php.po file with the result
    foreach ($this->dir_lc as $l){
      if ($this->checkJsTranslations($l)){ //merge
        //Scan the js code to find the latest gettext entries
        $entriesJs = Gettext\Extractors\JsCode::fromFile($filesArray['js']);
        //Get the translations of the code that are stored in a po file
        $oldEntries = Gettext\Extractors\Po::fromFile($l.'/'.$this->textdomain.'_js.po');
        //Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
        $entriesJs->mergeWith($oldEntries); //now $entries has all the values
      }
      else{ //create
        $entriesJs = Gettext\Extractors\JsCode::fromFile($filesArray['js']);
      }
    Gettext\Generators\Po::toFile($entriesJs, $l.'/'.$this->textdomain.'_prev_js.po');
    }


      /************************** PHP e JS *******************************/

      //Now save the php.po file with the result
      foreach ($this->dir_lc as $l){
        if ($this->checkTranslations($l)){ //merge
          //Scan the php code to find the latest gettext entries
          $entriesPhp = Gettext\Extractors\PhpCode::fromFile($filesArray['php']);

          //Get the translations of the code that are stored in a po file
          $oldEntries = Gettext\Extractors\Po::fromFile($l.'/'.$this->textdomain.'.po');

          //Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
          $entriesPhp->mergeWith($oldEntries); //now $entries has all the values
        }
        else{ //create
          $entriesPhp = Gettext\Extractors\PhpCode::fromFile($filesArray['php']);
          $entriesPhp->mergeWith($entriesPhp);
        }
        Gettext\Generators\Po::toFile($entriesPhp, $l.'/'.$this->textdomain.'_prev.po');
      }

      /**************************** TPL ********************************/

      $smartygettext = Cogumelo::getSetupValue( 'dependences:composerPath' ).'/smarty-gettext/smarty-gettext/tsmarty2c.php';
      exec( 'chmod 700 '.$smartygettext );

      // copiamos os ficheiros nun dir temporal
      foreach ($filesArray['tpl'] as $a){
        $a_parts = explode('/home/proxectos/',$a);
        $name = str_replace('/','_',$a_parts[1]);
        exec('cp '.$a.' '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).'/'.$name);
      }

      foreach ($this->dir_lc as $l){
        exec($smartygettext.' -o '.$l.'/'.$this->textdomain.'_tpl.po '.Cogumelo::getSetupValue( 'smarty:tmpPath' ));
        // Now we have to combine this PO file with the PO file we had previusly and discard the tmp file

        exec ('msgcat --use-first '.$l.'/'.$this->textdomain.'_prev.po '.$l.'/'.$this->textdomain.'_tpl.po > '.$l.'/'.$this->textdomain.'.po');
        exec ('msgcat --use-first '.$l.'/'.$this->textdomain.'_prev_js.po > '.$l.'/'.$this->textdomain.'_js.po');
        exec ('rm '.$l.'/'.$this->textdomain.'_tpl.po');
        exec ('rm '.$l.'/'.$this->textdomain.'_prev.po');
        exec ('rm '.$l.'/'.$this->textdomain.'_prev_js.po');
      }

      exec ('rm '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).'/*.tpl');
    error_reporting(E_ALL);
  }

  /**
    * Compile files.po to get the translations ready to be used
    */
  public function c_i18n_compile() {

    foreach ($this->dir_lc as $l){
      echo exec('msgfmt -c -v -o '.$l.'/'.$this->textdomain.'.mo '.$l.'/'.$this->textdomain.'.po');
    }
  }

  /**
    * Translate files.po into .json to be used in client
    */
  public function c_i18n_json() {
    foreach ($this->dir_lc as $l){
      exec('php '.$this->dir_modules_c.'/i18nServer/classes/po2json.php -i '.$l.'/'.$this->textdomain.'_js.po -o '.$l.'/translation.json');
      //exec('i18next-conv -l '.$this->textdomain.' -s '.$l.'/'.$this->textdomain.'_js.po -t '.$l.'/translation.json');
    }
  }

  /* Non se está usando */
  public function c_i18n_update() {
    if( Cogumelo::getSetupValue( 'i18n:gettextUpdate' ) !== true ) {
      return;
    }
    else if($_SERVER["REMOTE_ADDR"] == "127.0.0.1") {
      shell_exec('find '.COGUMELO_LOCATION.'/. ../app/. -iname "*.php" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o ../app/i18n/c_project.pot -L PHP');

      foreach( Cogumelo::getSetupValue( 'lang:available' ) as $lngKey => $values ) {
        shell_exec('msgmerge -U ../app/i18n/c_project_'.$lngKey.'.po ../app/i18n/c_project.pot');
      }

    }
  }

  public function getModuleFiles($modFiles, $relPath){
    $filesModule = array();
    if ($modFiles && $this->modules){
      foreach ($this->modules as $i => $dir) {
          foreach ($modFiles as $k => $file) {
            $outMod = explode($relPath.$dir.'/',$file);
            if (sizeof($outMod)==2){
              if(ModuleController::getRealFilePath($outMod[1], $dir)){
                    $parts = explode('.',$file);
                    switch($parts[1]){
                  case 'php':
                    $filesModule['php'][$i] = ModuleController::getRealFilePath($outMod[1], $dir);
                    break;
                  case 'js':
                    $filesModule['js'][$i] = ModuleController::getRealFilePath($outMod[1], $dir);
                    break;
                  case 'tpl':
                    $filesModule['tpl'][$i] = ModuleController::getRealFilePath($outMod[1], $dir);
                    break;
                }
              }
            }
          }
        }
    }
    return $filesModule;
  }


}
