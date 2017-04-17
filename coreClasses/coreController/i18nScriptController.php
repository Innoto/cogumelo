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

  /**
    * Get all the text in the system (cogumelo + geozzy) to be translated and update
    */
  public function c_i18n_getSystemTranslations(){
    error_reporting(E_ALL ^ E_NOTICE);
    // cogumelo modules
    if ($dh = opendir($this->dir_modules_c)) {
      while (($module = readdir($dh)) !== false) {
        if (is_dir($this->dir_modules_c . $module) && $module!="." && $module!=".."){
          $this->getModulePo($this->dir_modules_c.$module);
        }
      }
      $this->getSystemPo('cogumelo');
    }
    //geozzy distModules
   if ($dh = opendir($this->dir_modules_dist)) {
      while (($module = readdir($dh)) !== false) {
        if (is_dir($this->dir_modules_dist . $module) && $module!="." && $module!=".."){
          $this->getModulePo($this->dir_modules_dist.$module);
        }
      }
      $this->getSystemPo('geozzy');
    }
    error_reporting(E_ALL);
  }

  /**
    * Get all the app translations and generate an only PO with system plus app translations
    */
  public function c_i18n_getAppTranslations(){

    $appFilesModule = CacheUtilsController::listFolderFiles($this->dir_modules, array('php','js','tpl'), false);
    $appFilesMain = CacheUtilsController::listFolderFiles($this->dir_main, array('php','js','tpl'), false);
    $appFiles = array_merge_recursive($appFilesModule, $appFilesMain);

    // get all the files unless files into app folder, excluding tmp and vendor folders
    if ($appFiles){
      foreach($appFiles as $i => $dir){
        if (strpos($dir,'/coreModules/')===false && strpos($dir,'/vendor/')===false && strpos($dir,'/vendorPackages/')===false
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

    /************************** PHP *******************************/
    if (array_key_exists('php', $filesAll)){
      $this->generateAppPo($filesAll['php'], 'php');
    }

    /************************** JS *******************************/
    if (array_key_exists('js', $filesAll)){
      $this->generateAppPo($filesAll['js'], 'js');
    }

    /**************************** TPL ********************************/
    if (array_key_exists('tpl', $filesAll)){
      $this->generateAppTplPo($filesAll['tpl']);
    }

    $this->updateAppPo();

  }

  /*
  * Check if already exists a translation file PO in system modules
  **/
  public function checkModuleTranslations( $module, $l, $type ) {
    $exist = false;
    $transFile = $this->textdomain.'_'.$l.'.po';
    switch($type){
      case 'js':
        $transFile = $this->textdomain.'_'.$l.'_js.po';
        break;
      case 'tpl':
        $transFile = $this->textdomain.'_'.$l.'_tpl.po';
        break;
    }
    if (is_dir($module)){
      $handle = opendir($module);
      while ($file = readdir($handle)) {
        if ($file==$transFile){
          $exist = true;
        }
      }
    }
    return $exist;
  }

  /*
  * Check if already exists a translation file PO in App
  **/
  public function checkAppTranslations( $l, $type ) {
    $exist = false;
    $transFile = $this->textdomain.'.po';
    switch($type){
      case 'js':
        $transFile = $this->textdomain.'_js.po';
        break;
      case 'tpl':
        $transFile = $this->textdomain.'_tpl.po';
        break;
    }
    if (is_dir($l)){
      $handle = opendir($l);
      while ($file = readdir($handle)) {
        if ($file==$transFile){
          $exist = true;
        }
      }
    }
    return $exist;
  }

  /*
  * Generate translations file PO for a given module
  **/
  public function getModulePo($module){
    $path = $module;
    $files = CacheUtilsController::listFolderFiles($module, array('php','js','tpl'), false);
    $filesModule = array();
    foreach($files as $file){
      $parts = explode('.',$file);
      switch($parts[1]){
        case 'php':
          $filesModule['php'][] = $file->getRealPath();
          break;
        case 'js':
          $filesModule['js'][] = $file->getRealPath();
          break;
        case 'tpl':
          $filesModule['tpl'][] = $file->getRealPath();
          break;
      }
    }

    /************************** PHP *******************************/
    if (array_key_exists('php', $filesModule)){
      $this->generateModulePo($module, $filesModule['php'], 'php');
    }

    /************************** JS *******************************/
    if (array_key_exists('js', $filesModule)){
      $this->generateModulePo($module, $filesModule['js'], 'js');
    }

    /**************************** TPL ********************************/
    if (array_key_exists('tpl', $filesModule)){
      $this->generateModuleTplPo($module, $filesModule['tpl']);
    }
    // Now we have to combine each type PO's in one for each language
    $this->updateModulePo($module);
  }

  /*
  * Merge POs generated form each type of file in one and clean temp
  **/
  function updateModulePo($module){
    foreach( $this->lang as $l => $lang ) {
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_prev.po') && file_exists($module.'/'.$this->textdomain.'_'.$l.'_tpl.po')){
        exec ('msgcat '.$module.'/'.$this->textdomain.'_'.$l.'_prev.po '.$module.'/'.$this->textdomain.'_'.$l.'_tpl.po > '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po');
      }
      else{
        if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_prev.po')){
          exec ('msgcat '.$module.'/'.$this->textdomain.'_'.$l.'_prev.po > '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po');
        }
        else{
          if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_tpl.po')){
            exec ('msgcat '.$module.'/'.$this->textdomain.'_'.$l.'_tpl.po > '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po');
          }
        }
      }
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_js.po')){
        exec ('msgcat '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po '.$module.'/'.$this->textdomain.'_'.$l.'_js.po > '.$module.'/'.$this->textdomain.'_'.$l.'.po');
      }
      else{
        exec ('msgcat '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po > '.$module.'/'.$this->textdomain.'_'.$l.'.po');
      }

      // Delete all the tmp files created
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_tpl.po')){
        exec ('rm '.$module.'/'.$this->textdomain.'_'.$l.'_tpl.po');
      }
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_prev.po')){
        exec ('rm '.$module.'/'.$this->textdomain.'_'.$l.'_prev.po');
      }
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_js.po')){
        exec ('rm '.$module.'/'.$this->textdomain.'_'.$l.'_js.po');
      }
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_tmp.po')){
        exec ('rm '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po');
      }
      if(count(scandir(Cogumelo::getSetupValue( 'smarty:tmpPath' ))) > 2){
        exec ('rm '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).'/*.tpl');
      }
    }
  }

  /*
  * Merge POs generated form each type of file in one and clean temp
  **/
  function updateAppPo(){
    // Now we have to combine the po's we got in one for each language
    foreach( $this->dir_lc as $l ) {
      if(file_exists($l.'/'.$this->textdomain.'_prev.po') && file_exists($l.'/'.$this->textdomain.'_tpl.po')){
        exec ('msgcat '.$l.'/'.$this->textdomain.'_prev.po '.$l.'/'.$this->textdomain.'_tpl.po > '.$l.'/'.$this->textdomain.'_tmp.po');
      }
      else{
        if(file_exists($l.'/'.$this->textdomain.'_prev.po')){
          exec ('msgcat '.$l.'/'.$this->textdomain.'_prev.po > '.$l.'/'.$this->textdomain.'_tmp.po');
        }
        else{
          if(file_exists($l.'/'.$this->textdomain.'_tpl.po')){
            exec ('msgcat '.$l.'/'.$this->textdomain.'_tpl.po > '.$l.'/'.$this->textdomain.'_tmp.po');
          }
        }
      }
      if(file_exists($l.'/'.$this->textdomain.'_js.po')){
        exec ('msgcat '.$l.'/'.$this->textdomain.'_tmp.po '.$l.'/'.$this->textdomain.'_js.po > '.$l.'/'.$this->textdomain.'_app.po');
      }
      else{
        exec ('msgcat '.$l.'/'.$this->textdomain.'_tmp.po > '.$l.'/'.$this->textdomain.'_app.po');
      }

      //Delete all the tmp files created
      if(file_exists($l.'/'.$this->textdomain.'_tpl.po')){
        exec ('rm '.$l.'/'.$this->textdomain.'_tpl.po');
      }
      if(file_exists($l.'/'.$this->textdomain.'_prev.po')){
        exec ('rm '.$l.'/'.$this->textdomain.'_prev.po');
      }
      if(file_exists($l.'/'.$this->textdomain.'_js.po')){
        exec ('rm '.$l.'/'.$this->textdomain.'_js.po');
      }
      if(file_exists($l.'/'.$this->textdomain.'_tmp.po')){
        exec ('rm '.$l.'/'.$this->textdomain.'_tmp.po');
      }
      if(count(scandir(Cogumelo::getSetupValue( 'smarty:tmpPath' ))) > 2){
        exec ('rm '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).'/*.tpl');
      }

      // We merge cogumelo geozzy and app po to have only one final PO
      exec('msgcat '.$l.'/'.$this->textdomain.'_app.po '.$l.'/'.$this->textdomain.'_cogumelo.po '.$l.'/'.$this->textdomain.'_geozzy.po | grep -E -v \'^".+"$\' > '.$l.'/'.$this->textdomain.'.po');
    }
  }

  /*
  * Combine all modules PO's in only one for each system folder
  **/
  function getSystemPo($system){
    if($system == 'cogumelo'){
      $path = $this->dir_modules_c;
    }
    if($system == 'geozzy'){
      $path = $this->dir_modules_dist;
    }
    if ($dh = opendir($path)) {
      $all = array();
      foreach($this->dir_lc as $l => $lang){
        $all[$l] = '';
      }
      while (($module = readdir($dh)) !== false) {
        if (is_dir($path.$module)){
          if ($mod = opendir($path.$module)) {
            while ($file = readdir($mod) ){
              foreach( $this->dir_lc as $l => $lang ) {
                if ($file==$this->textdomain.'_'.$l.'.po' && filesize($path.$module.'/'.$file)!=0){
                  $all[$l] = $all[$l].' '.$path.$module.'/'.$file;
                }
              }
            }
          }
        }
      }
      foreach($this->dir_lc as $l=>$lang){
        exec('msgcat '.$all[$l].' > '.$path.$this->textdomain.'_'.$system.'_'.$l.'.po' );
        exec('cp '.$path.'/'.$this->textdomain.'_'.$system.'_'.$l.'.po '.$lang.'/'.$this->textdomain.'_'.$system.'.po');
      }
    }
  }

  /*
  * Extract strings from system to translate of a type (PHP, JS)and put them into an specific translations file PO
  **/
  function generateModulePo($module, $files, $type){
    foreach( $this->lang as $l => $lang) {
      switch($type){
        case 'php':
          $extractor = 'Gettext\Extractors\PhpCode';
          $oldFile = $module.'/'.$this->textdomain.'_'.$l.'.po';
          $newFile = $module.'/'.$this->textdomain.'_'.$l.'_prev.po';
          break;
        case 'js':
          $extractor = 'Gettext\Extractors\JsCode';
          $oldFile = $module.'/'.$this->textdomain.'_'.$l.'_js.po';
          $newFile = $module.'/'.$this->textdomain.'_'.$l.'_js.po';
      }
      if ($this->checkModuleTranslations($module, $l, $type)){ //merge
        //Scan the php code to find the latest gettext entries
        $entries = $extractor::fromFile($files);
        //Get the translations of the code that are stored in a po file
        $oldEntries = Gettext\Extractors\Po::fromFile($oldFile);
        //Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
        $entries->mergeWith($oldEntries); //now $entries has all the values
      }
      else{ //create
        $entries = $extractor::fromFile($files);
        $entries->mergeWith($entries);
      }
      Gettext\Generators\Po::toFile($entries, $newFile);
    }
  }

  /*
  * Extract strings to translate of TPL filesand put them into an specific translations file PO
  **/
  function generateModuleTplPo($module,$filesTpl){
    $smartygettext = Cogumelo::getSetupValue( 'dependences:composerPath' ).'/smarty-gettext/smarty-gettext/tsmarty2c.php';
    exec( 'chmod 700 '.$smartygettext );
    // copiamos os ficheiros nun dir temporal
    foreach ($filesTpl as $a){
      $a_parts = explode('/home/proxectos/',$a);
      $name = str_replace('/','_',$a_parts[1]);
      exec('cp '.$a.' '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).'/'.$name);
    }
    foreach( $this->lang as $l => $lang ) {
      exec($smartygettext.' -o '.$module.'/'.$this->textdomain.'_'.$l.'_tpl.po '.Cogumelo::getSetupValue( 'smarty:tmpPath' ));
    }
  }

  /*
  * Extract strings from App to translate of a type (PHP, JS)and put them into an specific translations file PO
  **/
  function generateAppPo($files, $type){
    foreach( $this->dir_lc as $l => $path ) {
      switch($type){
        case 'php':
          $extractor = 'Gettext\Extractors\PhpCode';
          $oldFile = $path.'/'.$this->textdomain.'.po';
          $newFile = $path.'/'.$this->textdomain.'_prev.po';
          break;
        case 'js':
          $extractor = 'Gettext\Extractors\JsCode';
          $oldFile = $path.'/'.$this->textdomain.'_js.po';
          $newFile = $path.'/'.$this->textdomain.'_js.po';
      }
      if ($this->checkAppTranslations($path, $type)){ //merge
        //Scan the php code to find the latest gettext entries
        $entries = $extractor::fromFile($files);
        //Get the translations of the code that are stored in a po file
        $oldEntries = Gettext\Extractors\Po::fromFile($oldFile);
        //Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
        $entries->mergeWith($oldEntries); //now $entries has all the values
      }
      else{ //create
        $entries = $extractor::fromFile($files);
        $entries->mergeWith($entries);
      }
      Gettext\Generators\Po::toFile($entries, $newFile);
    }
  }

  /*
  * Extract strings from App to translate of TPL type and put them into an specific translations file PO
  **/
  function generateAppTplPo($filesTpl){
    $smartygettext = Cogumelo::getSetupValue( 'dependences:composerPath' ).'/smarty-gettext/smarty-gettext/tsmarty2c.php';
    exec( 'chmod 700 '.$smartygettext );
    // copiamos os ficheiros nun dir temporal
    foreach ($filesTpl as $a){
      $a_parts = explode('/home/proxectos/',$a);
      $name = str_replace('/','_',$a_parts[1]);
      exec('cp '.$a.' '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).'/'.$name);
    }

    foreach( $this->dir_lc as $l ) {
      exec($smartygettext.' -o '.$l.'/'.$this->textdomain.'_tpl.po '.Cogumelo::getSetupValue( 'smarty:tmpPath' ));
    }
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

  // Remove cogumelo and geozzy po's (for testing)
  public function c_i18n_removeTranslations(){
    if ($dh = opendir($this->dir_modules_c)) {
      while (($module = readdir($dh)) !== false) {
        exec('rm '.$this->dir_modules_c.$module.'/*.po');
      }
    }
    if ($dh = opendir($this->dir_modules_dist)) {
      while (($module = readdir($dh)) !== false) {
        exec('rm '.$this->dir_modules_dist.$module.'/*.po');
      }
    }
  }

  /* Non se está usando */
  /*public function c_i18n_update() {
    if( Cogumelo::getSetupValue( 'i18n:gettextUpdate' ) !== true ) {
      return;
    }
    else if($_SERVER["REMOTE_ADDR"] == "127.0.0.1") {
      shell_exec('find '.COGUMELO_LOCATION.'/. ../app/. -iname "*.php" -o -iname "*.php" | xargs xgettext -kT_gettext -kT_ --from-code utf-8 -d c_project -o ../app/i18n/c_project.pot -L PHP');

      foreach( Cogumelo::getSetupValue( 'lang:available' ) as $lngKey => $values ) {
        shell_exec('msgmerge -U ../app/i18n/c_project_'.$lngKey.'.po ../app/i18n/c_project.pot');
      }

    }
  }*/

  /**
    * Get all the text to be translated and update or create a file.po if not exists
    */
  /*public function c_i18n_gettext_old(){

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
    // App modules (rTypes)
    $filesAppModules = $this->getModuleFiles($cogumeloFilesModule, $this->dir_modules);
    // Cogumelo core modules
    $filesCoreModules = $this->getModuleFiles($cogumeloFilesModuleC, $this->dir_modules_c);
    // Distribution modules
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


    // JS //

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


      // PHP e JS //

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

      // TPL //

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

  // Old function
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
                    $filesModule['php'][$k] = ModuleController::getRealFilePath($outMod[1], $dir);
                    break;
                  case 'js':
                    $filesModule['js'][$k] = ModuleController::getRealFilePath($outMod[1], $dir);
                    break;
                  case 'tpl':
                    $filesModule['tpl'][$k] = ModuleController::getRealFilePath($outMod[1], $dir);
                    break;
                }
              }
            }
          }
        }
    }
    return $filesModule;
  }
  */


}
