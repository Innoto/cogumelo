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
      //putenv('LC_ALL='.$locale);
      putenv('LC_MESSAGES='.$locale);

      setlocale(LC_ALL,$locale);
      setlocale(LC_CTYPE,$locale);

      bindtextdomain($this->textdomain, $this->dir_path);
      bind_textdomain_codeset($this->textdomain, 'UTF-8');
      textdomain($this->textdomain);
  }

  /**
    * Get all the text in the system (cogumelo + geozzy) to be translated and generate a file.po in each module
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
    }
    //geozzy distModules
   if ($dh = opendir($this->dir_modules_dist)) {
      while (($module = readdir($dh)) !== false) {
        if (is_dir($this->dir_modules_dist . $module) && $module!="." && $module!=".."){
          $this->getModulePo($this->dir_modules_dist.$module);
        }
      }
    }
    error_reporting(E_ALL);
  }

  /**
    * Get all the app translations and generate a file PO into the project
    */
  public function c_i18n_getAppTranslations(){

    //$appFilesModule = CacheUtilsController::listFolderFiles($this->dir_modules, array('php','js','tpl'), false);
    $appFilesMain = CacheUtilsController::listFolderFiles($this->dir_main, array('php','js','tpl'), false);

    // App modules po
    if ($dh = opendir($this->dir_modules)) {
      while (($module = readdir($dh)) !== false) {
        if (is_dir($this->dir_modules . $module) && $module!="." && $module!=".."){
          $this->getModulePo($this->dir_modules.$module);
        }
      }
    }

    // Project po
    if ($appFilesMain){
      foreach($appFilesMain as $i => $dir){
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
    $module = $module.'/translations';
    exec('chmod 700 '.$this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh');
    foreach( $this->lang as $l => $lang ) {
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_prev.po') && file_exists($module.'/'.$this->textdomain.'_'.$l.'_tpl.po')){
        exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po '.$module.'/'.$this->textdomain.'_'.$l.'_prev.po '.$module.'/'.$this->textdomain.'_'.$l.'_tpl.po');
      }
      else{
        if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_prev.po')){
          exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po '.$module.'/'.$this->textdomain.'_'.$l.'_prev.po');
        }
        else{
           if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_tpl.po')){
             exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po '.$module.'/'.$this->textdomain.'_'.$l.'_tpl.po');
           }
         }
      }
      if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_js.po')){
        if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_tmp.po')){
          exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$module.'/'.$this->textdomain.'_'.$l.'.po '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po '.$module.'/'.$this->textdomain.'_'.$l.'_js.po');
        }
      }
      else{
        if(file_exists($module.'/'.$this->textdomain.'_'.$l.'_tmp.po')){
          exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$module.'/'.$this->textdomain.'_'.$l.'.po '.$module.'/'.$this->textdomain.'_'.$l.'_tmp.po');
        }
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
        exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$l.'/'.$this->textdomain.'_tmp.po '.$l.'/'.$this->textdomain.'_tpl.po');
      }
      else{
        if(file_exists($l.'/'.$this->textdomain.'_prev.po')){
          exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$l.'/'.$this->textdomain.'_tmp.po '.$l.'/'.$this->textdomain.'_prev.po');
        }
        else{
          if(file_exists($l.'/'.$this->textdomain.'_tpl.po')){
            exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$l.'/'.$this->textdomain.'_tmp.po '.$l.'/'.$this->textdomain.'_tpl.po');
          }
        }
      }
      if(file_exists($l.'/'.$this->textdomain.'_js.po')){
        exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$l.'/'.$this->textdomain.'_app.po '.$l.'/'.$this->textdomain.'_tmp.po '.$l.'/'.$this->textdomain.'_js.po');
      }
      else{
        exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$l.'/'.$this->textdomain.'_app.po '.$l.'/'.$this->textdomain.'_tmp.po');
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
        $module = $module.'/translations';
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
      exec('chmod 700 '.$this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh');
      foreach($this->dir_lc as $l=>$lang){
        exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$path.$this->textdomain.'_'.$system.'_'.$l.'.po '.$all[$l]);
        exec('cp '.$path.'/'.$this->textdomain.'_'.$system.'_'.$l.'.po '.$lang.'/'.$this->textdomain.'_'.$system.'.po');
        exec('rm '.$path.'/'.$this->textdomain.'_'.$system.'_'.$l.'.po');
      }
    }
  }

  /*
  * Extract strings from system to translate of a type (PHP, JS)and put them into an specific translations file PO
  **/
  function generateModulePo($module, $files, $type){
    if(is_dir($module.'/translations')){
      $module = $module.'/translations';
    }
    else{
      exec('mkdir '.$module.'/translations');
      $module = $module.'/translations';
    }
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
    if(is_dir($module.'/translations')){
      $module = $module.'/translations';
    }
    else{
      exec('mkdir '.$module.'/translations');
      $module = $module.'/translations';
    }
    $smartygettext = Cogumelo::getSetupValue( 'dependences:manualPath' ).'/smarty-gettext/tsmarty2c.php';
    exec( 'chmod 700 '.$smartygettext );
    // copiamos os ficheiros nun dir temporal
    foreach ($filesTpl as $a){
      $a_parts = explode(APP_BASE_PATH,$a);
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
    $smartygettext = Cogumelo::getSetupValue( 'dependences:manualPath' ).'/smarty-gettext/tsmarty2c.php';
    exec( 'chmod 700 '.$smartygettext );
    // copiamos os ficheiros nun dir temporal
    foreach ($filesTpl as $a){
      $a_parts = explode(APP_BASE_PATH,$a);
      $name = str_replace('/','_',$a_parts[1]);
      exec('cp '.$a.' '.Cogumelo::getSetupValue( 'smarty:tmpPath' ).'/'.$name);
    }

    foreach( $this->dir_lc as $l ) {
      exec($smartygettext.' -o '.$l.'/'.$this->textdomain.'_tpl.po '.Cogumelo::getSetupValue( 'smarty:tmpPath' ));
    }
  }

  /**
    * Mix system and app POS in one and compile it: translations will be ready for use now    */
  public function c_i18n_compile() {
    exec('chmod 700 '.$this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh');
    foreach ($this->dir_lc as $l){
      $this->getSystemPo('cogumelo');
      $this->getSystemPo('geozzy');
      // We merge cogumelo geozzy and app po to have only one final PO
      exec($this->dir_modules_c.'i18nGetLang/classes/cgml-msgcat.sh '.$l.'/'.$this->textdomain.'.po '.$l.'/'.$this->textdomain.'_app.po '.$l.'/'.$this->textdomain.'_cogumelo.po '.$l.'/'.$this->textdomain.'_geozzy.po');
      // We compile the resultant PO and generate a json for client side
      echo exec('msgfmt -c -v -o '.$l.'/'.$this->textdomain.'.mo '.$l.'/'.$this->textdomain.'.po');
      exec('php '.$this->dir_modules_c.'/i18nServer/classes/po2json.php -i '.$l.'/'.$this->textdomain.'.po -o '.$l.'/translation.json');
      //exec('rm '.$l.'/'.$this->textdomain.'.po');
    }
  }

  /**
    * Translate files.po into .json to be used in client: not in use today
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
        if($this->dir_modules_c.$module!='.' && $this->dir_modules_c.$module!='..'){
          exec('rm '.$this->dir_modules_c.$module.'/translations/*.po');
        }
      }
    }
    if ($dh = opendir($this->dir_modules_dist)) {
      while (($module = readdir($dh)) !== false) {
        if($this->dir_modules_dist.$module!='.' && $this->dir_modules_c.$module!='..'){
          exec('rm '.$this->dir_modules_dist.$module.'/translations/*.po');
        }
      }
    }
    foreach( $this->dir_lc as $l ) {
      exec('rm '.$l.'/*.po');
    }
  }
}
