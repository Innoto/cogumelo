<?php

Cogumelo::vendorLoad('jdorn/sql-formatter/lib/SqlFormatter.php');
Cogumelo::vendorLoad('raveren/kint/Kint.class.php');
Cogumelo::load('c_view/View');
devel::load('controller/LogReaderController');
devel::load('controller/DevelDBController');
devel::load('controller/UrlListController');


class DevelView extends View
{

  function __construct($base_dir) {
    parent::__construct($base_dir);
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {
/*
    global $DEVEL_ALLOWED_HOSTS;
    if( !in_array($_SERVER["REMOTE_ADDR"], $DEVEL_ALLOWED_HOSTS) ){
*/
    if( !MOD_DEVEL_ALLOW_ACCESS ) {
      Cogumelo::error("Must be developer to enter on this site");
      RequestController::redirect(SITE_URL_CURRENT.'');
    }
    else {
      if ( !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!= DEVEL_PASSWORD ) {
        header('WWW-Authenticate: Basic realm="Cogumelo Devel Confirm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Acceso Denegado.';
        exit;
      }
      else {
        return true;
      }
    }
  }

  function main($url_path=''){
    $this->template->setTpl('develpage.tpl', 'devel');
    $this->template->addJs('js/devel.js', 'devel');
    $this->template->addCss('css/devel.css', 'devel');
    $this->logs();
    $this->infoSetup();
    $this->DBSQL();
    $this->infoUrls();

    $this->template->exec();
  }


  //
  // actions Logs
  //
  function logs( ){
    $list_file_logs_path = glob(SITE_PATH."log/*.log");
    $list_file_logs = str_replace(SITE_PATH."log/", "", $list_file_logs_path);
    $list_file_logs = str_replace(".log", "", $list_file_logs);
    $this->template->assign("list_file_logs" , $list_file_logs);
  }

  function read_logs(){ //LLamada a Ajax para buscar mas lineas
    $readerlogcontrol = new LogReaderController();
    $content_logs = $readerlogcontrol->read_logs();
    header("Content-Type: application/json"); //return only JSON data
    echo json_encode($content_logs);
  }

  function DBSQL(){
    $data_sql = $this->get_sql_tables();
    foreach ($data_sql as $k => $v) {
      $data_sql[$k] = SqlFormatter::format($v);
    }
    $this->template->assign("data_sql" , $data_sql);
  }

  function infoSetup(){
    global $C_ENABLED_MODULES;
    if(IS_DEVEL_ENV){
      $this->template->assign("infoIsDevelEnv" , 'True');
    }
    else{
      $this->template->assign("infoIsDevelEnv" , 'False');
    }
    $this->template->assign("infoCogumeloLocation" , COGUMELO_LOCATION);
    $this->template->assign("infoDBEngine" , DB_ENGINE);
    $this->template->assign("infoDBHostName" , DB_HOSTNAME);
    $this->template->assign("infoDBPort" , DB_PORT);
    $this->template->assign("infoDBUser" , DB_USER);
    $this->template->assign("infoDBName" , DB_NAME);
    
    if(DB_ALLOW_CACHE){
      $this->template->assign("infoDBAllowCache" , 'True');
    }
    else{
      $this->template->assign("infoDBAllowCache" , 'False');
    }
    $this->template->assign("infoSiteProtocol" , SITE_PROTOCOL);
    $this->template->assign("infoSiteHost" , SITE_HOST);
    $this->template->assign("infoSiteFolder" , SITE_FOLDER);
    $this->template->assign("infoSiteUrl" , SITE_URL);
    $this->template->assign("infoSiteUrlHttp" , SITE_URL_HTTP);
    $this->template->assign("infoSiteUrlHttps" , SITE_URL_HTTPS);
    $this->template->assign("infoSiteUrlCurrent" , SITE_URL_CURRENT);
    $this->template->assign("infoSmtpHost" , SMTP_HOST);
    $this->template->assign("infoSmtpPort" , SMTP_PORT);
    $this->template->assign("infoSmtpAuth" , SMTP_AUTH);
    $this->template->assign("infoSmtpUser" , SMTP_USER);
    $this->template->assign("infoSysMailFromName" , SYS_MAIL_FROM_NAME);
    $this->template->assign("infoSysMailFromEmail" , SYS_MAIL_FROM_EMAIL);
    $this->template->assign("infoSmartyConfig" , SMARTY_CONFIG);
    $this->template->assign("infoSmartyCompile" , SMARTY_COMPILE);
    $this->template->assign("infoSmartyCache" , SMARTY_CACHE);
    $this->template->assign("infoMediaServerHost" , MEDIASERVER_HOST);
    $this->template->assign("infoMediaServerTmpCachePath" , MEDIASERVER_TMP_CACHE_PATH);
    $this->template->assign("infoMediaServerFinalCachePath" , MEDIASERVER_FINAL_CACHE_PATH);
    
    if(MEDIASERVER_COMPILE_LESS){
      $this->template->assign("infoMediaServerCompileLess" , 'True');
    }
    else{
      $this->template->assign("infoMediaServerCompileLess" , 'False');
    }
    
    $stringEnabledModules = "";
    foreach( $C_ENABLED_MODULES as $em){
      $stringEnabledModules = $stringEnabledModules." ".$em." "; 
    }
    $this->template->assign("infoCEnabledModules" , $stringEnabledModules);
    $this->template->assign("infoBck" , BCK);
    $this->template->assign("infoLogDir" , LOGDIR);
    
    if(LOG_RAW_SQL){
      $this->template->assign("infoLogRawSql" , 'True');
    }
    else{
      $this->template->assign("infoLogRawSql" , 'False');
    }
    if(DEBUG){
      $this->template->assign("infoDebug" , 'True');
    }
    else{
      $this->template->assign("infoDebug" , 'False');
    }
    
    if(ERRORS){
      $this->template->assign("infoErrors" , 'True');
    }
    else{
      $this->template->assign("infoErrors" , 'False');
    }    
    
    if(MOD_DEVEL_ALLOW_ACCESS){
      $this->template->assign("infoModDevelAllowAccess" , 'True');
    }
    else{
      $this->template->assign("infoModDevelAllowAccess" , 'False');
    }    
    if(GETTEXT_UPDATE){
      $this->template->assign("infoGetTextUpdate" , 'True');
    }
    else{
      $this->template->assign("infoGetTextUpdate" , 'False');
    } 
    
    $this->template->assign("infoLangDefault" , LANG_DEFAULT);
    $this->template->assign("infoLangAvailable" , LANG_AVAILABLE);
    
  }

  function infoUrls(){
    $regexlist = new UrlListController();
    $this->template->assign("dataUrls",  $regexlist->listUrls());
  }


  //
  // Actions
  //

  function get_sql_tables(){
    $fvotdbcontrol = new DevelDBController();
    return ($fvotdbcontrol->getTablesSQL() );
  }

  function get_debugger(){
    $temp_debugs = Cogumelo::objDebugPull();
    $result_debugs = array();
    header("Content-Type: application/json"); //return only JSON data
    if(isset($temp_debugs)){
      foreach ($temp_debugs as $val_debug){
        if($val_debug['creation_date']['minutes'] < 10){
          $val_debug['creation_date']['minutes'] = "0".$val_debug['creation_date']['minutes'];
        }
        if($val_debug['creation_date']['seconds'] < 10){
          $val_debug['creation_date']['seconds'] = "0".$val_debug['creation_date']['seconds'];
        }
        $temp_date = $val_debug['creation_date']['hours'].":".$val_debug['creation_date']['minutes'].":".$val_debug['creation_date']['seconds'];
        array_push( $result_debugs, array(
          'comment' => $val_debug['comment'],
          'date' => $temp_date,
          'debuging' => @Kint::dump( $val_debug['data'] )
        ));
      }
      echo json_encode($result_debugs);
    }
  }

}



