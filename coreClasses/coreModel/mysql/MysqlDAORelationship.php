<?php


/**
 * DAO relationship utilities
 *
 * @package Cogumelo Model
 */
class MysqlDAORelationship {


  var $filters = array();
  var $whereData = array();
  var $joinType = 'LEFT';

  public function __construct() {

  }

  /**
   * Get joins from VO or Model Class
   *
   * @return string
   */
  public function getVOJoins( $VOClass, $joinType, $resolveDependences, $filters = array() ) {

    $this->filters = $filters;
    $this->joinType = $joinType;
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
  public function joins($vo) {
    $joinList = '';

    foreach( $vo->relationship as $voRel) {

      if( count($voRel->relationship) == 0  ) {
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
  public function leftJoin($select, $sonVo ) {
    return " ".$this->joinType." JOIN ( ".$select." ) as " . $sonVo->parentId."_".$sonVo->table."_serialized  ON ".$sonVo->parentId."_".$sonVo->table."_serialized.".$sonVo->relatedWithId." = ".$sonVo->parentTable.".".$sonVo->parentId;
  }


  /**
   * Select with simple concat
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */
  public function selectConcat( $vo ) {

    $where = $this->setWheres( $vo->vo );
    return " SELECT " . $this->cols($vo) . ", group_concat('{', " . $this->jsonCols($vo) . "'}' ) as ".$vo->table." from ".$vo->table. $where. " GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }


  /**
   * Select with group concat
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   * @param string $joins join string
   *
   * @return string
   */
  public function selectGroupConcat( $vo, $joins ) {
    $where = $this->setWheres( $vo->vo );
    return " SELECT " .$this->cols($vo). " , concat('{', ". $this->jsonCols($vo) ." ". $this->getGroupConcats( $vo ) ."'}') as ".$vo->table." from ".$vo->table." ". $joins .$where. " GROUP BY " . $vo->table . "." . $vo->relatedWithId;
  }


  /**
   * get Cols in json format
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */
  public function jsonCols($vo) {

    $returnCols = '';
    $coma = '';

    foreach($vo->cols as $col) {
      $returnCols .= "'".$coma."\"".$vo->table.".".$col."\": ' ,'\"', " . $this->colToString($col, $vo) . ",'\"', ";
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
  public function cols($vo) {
    $returnCols = '';
    $coma = '';

    foreach($vo->cols as $col) {
      $returnCols .= $coma." ".$vo->table.".".$col."  ";
      $coma = ',';
    }

    return $returnCols;
  }

  public function colToString( $colKey, $vo ) {
    //$retStr = "COALESCE(".$vo->table.".".$colKey.", '".COGUMELO_NULL."')";

    $col = false;
    $colType =  false;
    $model =  new $vo->vo();


    eval( '$col = $model->langKey($colKey, true);' );
    eval( '$colType = '.$vo->vo.'::$cols[ $col ]["type"];' );



    if( $colType == 'BOOLEAN' ) {
      $retStr = "ASCII( COALESCE(".$vo->table.".".$colKey.", '".COGUMELO_NULL."') )";
    }
    else
    if( $colType == 'GEOMETRY' ) {
      $retStr = "ASCII( AsText( COALESCE(".$vo->table.".".$colKey.", '".COGUMELO_NULL."')) )";
      $retStr = "if(".$vo->table.".".$colKey." is not null,  astext(".$vo->table.".".$colKey."),'".COGUMELO_NULL."' )";
    }
    else {
      $toScape = '"';
      $scaped = '\\\"';


      // OLD WAY
      //$retStr = "REPLACE( COALESCE(" . $vo->table.".".$colKey.", '".COGUMELO_NULL."'), '".$toScape."', '".$scaped."' )";

      // NEW WAY
      $retStr = "REPLACE( " . $vo->table.".".$colKey.", '".$toScape."', '".$scaped."' )";
      $retStr = "case when " . $vo->table.".".$colKey." is null then '".COGUMELO_NULL."'  else ". $retStr. " END ";

      //$retStr = "CASE  WHEN ". $vo->table.".".$colKey." IS NULL THEN 'LOLAZO'  ELSE 'LOLAZO' END AS ". $vo->table.".".$colKey;

      // NEWEST WAY
      //$retStr = "COALESCE(" . $vo->table.".".$colKey.", '".COGUMELO_NULL."')";

    }

    return $retStr;
  }

  /**
   * Group Concat function
   *
   * @param object $vo VO or Model relationship object (From tmp files)
   *
   * @return string
   */
  public function getGroupConcats($vo) {
    $groupConcats = '';

    foreach( $vo->relationship as $voRel) {
      $groupConcats .= "',\"".$voRel->parentTable.".".$voRel->table."\": [', COALESCE( group_concat(".$voRel->parentId."_".$voRel->table."_serialized.".$voRel->table."),'' ) , ']',";
    }

    return $groupConcats;
  }


  public function searchVOInFilters($voName) {
    $found = array();


    if( count($this->filters)>0 && $this->filters !== false ) {
      //var_dump($this->filters);
      foreach ($this->filters as $fK => $fD) {
        preg_match('#'.$voName.'\.(.*)#', $fK, $matches);
        if( count($matches)>0 ) {
          $found[ $matches[1] ] = $fD;
        }

      }
    }

    return $found;
  }

  public function setWheres( $voName ) {

    $where_str = "";
    $val_array = array();


    $thisVOFilterValues = $this->searchVOInFilters( $voName );

    if( array($thisVOFilterValues) > 0) {

      eval( '$getVOFilters = '.$voName.'::getFilters();' );



      $thisVOFilterArray = MysqlAutogeneratorDAO::filtersAsMysql( $getVOFilters );

      foreach($thisVOFilterValues as $fkey => $filter_val) {

        if( array_key_exists($fkey, $thisVOFilterArray) ) {
          $fstr = " AND ".$thisVOFilterArray[$fkey];
        }
        else {
          Cogumelo::error( $fkey." not found on wherearray or into (".$voName.") VO in Relationship. Omiting..." );
        }

        // where string
        $where_str.=$fstr;


        // dump value or array value into $values array
        if( is_array($filter_val) ) {
          foreach($filter_val as $val) {
            $val_array[] = $val;
          }
        }
        else {
          $var_count = mb_substr_count( $fstr , "?");
          for( $c=0; $c < $var_count; $c++ ) {
            $val_array[] = $filter_val;
          }
        }

      }

    }


    $fArray = array(
        'string' => " WHERE true ".$where_str,
        'values' => $val_array
    );

    $this->whereData[] = $fArray;

    return $fArray['string'];
  }

  public function getFilterArrays() {

    return $this->whereData;
  }

}
