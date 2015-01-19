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


  static function getVOCols($voName) {
    $retCols = array();

    $vo = new $voName();

    foreach( $vo->getCols() as $colK => $col ) {
        $retCols[] = $colK;
    }

    return $retCols;
  }

  static function getVOConnections( $VOInstance, $includeKeys= false ) {
    $relationships = array();

    if( sizeof( $VOInstance->getCols() ) > 0 ) {
      foreach ( $VOInstance->getCols() as $attrKey=>$attr ) {
        if( array_key_exists( 'type', $attr ) && $attr['type'] == 'FOREIGN' ){

          if( !$includeKeys ) {
            $relationships[] =  $attr['vo'];
          }
          else {
            $relationships[ $attr['vo'] ] =  array('parent' => $attrKey, 'related'=>$attr['key'] );
          }
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
                      'extendedRelationship' => self::getVOConnections( $vo, true ),
                      'elements' => sizeof( $vo->getCols() ),
                      'module' => $voDef['module']
                    );
    }

    return $ret;
  }




  static function getVORelationship( $voName, $parentInfo=array( 'parentVO' => false, 'parentTable'=>false, 'parentId'=>false, 'relatedWithId'=>false ) ) {

    $vo = new $voName();
    $relArray = array(
                        'vo' => $voName, 
                        'table' => $vo::$tableName
                      );
    $relArray = array_merge( $relArray, $parentInfo);

    $relArray['cols'] = self::getVOCols( $voName );
    $retArray['relationship'] = array();


    $allVOsRel = self::getAllRelScheme();

    if( sizeof( $allVOsRel ) > 0) {
      foreach( $allVOsRel as $roRel ) {
        if(  
          (
            in_array( $roRel['name'], $allVOsRel[$voName]['relationship']) ||   // relation from this to other VO
            in_array( $voName, $roRel['relationship'] )                         // relation fron other to this VO
          ) && 
          ($parentInfo['parentVO'] == false || $roRel['name'] != $parentInfo['parentVO'])
        ) {


            $sonParentArray = array(
             'parentVO' => $voName, 
             'parentTable'=> $vo::$tableName, 
             'parentId'=> 'NO', 
             'relatedWithId'=> 'NO' 
            );

            //if( sizeof($allVOsRel[$voName]['extendedRelationship']) != 0 ) {
              if( array_key_exists( $roRel['name'], $allVOsRel[$voName]['extendedRelationship'] ) ) {

                $sonParentArray['parentId'] = $allVOsRel[$voName]['extendedRelationship'][$roRel['name']]['parent'];
                $sonParentArray['relatedWithId'] = $allVOsRel[$voName]['extendedRelationship'][$roRel['name']]['related'];
              }
              else {
                $sonParentArray['parentId'] = $roRel['extendedRelationship'][$voName]['related'];
                $sonParentArray['relatedWithId']  = $roRel['extendedRelationship'][$voName]['parent'];
              }
            //}


            if($sonParentArray['parentId'] == 'NO') {
              echo "\n\nanalicemos esto\n\n";
              var_dump( $roRel['extendedRelationship'][$voName]['parent'] );
              exit;
            }

            $relArray['relationship'][] = self::getVORelationship( $roRel['name'], $sonParentArray );
          }
      }
    }

    return $relArray;
  }



  static function createModelRelTreeFiles() {
    Cogumelo::load('coreModel/mysql/MysqlDAORelationship.php');

    $mrel = new MysqlDAORelationship();
    $vo = file_get_contents("/home/pblanco/proxectos/cogumelo/packages/sampleApp/app/tmp/modelRelationship/UserVO.json");

    echo $mrel->joins(json_decode($vo));

    foreach( self::listVOs() as $voName => $vo) {
      file_put_contents( APP_TMP_PATH.'/modelRelationship/'.$voName.'.json' , json_encode(self::getVORelationship($voName)) );
    }
  }



  static function getRelTree( $vo ) {
    $ret = false;

    $voJSONPath = APP_TMP_PATH.'/modelRelationship/'.$voName.'.json';

    if( file_exists( $voJSONPath ) ) {
      $ret = json_decode( file_get_contents( $voJSONPath ) );
    }
    else{
      Cogumelo::error('No dependence file:('.$voJSONPath.') for "'.$vo.'", please execute ./cogumelo createRelSchemes');
    }

    return $ret;
  }


  static function includeVOs() {
    // incluir todos VO's
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
/*
select user_user.id, user_user.login, concat('[', group_concat( user_role_json.user_permission) , ']' ) from user_user
  left join user_userRole ON user_userRole.user = user_user.id
  left join (
    select user_role.id as id, concat('{ "user_role.id": "', user_role.id, '","user_role.name": "', user_role.name, '", "user_role.user_permission": [', group_concat(user_permission_serialized.user_permission), ']}') as user_permission from user_role
      left join ( 
        select user_rolePermission.id as id, role, permission, concat('{ "user_rolePermission.id": "',user_rolePermission.id, '", "user_rolePermission.role": "',user_rolePermission.role, '", "user_rolePermission.permission": "',user_rolePermission.permission, '"  }' ) as user_rolePermission from user_rolePermission group by  user_rolePermission.id
      ) as user_rolePermission_serialized ON user_rolePermission_serialized.role = user_role.id
      left join (
        select user_permission.id as id, concat('{ "user_permission.id": "',user_permission.id, '", "user_permission.name": "',user_permission.name, '" }' ) as user_permission from user_permission group by user_permission.id
      ) as user_permission_serialized  ON user_permission_serialized.id = user_rolePermission_serialized.permission
    group by user_role.id

  ) as user_role_json ON user_role_json.id = user_userRole.role
group by user_user.id
*/
/*
select user_user.id, user_user.login, concat('[', group_concat( user_role_json.user_permission) , ']' ) from user_user
  left join user_userRole ON user_userRole.user = user_user.id
  left join (
    select user_role.id as id, concat('{ "user_role.id": "', user_role.id, '","user_role.name": "', user_role.name, '", "user_role.user_permission": [', group_concat(user_rolePermission_serialized.user_rolePermission,', ', user_permission_serialized.user_permission ), ']}') as user_permission from user_role
      left join ( 
        select user_rolePermission.id as id, role, permission, concat('{ "user_rolePermission.id": "',user_rolePermission.id, '", "user_rolePermission.role": "',user_rolePermission.role, '", "user_rolePermission.permission": "',user_rolePermission.permission, '"  }' ) as user_rolePermission from user_rolePermission group by  user_rolePermission.id
      ) as user_rolePermission_serialized ON user_rolePermission_serialized.role = user_role.id
      left join (
        select user_permission.id as id, concat('{ "user_permission.id": "',user_permission.id, '", "user_permission.name": "',user_permission.name, '" }' ) as user_permission from user_permission group by user_permission.id
      ) as user_permission_serialized  ON user_permission_serialized.id = user_rolePermission_serialized.permission
    group by user_role.id

  ) as user_role_json ON user_role_json.id = user_userRole.role
group by user_user.id*/