<?php

Cogumelo::load('coreModel/DAORelationship.php');

class MysqlDAORelationship extends DAORelationship
{


  function joins($vo, $relFrom, $relTo) {
    $joinList = '';

    foreach( $vo['relationship'] $voRel) {

      if( $voRel['type'] == 'M2M' ) {
        $joinList .= leftJoin( selectConcat($voRel) );
        $joinList .= leftJoin( selectGroupConcat( $voRel, joins( $voRel ) ) );
      }
      else 
      if( $voRel['type'] == 'FINAL' ){
        $joinList .= leftJoin( selectConcat($voRel) );
      }
      else {
        $joinList .= leftJoin( selectGroupConcat( $voRel, joins( $voRel ) )  );
      }

      return $joinList;
    }
  }


  function leftJoin($select, $as, $onFrom, $onTo ) {
    return " LEFT JOIN ( ".$select." ) as ".$as." ON ".$onFrom."_serialized = ".$onTo;
  }

  function selectConcat( $vo ) {
    return "SELECT " . cols($vo) . ", concat('{ " . jsonCols() . " }' ) as ".$vo['table']." from ".$vo['table']." GROUP BY " . $vo['table'] . "." . $vo['pk'];
  }

  function selectGroupConcat($vo, $joins) {
    return "SELECT " .cols($vo). " , concat('{ ". jsonCols($vo) .", ". getGroupConcats( $vo ) ."}') as ".$vo['table']." from ".$vo['table']. $joins. " GROUP BY " . $vo['table'] . "." . $vo['pk'];
  }



  function jsonCols($vo) {
    return '"campo":campo, "campo":campo, "campo":campo';
  }

  function cols($vo) {
    return "campo, campo, campo";
  }

  function getGroupConcats ($vo) {
    $groupConcats = '';

    foreach( $vo['relationship'] $voRel) {
      if( $voRel['type'] != 'M2M' ) {
      }
    }
    
    $groupConcats = "user_role.user_permission: [', group_concat(user_permission_serialized.user_permission), ']";

    return $groupConcats;
  }

}