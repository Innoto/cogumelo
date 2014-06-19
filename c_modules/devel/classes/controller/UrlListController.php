<?php 


class UrlListController 
{
  function __construct()
  {       
        
  }

  function listUrls() {
    global $C_ENABLED_MODULES;
    $finalArray = array();
    
    // add modules
    if(sizeof($C_ENABLED_MODULES) > 0 ) {
      foreach ($C_ENABLED_MODULES as $module_name) {
        array_push($finalArray, $this->listModuleRegex($module_name) );
      }
    }
    
    // main cogumelo app
    array_push($finalArray, $this->listAppRegex() );


    return $finalArray;
  }


  function listModuleRegex($module_name) {

    $regex_array = array();

    eval('$mod_obj = new '.$module_name.'();');

    foreach( $mod_obj->url_patterns as $regex => $dest ) {
      array_push($regex_array, array('regex'=>$regex, 'dest' => $dest) );
    }

    return array('name' => $module_name, 'regex_list' => $regex_array);
  }


  function listAppRegex() {

    global $_C;
    $regex_array = array();

    foreach( $_C->url_patterns as $regex => $dest ) {
      array_push($regex_array, array('regex'=>$regex, 'dest' => $dest) );
    }


    return array('name' => 'c_app', 'regex_list' => $regex_array);
  }

}

