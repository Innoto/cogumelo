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
      else if( $vo->isM2M == true ) {
        // M2M table
        $joinList .= $this->leftJoin( $this->selectConcat($voRel), $voRel );
        $joinList .= $this->leftJoin( $this->selectGroupConcat( $voRel, $this->joins( $voRel ) ), $voRel );
      }
      else {
        // Any table
        $joinList .= $this->leftJoin( $this->selectGroupConcat( $voRel, $this->joins( $voRel ) ), $voRel );
      }
      
      return $joinList;
    }
  }


  function leftJoin($select, $sonVo ) {

    return " LEFT JOIN ( ".$select." ) as ".$sonVo->table."_serialized  ON ".$sonVo->table."_serialized.".$sonVo->relatedWithId." = ".$sonVo->parentTable.".".$sonVo->parentId;
  }


  function selectConcat( $vo ) {
    return "SELECT " . $this->cols($vo) . ", concat('{ " . $this->jsonCols($vo) . " }' ) as ".$vo->table." from ".$vo->table." GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }

  function selectGroupConcat($vo, $joins, $relKey) {
    return "SELECT " .$this->cols($vo). " , concat('{ ". $this->jsonCols($vo) .", ". $this->getGroupConcats( $vo ) ."}') as ".$vo->table." from ".$vo->table. $joins. " GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }



  function jsonCols($vo) {
    return '"campo":campo, "campo":campo, "campo":campo';
  }

  function cols($vo) {
    return implode(', ', $vo->cols);
  }

  function getGroupConcats ($vo) {
    $groupConcats = '';

    foreach( $vo->relationship as $voRel) {
      /*if( $voRel['type'] != 'M2M' ) {
      }*/
    }
    
    $groupConcats = "user_role.user_permission: [', group_concat(user_permission_serialized.user_permission), ']";

    return $groupConcats;
  }

}