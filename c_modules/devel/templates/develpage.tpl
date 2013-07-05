<!doctype html>
 
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Cogumelo Devel!</title>
  
  {literal}
  <link href='http://fonts.googleapis.com/css?family=Share+Tech+Mono' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>

  <script>
  function hideContainer(){
      $(".container").hide();
  }
  </script>

  

  <style>
    body{margin: 0px; padding: 0px; color:#444; font-family: 'Share Tech Mono', sans-serif !important; background:#E7E7E8;}
    #header{
      background:#333334;
      height:80px;
      position: fixed;
      top:0px;
      z-index: 10000;
      width: 100%;
    }
    #header #logo{opacity: 0.8;}
    #header #logo,
    #header h1{
      float:left;
    }
    #options_header_menu{float:right;}
    #header #logo{ margin: 10px 20px; }
    #header h1{margin: 20px 0px;} 
    #options_header_menu{ margin: 0px; padding: 0px; }
    #options_header_menu li{ 
      display:inline-block;
      color:#E7E7E8;
      list-style: none;
      list-style-image: none;
      margin:15px;
      padding: 0px 45px 0px 10px;
      height: 40px;
      line-height: 40px;
      vertical-align: middle;
    }
    #options_header_menu li a{ color:#E7E7E8; text-decoration: none;}
    #deBugs_link{ background: url('/media/img/deBugs.png') right no-repeat;}
    #logs_link{ background: url('/media/img/logs.png') right no-repeat;}
    #dbsql_link{ background: url('/media/img/database.png') right no-repeat;}
    #logs_tabs{ font-family: 'Share Tech Mono', sans-serif !important; font-size: 0.9em; background: green;}
    #logs_tabs .container_log{ min-height: 480px; color:green; background:#000 url('/media/img/cogumelo_logo_mini.png') right bottom no-repeat;}
    #logs_tabs ul{background: green; border:0px;}
    #logs_tabs li{background: #005200; border:0px;}
    #logs_tabs li.ui-tabs-active{ margin:0px 5px; background: #fff;}
    #logs_tabs li a{color: white !important;}
    #logs_tabs li.ui-tabs-active a{color:green !important; text-decoration: underline;}
    #logs_tabs p{ margin:5px 0px; }
    #logs_tabs div.lines{ margin:5px 0px; }
    
    #main{ width:100%; max-width:1100px;
      margin:90px auto 0px auto;}
    #dbsql_container{
      background: #E7E7E8;
      padding: 20px;
    }
    #dbsql_container .columnL{ float: left;  width: 70%; min-height: 300px; }
    #dbsql_container .columnR{ float: right;  width: 30%; padding-top:55px; min-height: 245px;}    
    #dbsql_container .columnL,
    #dbsql_container .columnR{  margin-bottom: 40px;}    
    #dbsql_container .options_container_SQL{ width:100%; height: 100px; }
    
    #dbsql_container .columnL input{ background: #333; border:0; padding: 7px; color:#E7E7E8; 
      -webkit-border-radius: 5px; -moz-border-radius: 5px;  border-radius: 5px; font-family: 'Share Tech Mono', sans-serif !important;}
    #dbsql_container .columnL #infoSQL{ padding: 10px; height:100%; max-height: 300px; overflow: auto; }
    #dbsql_container .columnL fieldset{border:3px solid #D6D6D6;}
    #dbsql_container .columnL legend{ font-size: 28px;}
    #dbsql_container .columnR input{ width:90%; margin-left: 20px; padding: 10px; font-family: 'Share Tech Mono', sans-serif !important;}
    #dbsql_container .options_container_SQL{ text-align: center; height: 65px;}
    #execute{ width:182px; height: 65px; border:0px; background: url('/media/img/exec_button2.png') no-repeat; cursor: pointer; color:#fff; font-size: 16px; font-family: 'Share Tech Mono', sans-serif !important;
    margin-left: auto; margin-right: auto;}

    hr{ border:1px solid #D6D6D6; }
    .cll_container{ color: white;}
  </style>
  {/literal}
  {$css_includes}
  {$js_includes}

</head>
<body>
<div id="header">
  <img id="logo" src="/media/img/cogumelo_logo.png" >
  <h1>DEVEL</h1>
  <ul id="options_header_menu">
    <li id ="deBugs_link"><a onclick="hideContainer(); $('#debug_container').show();" href="#deBugs">deBugs</a></li>
    <li id ="logs_link"><a onclick="hideContainer(); $('#logs_tabs').show();" href="#logs">Logs</a></li>
    <li id ="dbsql_link"><a onclick="hideContainer(); $('#dbsql_container').show();"  href="#dbsql">DB SQL</a></li>
  </ul>
</div>
<div id="main">
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
  <div id="debug_container" class="container"></div>
  <div id="dbsql_container" class="container" style="display:none;">
    <div class="columnL">
      <p>
        <label for="user_root">User</label>
        <input name="user_root" id="user_root" type="text" />
        <label for="user_pass">Pass</label>
        <input name="user_pass" id="user_pass" type="text" />
      </p>
      <fieldset>
      <legend>SQL</legend>
      <div id="infoSQL">
          CREATE TABLE sample.taceledger (
          `runno` INT(10) unsigned not null default '0',
          `doc_no` DECIMAL(6,0) unsigned NOT null ,
          `doc_date` DATE,
          `gl` DECIMAL(4,0) unsigned not null ,
          `slcode` VARCHAR(6) not null ,
          `tr` VARCHAR(1) not null ,
          `cr_db` VARCHAR(1) not null,
          `amount` DECIMAL(13,2)unsigned not null ,
          `revcode` DECIMAL(4,0)unsigned not null ,
          `narration` VARCHAR(30)unsigned not null,
          `cramt` DECIMAL(13,2) unsigned not null,
          `indi` VARCHAR(1)not null ,
          `dbamt` DECIMAL(13,2) unsigned not null,
          `acctype` VARCHAR(1) unsigned not null,
          PRIMARY KEY (`runno`)
          )ENGINE=InnoDB;

          CREATE TABLE sample.taceledger (
          `runno` INT(10) unsigned not null default '0',
          `doc_no` DECIMAL(6,0) unsigned NOT null ,
          `doc_date` DATE,
          `gl` DECIMAL(4,0) unsigned not null ,
          `slcode` VARCHAR(6) not null ,
          `tr` VARCHAR(1) not null ,
          `cr_db` VARCHAR(1) not null,
          `amount` DECIMAL(13,2)unsigned not null ,
          `revcode` DECIMAL(4,0)unsigned not null ,
          `narration` VARCHAR(30)unsigned not null,
          `cramt` DECIMAL(13,2) unsigned not null,
          `indi` VARCHAR(1)not null ,
          `dbamt` DECIMAL(13,2) unsigned not null,
          `acctype` VARCHAR(1) unsigned not null,
          PRIMARY KEY (`runno`)
          )ENGINE=InnoDB;

          CREATE TABLE sample.taceledger (
          `runno` INT(10) unsigned not null default '0',
          `doc_no` DECIMAL(6,0) unsigned NOT null ,
          `doc_date` DATE,
          `gl` DECIMAL(4,0) unsigned not null ,
          `slcode` VARCHAR(6) not null ,
          `tr` VARCHAR(1) not null ,
          `cr_db` VARCHAR(1) not null,
          `amount` DECIMAL(13,2)unsigned not null ,
          `revcode` DECIMAL(4,0)unsigned not null ,
          `narration` VARCHAR(30)unsigned not null,
          `cramt` DECIMAL(13,2) unsigned not null,
          `indi` VARCHAR(1)not null ,
          `dbamt` DECIMAL(13,2) unsigned not null,
          `acctype` VARCHAR(1) unsigned not null,
          PRIMARY KEY (`runno`)
          )ENGINE=InnoDB;
      </div>
    </fieldset>
    </div>
    <div class="columnR">
      <p><input type="button" value="CREATE DB AND USER DB" id="create_db" name="create_db" /></p>
      <p><input type="button" value="CREATE TABLE" id="create_table" name="create_table" /></p>
      <p><input type="button" value="CREATE CONTENT" id="create_content" name="create_content" /><p>
    </div>
    <hr style="clear:both;">
    <div class="options_container_SQL">
      <input type="button" value="EXEC" id="execute" name="execute" />
    </div>
    
  </div>
</div> 
 
</body>
</html>