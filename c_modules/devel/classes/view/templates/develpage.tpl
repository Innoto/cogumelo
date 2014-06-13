<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Cogumelo Devel!</title>
  
  {literal}
  <link href='http://fonts.googleapis.com/css?family=Share+Tech+Mono' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" >
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>

  <script>
  function hideContainer(){
      $(".container").hide();
  }
  </script>

  {/literal}
  {$css_includes}
  {$js_includes}

</head>
<body>
<div id="header">
  <img id="logo" src="/media/module/devel/img/cogumelo_logo.png" >
  <h1>DEVEL</h1>
  <ul id="options_header_menu">
    <li id ="infosetup_link"><a onclick="hideContainer(); $('#infosetup_container').show();" href="#infosetup">Infosetup</a></li>
    <li id ="deBugs_link"><a onclick="hideContainer(); $('#debug_container').show();" href="#deBugs">deBugs</a></li>
    <li id ="logs_link"><a onclick="hideContainer(); $('#logs_tabs').show();" href="#logs">Logs</a></li>
    <li id ="dbsql_link"><a onclick="hideContainer(); $('#dbsql_container').show();"  href="#dbsql">DB SQL</a></li>
  </ul>
</div>
<div id="main">
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="logs_tabs" class="container">
    <ul>
      {foreach key=key item=name_log from=$list_file_logs}    
        <li><a href="#{$name_log}">{$name_log}</a></li>
      {/foreach}
    </ul>
    {foreach key=key item=name_log from=$list_file_logs}    
      <div id="{$name_log}" class="container_log"></div>
    {/foreach}
  </div>
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="debug_container" class="container"></div>
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="infosetup_container" class="container" style="display:none;">
    <table>
      <thead>
        <tr>
          <th>Options</th>
          <th>setup.dev</th>
          <th>setup.final</th>
        </tr>
      </thead>
      <tr>
        <td class="td_option">Lorem ipsum dolor sit ame</td>
        <td class="td_dev">Ut non </td>
        <td class="td_fnl">viverra suscipit.</td>
      </tr>

      <tr>
        <td class="td_option">STNF</td>
        <td class="td_dev">viverra suscipit.</td>
        <td class="td_fnl">Lorem ipsum dolor sit ame</td>
      </tr>

      <tr>
        <td class="td_option">viverra suscipit.</td>
        <td class="td_dev">Lorem ipsum dolor sit ame</td>
        <td class="td_fnl">1</td>
      </tr>

      

    </table>
  </div>
  <!-- ****************************************************************************************************************  -->
  <!-- ****************************************************************************************************************  -->
  <div id="dbsql_container" class="container" style="display:none;">
    <fieldset>
      <legend>SQL</legend>
      <div class="infoSQL">{foreach $data_sql as $item}{$item}{/foreach}</div>      
    </fieldset>
  </div>
</div> 
 
</body>
</html>