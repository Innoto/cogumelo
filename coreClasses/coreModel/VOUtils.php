<?php 

  class VOUtils {

  // list VOs with priority
  function listVOs() {

    $voarray = array();

    // VOs into APP
    $voarray = $this->mergeVOs($voarray, SITE_PATH.'classes/model/' ); // scan app model dir

    global $C_ENABLED_MODULES;
    foreach($C_ENABLED_MODULES as $modulename) {
      // modules into APP
      $voarray = $this->mergeVOs($voarray, SITE_PATH.'../modules/'.$modulename.'/classes/model/', $modulename );
      // modules into DIST
      $voarray = $this->mergeVOs($voarray, COGUMELO_DIST_LOCATION.'/distModules/'.$modulename.'/classes/model/', $modulename );
      // modules into COGUMELO 
      $voarray = $this->mergeVOs($voarray, COGUMELO_LOCATION.'/coreModules/'.$modulename.'/classes/model/', $modulename );
    }


    return $voarray;
  }

  
  function mergeVOs($voarray, $dir, $modulename='app') {
    $vos = array();

    // VO's from APP
    if ( is_dir($dir) && $handle = opendir( $dir )) {

      while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {

          if(substr($file, -6) == 'VO.php'){
            $class_vo_name = substr($file, 0,-4);

            // prevent reload an existing vo in other place
            if (!array_key_exists( $class_vo_name, $voarray )) {
              require_once($dir.$file);
              $vos[ $class_vo_name ] = array('path' => $dir, 'module' => $modulename );
            }
          }
        }
      }
      closedir($handle);
    }




    return array_merge( $voarray , $vos );
  }



  function getVORelationship( $VOInstance ) {
    $relationships = array();

    if( sizeof( $VOInstance->getCols() ) > 0 ) {
      foreach ( $VOInstance->getCols() as $attr ) {
        if( array_key_exists( 'type', $attr ) && $attr['type'] == 'FOREIGN' ){
          $relationships[] =  $attr['vo'];
        }
      }
    }

    return $relationships;
  }



  function getAllRelationshipRef() {

    $ret = array();

    foreach ( $this->listVOs() as $voName=>$voDef) {
      $vo = new $voName();
      $ret[] = array( 
                      'name' => $voName, 
                      'relationship' => $this->getVORelationship( $vo ), 
                      'elements' => sizeof( $vo->getCols() ),
                      'module' => $voDef['module']
                    );
    }


    return $ret;
  }

}