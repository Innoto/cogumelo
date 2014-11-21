<?php 


class LogReaderController 
{
  function __construct()
  {       
        
  }
  function read_logs(){
    $list_file_logs_path = glob(SITE_PATH."log/*.log");
    $list_file_logs = str_replace(SITE_PATH."log/", "", $list_file_logs_path);
    $list_file_logs = str_replace(".log", "", $list_file_logs);
    $content_logs = array();
    foreach ($list_file_logs_path as $key => $value) {
     $lines = $this->read_file($value, 10, $list_file_logs[$key]);
     $name_log = $list_file_logs[$key];
     $temp_array = array('log_name' => $name_log, 'data_log' => $lines);
     array_push($content_logs , $temp_array);    
    } 
    return $content_logs;            
  }

  function read_file($file, $lines, $name_file) {  
    $handle = fopen($file, "r");
    $pos = -2;
    $data = "";
    if(fseek($handle, $pos, SEEK_END) == -1) {

    }else{
      if(isset($_SESSION['log_session_'.$name_file])){
          $data = stream_get_contents($handle, -1, $_SESSION['log_session_'.$name_file]);
          
          $data = explode('<br />' , nl2br($data) );
          unset($data[0]);        
          $data = array_values($data); 
          $data = array_reverse($data);
          $data = implode('<br />' , $data);
      
      }else{
          fseek ( $handle, ($lines * -230), SEEK_END );
          $data = stream_get_contents($handle, -1);                
          $data = explode('<br />' , nl2br($data) );
          unset($data[0]);
        
          $data = array_values($data); 
          //$data = array_reverse($data);
          $data = implode('<br />' , $data);
      }
    }        
    $_SESSION['log_session_'.$name_file] = ftell($handle);
    
    fclose ($handle);
    return $data;
  }

}

?>