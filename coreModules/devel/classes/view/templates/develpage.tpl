<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=0.8, user-scalable=no">
  <title>Cogumelo Devel!</title>

  <link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono' rel='stylesheet' type='text/css'>
  {$main_client_includes}
  {$client_includes}
  <script>
    less = {
      env: "development",
      async: false,
      fileAsync: false,
      poll: 1000,
      functions: { },
      dumpLineNumbers: "all",
      relativeUrls: false,
      errorReporting: 'console'
    };
  </script>

  <script>
    var erData = {$erData};
    {literal}
    function hideContainer(){
      $(".container").hide();
    }
    {/literal}
  </script>

  <style>
    .node {
      stroke: #fff;
      stroke-width: 1.5px;
    }
    .link {
      stroke: #999;
      stroke-opacity: .8;
    }
  </style>
</head>


<body>
<div id="header">
  <img id="logo" src="/media/module/devel/img/cogumelo_logo.png" >
  <h1>DEVEL</h1>
  <ul id="options_header_menu">
    <li id ="dbsql_link"><a onclick="hideContainer(); $('#dbsql_container').show();"  href="#dbsql">[Database]</a></li>
    <li id ="urls_link"><a onclick="hideContainer(); $('#urls_container').show();" href="#urls">[URLs]</a></li>
    <li id ="infosetup_link"><a onclick="hideContainer(); $('#infosetup_container').show();" href="#infosetup">[Evironment]</a></li>
    <li id ="deBugs_link"><a onclick="hideContainer(); $('#debug_container').show();" href="#deBugs">[deBugs]</a></li>
    <li id ="logs_link"><a onclick="hideContainer(); $('#logs_tabs').show();" href="#logs">[Logs]</a></li>
  </ul>
</div>
<div id="main">
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="logs_tabs" class="container" style="display:none;">
    <ul class="nav nav-tabs" role="tablist">
      {foreach key=key item=name_log from=$list_file_logs}
        <li role="presentation"><a href="#{$name_log}" aria-controls="{$name_log}" role="tab" data-toggle="tab">{$name_log}</a></li>
      {/foreach}
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
     {foreach key=key item=name_log from=$list_file_logs}
       <div id="{$name_log}" role="tabpanel" class="tab-pane active container_log">...</div>
     {/foreach}
    </div>
  </div>
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="debug_container" class="container" style="display:none;">
    <ul class="debugOptionsContainer withoutDecoration clearfix">
      <li><button class="buttonGrey buttonFormat clearDebugger">Limpar debugger</button></li>
      <li><button class="buttonGrey buttonFormat refreshDebugger">Refrescar debugger</button></li>
      <li>[Este debugger actualízase automáticamente]</li>
    </ul>
    <div class="debugItemsContainer"></div>

  </div>
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="infosetup_container" class="container" style="display:none;">
    <table>
      <thead>
        <tr>
          <th>Cogumelo Setup</th>
          <th>Value</th>
        </tr>
      </thead>
      <!-- row -->
      <tr>
        <td class="td_option">IS_DEVEL_ENV</td>
        <td class="td_value">{$infoIsDevelEnv}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">COGUMELO_LOCATION</td>
        <td class="td_value">{$infoCogumeloLocation}</td>
      </tr>
      <tr>
        <td class="td_option">SITE_PROTOCOL</td>
        <td class="td_value">{$infoSiteProtocol}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SITE_HOST</td>
        <td class="td_value">{$infoSiteHost}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SITE_FOLDER</td>
        <td class="td_value">{$infoSiteFolder}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SITE_URL</td>
        <td class="td_value">{$infoSiteUrl}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SITE_URL_HTTP</td>
        <td class="td_value">{$infoSiteUrlHttp}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SITE_URL_HTTPS</td>
        <td class="td_value">{$infoSiteUrlHttps}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SITE_URL_CURRENT</td>
        <td class="td_value">{$infoSiteUrlCurrent}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SMARTY_CONFIG</td>
        <td class="td_value">{$infoSmartyConfig}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SMARTY_COMPILE</td>
        <td class="td_value">{$infoSmartyCompile}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SMARTY_CACHE</td>
        <td class="td_value">{$infoSmartyCache}</td>
      </tr>
      <tr>
        <td class="td_option">C_ENABLED_MODULES</td>
        <td class="td_value">{$infoCEnabledModules}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">BCK</td>
        <td class="td_value">{$infoBck}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">LOGDIR</td>
        <td class="td_value">{$infoLogDir}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">LOG_RAW_SQL</td>
        <td class="td_value">{$infoLogRawSql}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">DEBUG</td>
        <td class="td_value">{$infoDebug}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">ERRORS</td>
        <td class="td_value">{$infoErrors}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">MOD_DEVEL_ALLOW_ACCESS</td>
        <td class="td_value">{$infoModDevelAllowAccess}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">GETTEXT_UPDATE</td>
        <td class="td_value">{$infoGetTextUpdate}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">LANG_DEFAULT</td>
        <td class="td_value">{$infoLangDefault}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">LANG_AVAILABLE</td>
        <td class="td_value">{$infoLangAvailable}</td>
      </tr>
    </table>


    <table>
      <thead>
        <tr>
          <th>MediaServer Setup</th>
          <th>Value</th>
        </tr>
      </thead>

      <tr>
        <td class="td_option">MEDIASERVER_HOST</td>
        <td class="td_value">{$infoMediaServerHost}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">MEDIASERVER_TMP_CACHE_PATH</td>
        <td class="td_value">{$infoMediaServerTmpCachePath}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">MEDIASERVER_FINAL_CACHE_PATH</td>
        <td class="td_value">{$infoMediaServerFinalCachePath}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">MEDIASERVER_COMPILE_LESS</td>
        <td class="td_value">{$infoMediaServerCompileLess}</td>
      </tr>

    </table>

    <table>
      <thead>
        <tr>
          <th>MAIL Setup</th>
          <th>Value</th>
        </tr>
      </thead>

      <!-- row -->
      <tr>
        <td class="td_option">SMTP_HOST</td>
        <td class="td_value">{$infoSiteHost}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SMTP_PORT</td>
        <td class="td_value">{$infoSmtpPort}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SMTP_AUTH</td>
        <td class="td_value">{$infoSmtpAuth}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SMTP_USER</td>
        <td class="td_value">{$infoSmtpUser}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SYS_MAIL_FROM_NAME</td>
        <td class="td_value">{$infoSysMailFromName}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">SYS_MAIL_FROM_EMAIL</td>
        <td class="td_value">{$infoSysMailFromEmail}</td>
      </tr>
    </table>

    <table>
      <thead>
        <tr>
          <th>DB Setup</th>
          <th>Value</th>
        </tr>
      </thead>
      <!-- row -->
      <tr>
        <td class="td_option">DB_ENGINE</td>
        <td class="td_value">{$infoDBEngine}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">DB_HOSTNAME</td>
        <td class="td_value">{$infoDBHostName}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">DB_PORT</td>
        <td class="td_value">{$infoDBPort}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">DB_USER</td>
        <td class="td_value">{$infoDBUser}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">DB_NAME</td>
        <td class="td_value">{$infoDBName}</td>
      </tr>
      <!-- row -->
      <tr>
        <td class="td_option">DB_ALLOW_CACHE</td>
        <td class="td_value">{$infoDBAllowCache}</td>
      </tr>
    </table>


  </div>
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="dbsql_container" class="container" style="display:none;">

    <fieldset class="erDiagram">
      <legend>Relationship</legend>
      <div class="legend"></div>
      <div id="svgDiv"></div>
    </fieldset>
    <fieldset>
      <legend>Generate Model SQL: This code will executed in generateModel action </legend>
      <div class="infoSQL">{foreach $data_sql as $item}{$item}{/foreach}</div>
    </fieldset>
    <fieldset>
      <legend>Deploy SQL: This code will executed in deploy action</legend>
      <div class="infoSQL">{foreach $deploy_sql as $item}{$item}{/foreach}</div>
    </fieldset>
  </div>

  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="urls_container" class="container" style="display:none;">
    {foreach $dataUrls as $siteUrl}
    <fieldset>
      <legend>{$siteUrl['name']}</legend>
      <div class="infoUrls">
        {foreach $siteUrl['regex_list'] as $regexItem}
          <div class="regexItem clearfix">
            <span class="regex">{$regexItem['regex']}</span>
            <span class="regexDest">{$regexItem['dest']}</span>
          </div>
        {/foreach}
      </div>
    </fieldset>
    {/foreach}
  </div>

</div>
</body>
</html>
