<?php


class MysqlDAORelationship 
{


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


  function leftJoin($select, $sonVo ) {
    return " \n LEFT JOIN ( ".$select." ) as ".$sonVo->table."_serialized  ON ".$sonVo->table."_serialized.".$sonVo->relatedWithId." = ".$sonVo->parentTable.".".$sonVo->parentId;
  }

  function selectConcat( $vo ) {
    return "\n SELECT " . $this->cols($vo) . ", concat('{', " . $this->jsonCols($vo) . "'}' ) as ".$vo->table." from ".$vo->table." GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }

  function selectGroupConcat( $vo, $joins ) {
    return "\nSELECT " .$this->cols($vo). " , concat('{', ". $this->jsonCols($vo) ." ". $this->getGroupConcats( $vo ) ."'}') as ".$vo->table." from ".$vo->table." ". $joins. " GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }

  function jsonCols($vo) {
    $returnCols = '';
    $coma = '';

    foreach($vo->cols as $col) {
      $returnCols .= "'".$coma."\"".$vo->table.".".$col."\": ' ,'\"',".$vo->table.".".$col.",'\"', ";
      $coma = ',';
    }

    return $returnCols;
  }

  function cols($vo) {
    $returnCols = '';
    $coma = '';

    foreach($vo->cols as $col) {
      $returnCols .= $coma." ".$vo->table.".".$col."  ";
      $coma = ',';
    }

    return $returnCols;
  }

  function getGroupConcats ($vo) {
    $groupConcats = '';

    foreach( $vo->relationship as $voRel) {
      $groupConcats .= "',\"".$voRel->parentTable.".".$voRel->table."\": [', group_concat(".$voRel->table."_serialized.".$voRel->table."), ']',";
    }
    
    return $groupConcats;
  }

}