<?php 

  class VOUtils {

  // list VOs with priority
  static function listVOs() {

    $voarray = array();

    // VOs into APP
    $voarray = self::mergeVOs($voarray, SITE_PATH.'classes/model/' ); // scan app model dir

    global $C_ENABLED_MODULES;
    foreach($C_ENABLED_MODULES as $modulename) {
      // modules into APP
      $voarray = self::mergeVOs($voarray, SITE_PATH.'../modules/'.$modulename.'/classes/model/', $modulename );
      // modules into DIST
      $voarray = self::mergeVOs($voarray, COGUMELO_DIST_LOCATION.'/distModules/'.$modulename.'/classes/model/', $modulename );
      // modules into COGUMELO 
      $voarray = self::mergeVOs($voarray, COGUMELO_LOCATION.'/coreModules/'.$modulename.'/classes/model/', $modulename );
    }


    return $voarray;
  }

  
  static function mergeVOs($voarray, $dir, $modulename='app') {
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


  static function getVOConnections( $VOInstance ) {
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



  static function getAllRelScheme() {

    $ret = array();

    foreach ( self::listVOs() as $voName=>$voDef) {
      $vo = new $voName();
      $ret[ $voName ] = array( 
                      'name' => $voName, 
                      'relationship' => self::getVOConnections( $vo ), 
                      'elements' => sizeof( $vo->getCols() ),
                      'module' => $voDef['module']
                    );
    }

    return $ret;
  }




  static function getVORelationship( $voName, $voOriginName=false ) {

    $relArray = array('vo' => $voName, 'relationship' => array() );

    $allVOsRel = self::getAllRelScheme();

    if( sizeof( $allVOsRel ) > 0) {
      foreach( $allVOsRel as $roRel ) {
        if(  
          (
            in_array( $roRel['name'], $allVOsRel[$voName]['relationship']) ||             // relation from this to other VO
            in_array( $voName, $roRel['relationship'] )                     // relation fron other to this VO
          ) && 
          $roRel['name'] != $voOriginName
        ) {
            $relArray['relationship'][] = self::getVORelationship( $roRel['name'], $voName );
          }
      }
    }

    return $relArray;
  }



  static function createModelRelTreeFiles() {

    foreach( self::listVOs() as $voName => $vo) {
      file_put_contents( APP_TMP_PATH.'/modelRelationship/'.$voName.'.json' , json_encode(self::getVORelationship($voName)) );
    }
  }

  static function getRelTree( $vo ) {
    Cogumelo::error('No dependence files, please execute ./cogumelo updateModelRelationship');
  }


}

/*
  
  SUPER CONSULTA!!!!

select user_user.id, user_user.login, concat('[', group_concat( user_role_json.permission) , ']' ) from user_user
left join user_userRole ON user_userRole.user = user_user.id
left join (
  select user_role.id as id, concat('{ "user_role.id": "', user_role.id, '","user_role.name": "', user_role.name, '", "user_role.permission": [', group_concat(user_permission_json.permission), ']}') as permission from user_role
  left join user_rolePermission ON user_rolePermission.role = user_role.id
  left join (
    select 
      user_permission.id as id,
      concat(
        '{',
          '"user_permission.id": "',user_permission.id, '",',
          '"user_permission.name": "',user_permission.name, '"',
        '}'
      ) as permission
  from user_permission) as user_permission_json ON user_permission_json.id = user_rolePermission.permission
  group by user_role.id

) as user_role_json ON user_role_json.id = user_userRole.role
group by user_user.id

*/