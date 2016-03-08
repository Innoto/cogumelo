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
    {$infoConf}
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
