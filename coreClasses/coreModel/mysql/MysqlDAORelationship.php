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
  function getVOJoins( $VOClass, $resolveDependences ) {
    
    $ret = '';

    if( $resolveDependences ) {
      VOUtils::includeVOs();
      $ret = $this->joins( VOUtils::getRelObj($VOClass, $resolveDependences) );
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
  function joins($vo) {
    $joinList = '';

    foreach( $vo->relationship as $voRel) {

      if( sizeof($voRel->relationship) == 0  ) {
        // FINAL
        $joinList .= $this->leftJoin( $this->selectConcat($voRel), $voRel );
      }
      else {
        // Any table
        $joinList .= $this->leftJoin( $this->selectGroupConcat( $voRel, $this->joins( $voRel ) ), $voRel );
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
  function leftJoin($select, $sonVo ) {
    return " LEFT JOIN ( ".$select." ) as ".$sonVo->table."_serialized  ON ".$sonVo->table."_serialized.".$sonVo->relatedWithId." = ".$sonVo->parentTable.".".$sonVo->parentId;
  }


  /**
   * Select with simple concat
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  function selectConcat( $vo ) {
    return " SELECT " . $this->cols($vo) . ", concat('{', " . $this->jsonCols($vo) . "'}' ) as ".$vo->table." from ".$vo->table." GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }


  /**
   * Select with group concat
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   * @param string $joins join string
   *
   * @return string
   */  
  function selectGroupConcat( $vo, $joins ) {
    return " SELECT " .$this->cols($vo). " , concat('{', ". $this->jsonCols($vo) ." ". $this->getGroupConcats( $vo ) ."'}') as ".$vo->table." from ".$vo->table." ". $joins. " GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }


  /**
   * get Cols in json format
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */  
  function jsonCols($vo) {
    $returnCols = '';
    $coma = '';

    foreach($vo->cols as $col) {
      $returnCols .= "'".$coma."\"".$vo->table.".".$col."\": ' ,'\"', COALESCE(".$vo->table.".".$col.", 'null'),'\"', ";
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
  function cols($vo) {
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
  function getGroupConcats ($vo) {
    $groupConcats = '';

    foreach( $vo->relationship as $voRel) {
      $groupConcats .= "',\"".$voRel->parentTable.".".$voRel->table."\": [', group_concat(".$voRel->table."_serialized.".$voRel->table."), ']',";
    }
    
    return $groupConcats;
  }

}