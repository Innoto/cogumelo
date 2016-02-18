<?php

Cogumelo::load('coreView/View.php');
devel::autoIncludes();


class DevelView extends View
{

  function __construct($base_dir) {
    parent::__construct($base_dir);

  }

  /**
  * Evaluate the access conditions and report if can continue
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
      if ( !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!= MOD_DEVEL_PASSWORD ) {
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

    // ER diagram data
    Cogumelo::load('coreModel/VOUtils.php');
    $this->template->assign('erData', json_encode(VOUtils::getAllRelScheme()) );

    // SQL code
    $data_sql = $this->get_sql_tables();
    foreach ($data_sql as $k => $v) {
      $data_sql[$k] = SqlFormatter::format($v);
    }
    $this->template->assign("data_sql" , $data_sql);
  }

  function infoSetup(){
    global $C_ENABLED_MODULES;

    $this->template->assign("infoCogumeloLocation" , COGUMELO_LOCATION);
    $this->template->assign("infoDBEngine" , cogumeloGetSetupValue( 'db:engine' ));
    $this->template->assign("infoDBHostName" , cogumeloGetSetupValue( 'db:hostname' ));
    $this->template->assign("infoDBPort" , cogumeloGetSetupValue( 'db:port' ));
    $this->template->assign("infoDBUser" , cogumeloGetSetupValue( 'db:user' ));
    $this->template->assign("infoDBName" , cogumeloGetSetupValue( 'db:name' ));

    if(cogumeloGetSetupValue( 'db:allowCache' )){
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
    $this->template->assign("infoSmtpHost" , cogumeloGetSetupValue( 'smtp:host' ));
    $this->template->assign("infoSmtpPort" , cogumeloGetSetupValue( 'smtp:port' ));
    $this->template->assign("infoSmtpAuth" , cogumeloGetSetupValue( 'smtp:auth' ));
    $this->template->assign("infoSmtpUser" , cogumeloGetSetupValue( 'smtp:user' ));
    $this->template->assign("infoSysMailFromName" , cogumeloGetSetupValue( 'smtp:fromName' ));
    $this->template->assign("infoSysMailFromEmail" , cogumeloGetSetupValue( 'smtp:fromEmail' ));
    $this->template->assign("infoSmartyConfig" , cogumeloGetSetupValue( 'smarty:configPath' ));
    $this->template->assign("infoSmartyCompile" , cogumeloGetSetupValue( 'smarty:compilePath' ));
    $this->template->assign("infoSmartyCache" , cogumeloGetSetupValue( 'smarty:cachePath' ) );
    $this->template->assign("infoMediaServerHost" , cogumeloGetSetupValue( 'mod:mediaserver:host' ));
    $this->template->assign("infoMediaServerTmpCachePath" , cogumeloGetSetupValue( 'mod:mediaserver:tmpCachePath' ));
    $this->template->assign("infoMediaServerFinalCachePath" , cogumeloGetSetupValue( 'mod:mediaserver:cachePath' ));



    $stringEnabledModules = "";
    foreach( $C_ENABLED_MODULES as $em){
      $stringEnabledModules = $stringEnabledModules." ".$em." ";
    }
    $this->template->assign("infoCEnabledModules" , $stringEnabledModules);
    $this->template->assign("infoBck" , cogumeloGetSetupValue( 'script:backupPath' ));
    $this->template->assign("infoLogDir" , cogumeloGetSetupValue( 'logs:path' ));

    if(cogumeloGetSetupValue( 'logs:rawSql' ) ){
      $this->template->assign("infoLogRawSql" , 'True');
    }
    else{
      $this->template->assign("infoLogRawSql" , 'False');
    }
    if(cogumeloGetSetupValue( 'logs:debug' )){
      $this->template->assign("infoDebug" , 'True');
    }
    else{
      $this->template->assign("infoDebug" , 'False');
    }

    if(cogumeloGetSetupValue( 'logs:error' ) ){
      $this->template->assign("infoErrors" , 'True');
    }
    else{
      $this->template->assign("infoErrors" , 'False');
    }

    if(cogumeloGetSetupValue( 'mod:devel:allowAccess' )){
      $this->template->assign("infoModDevelAllowAccess" , 'True');
    }
    else{
      $this->template->assign("infoModDevelAllowAccess" , 'False');
    }
    if(cogumeloGetSetupValue( 'i18n:gettextUpdate' )){
      $this->template->assign("infoGetTextUpdate" , 'True');
    }
    else{
      $this->template->assign("infoGetTextUpdate" , 'False');
    }

    $this->template->assign("infoLangDefault" , cogumeloGetSetupValue('lang:default'));
    $this->template->assign("infoLangAvailable" , implode(',',array_keys(cogumeloGetSetupValue( 'lang:available' ))) );

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

  function develPhpInfo(){
    phpinfo();
  }

}
