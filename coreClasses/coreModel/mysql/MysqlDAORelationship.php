<?php


/**
 * DAO relationship utilities
 *
 * @package Cogumelo Model
 */
class MysqlDAORelationship 
{


  /**
   * Get joins from VO or Model Class
   *
   * @return string
   */  
  static function getVOJoins( $VOClass, $resolveDependences ) {
    
    $ret = '';

    if( $resolveDependences ) {
      VOUtils::includeVOs();
      $ret = self::joins( VOUtils::getRelObj($VOClass) );
    }

    return $ret;
  }


  /**
   * Compose and nest all joins
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  static function joins($vo) {
    $joinList = '';

    foreach( $vo->relationship as $voRel) {

      if( sizeof($voRel->relationship) == 0  ) {
        // FINAL
        $joinList .= self::leftJoin( self::selectConcat($voRel), $voRel );
      }
      else {
        // Any table
        $joinList .= self::leftJoin( self::selectGroupConcat( $voRel, self::joins( $voRel ) ), $voRel );
      }
      
    }

    return $joinList;
  }


  /**
   * Left Join string
   *
   * @param string $select SELECT string
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  static function leftJoin($select, $sonVo ) {
    return " LEFT JOIN ( ".$select." ) as ".$sonVo->table."_serialized  ON ".$sonVo->table."_serialized.".$sonVo->relatedWithId." = ".$sonVo->parentTable.".".$sonVo->parentId;
  }


  /**
   * Select with simple concat
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  static function selectConcat( $vo ) {
    return " SELECT " . self::cols($vo) . ", concat('{', " . self::jsonCols($vo) . "'}' ) as ".$vo->table." from ".$vo->table." GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }


  /**
   * Select with group concat
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   * @param string $joins join string
   *
   * @return string
   */  
  static function selectGroupConcat( $vo, $joins ) {
    return " SELECT " .self::cols($vo). " , concat('{', ". self::jsonCols($vo) ." ". self::getGroupConcats( $vo ) ."'}') as ".$vo->table." from ".$vo->table." ". $joins. " GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }


  /**
   * get Cols in json format
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  static function jsonCols($vo) {
    $returnCols = '';
    $coma = '';

    foreach($vo->cols as $col) {
      $returnCols .= "'".$coma."\"".$vo->table.".".$col."\": ' ,'\"',".$vo->table.".".$col.",'\"', ";
      $coma = ',';
    }

    return $returnCols;
  }

  /**
   * get cols in plain text format
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  static function cols($vo) {
    $returnCols = '';
    $coma = '';

    foreach($vo->cols as $col) {
      $returnCols .= $coma." ".$vo->table.".".$col."  ";
      $coma = ',';
    }

    return $returnCols;
  }

  /**
   * group Concat function
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  static function getGroupConcats ($vo) {
    $groupConcats = '';

    foreach( $vo->relationship as $voRel) {
      $groupConcats .= "',\"".$voRel->parentTable.".".$voRel->table."\": [', group_concat(".$voRel->table."_serialized.".$voRel->table."), ']',";
    }
    
    return $groupConcats;
  }

}