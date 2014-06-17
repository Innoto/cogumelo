<?php

Cogumelo::load('c_vendor/sql-formatter-master/lib/SqlFormatter.php');
Cogumelo::load('c_vendor/kint-1.0.0-wip/Kint.class.php');
Cogumelo::load('c_view/View');
devel::load('controller/LogReaderController');
devel::load('controller/DevelDBController');


class DevelView extends View
{

  function __construct($base_dir){
    parent::__construct($base_dir);
  }

  function accessCheck() {
    global $DEVEL_ALLOWED_HOSTS;
    if(!in_array($_SERVER["REMOTE_ADDR"], $DEVEL_ALLOWED_HOSTS)){
      Cogumelo::error("Must be developer machine to enter on this site");
      RequestController::redirect(SITE_URL_CURRENT.'');
    }
    else{
      if ($_SERVER['PHP_AUTH_PW']!= DEVEL_PASSWORD) {
          header('WWW-Authenticate: Basic realm="Cogumelo Devel Confirm"');
          header('HTTP/1.0 401 Unauthorized');
          echo 'Acceso Denegado.';
          exit;
      } else {
          return true;
      }
    }
  }

  function main($url_path=''){
    $this->template->setTpl("develpage.tpl", "devel");
    $this->template->addJs('js/devel.js', 'devel');
    $this->template->addCss('css/devel.css', 'devel');
    $this->logs();       
    $this->infosetup();
    $this->DBSQL();    

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

    function infosetup(){
      print ".";
    }

    function DBSQL(){
      $data_sql = $this->get_sql_tables();
      foreach ($data_sql as $k => $v) {
        $data_sql[$k] = SqlFormatter::format($v); 
      }
      $this->template->assign("data_sql" , $data_sql);
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

          $result_debugs = array(
            'comment' => $val_debug['comment'],
            'date' => $val_debug['creation_date'],
            'debuging' => @Kint::dump( $val_debug['data'] )
          ); 
            
          echo json_encode($result_debugs);         
          //echo @Kint::dump( $val_debug['data'] );          
        }   
      }
    }

}



