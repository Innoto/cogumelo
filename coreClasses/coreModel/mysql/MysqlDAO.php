<?php

Cogumelo::load('coreController/Cache.php');
Cogumelo::load('coreModel/DAO.php');
Cogumelo::load('coreModel/mysql/MysqlDAORelationship.php');
Cogumelo::load('coreModel/mysql/MysqlDAOResult.php');


/**
* Mysql DAO (Abstract)
*
* @package Cogumelo Model
*/
class MysqlDAO extends DAO {

  var $VO;


  /**
  * Composes order mysql (ORDER BY) String
  *
  * @param array $ORDArray array('id1'=>-1, 'id2'=>1)
  *
  * @return string
  */
  public function orderByString( $ORDArray ) {
    // Direction (ASC, DESC) Array
    if( is_array( $ORDArray ) ) {
      $orderSTR = " ORDER BY ";
      $coma = "";
      foreach( $ORDArray as $elementK => $elementV ) {
        if( !preg_match('/\s/',$elementK) ) {
          if( $elementV < 0 ) {
            $orderSTR .= $coma .$elementK." DESC";
          }
          else {
            $orderSTR .= $coma .$elementK." ASC";
          }
        }
        $coma=", ";
      }

      return $orderSTR;
    }
    else {
      return "";
    }
  }


  //
  //
  //

  /**
  * Execute a SQL query command
  *
  * @param object $connectionControl mysqli connection object
  * @param string $sql query
  * @param string $val_array value array
  *
  * @return mixed
  */
  public function execSQL( &$connectionControl, $sql, $val_array = array() ) {
    Cogumelo::debugSQL($sql);
    // obtaining debug data
    $d = debug_backtrace();

    if(
      isset( $d[1]['class'] ) &&
      isset( $d[1]['function'] )
    ){
      $caller_method = $d[1]['class'].'.'.$d[1]['function'].'()';
    }
    else {
      $caller_method = 'unknown';
    }



    //set prepare sql
    /*
    if( $connectionControl->stmt ) {
      mysqli_stmt_free_result ( $connectionControl->stmt );
    }
    while( $connectionControl->db->more_results() ) {
      echo "\nMysqlDAO rawExecSQL - TRAGADATOS\n";
      $connectionControl->db->next_result();
      var_dump( $connectionControl->db->use_result() );
    }
    */
    $connectionControl->stmt = $connectionControl->db->prepare( $sql );

    if( $connectionControl->stmt ) {  //set prepare sql

      $bind_vars_type = $this->getPrepareTypes($val_array);

      $bind_vars_str = "";
      foreach( $val_array as $ak => $vk ) {
        $bind_vars_str .= ', $val_array['.$ak.']';
      }


      // bind params
      if( $bind_vars_type !== '' ) {
        eval( '$connectionControl->stmt->bind_param("'. $bind_vars_type .'"'. $bind_vars_str .');' );
      }

      $connectionControl->stmt->execute();

      if( $connectionControl->stmt->error === '' ) {
        $ret_data = $connectionControl->stmt->get_result();
      }
      else {
        Cogumelo::error( "MYSQL STMT ERROR on ".$caller_method.": ".$connectionControl->stmt->error.' - '.$sql);
        $ret_data = COGUMELO_ERROR;
      }
    }
    else {
      Cogumelo::error( "MYSQL QUERY ERROR on ".$caller_method.": ".$connectionControl->db->error.' - '.$sql);
      $ret_data = COGUMELO_ERROR;
    }

    return $ret_data;
  }


  public function rawExecSQL( &$connectionControl, $sql ) {
    $ret = '';
    $connectionControl->db->multi_query( $sql );

    if( $connectionControl->db->error != ''){
      echo "Error executing rawExecSQL: ".$connectionControl->db->error;
      $ret =  COGUMELO_ERROR;
    }
    else {
      $ret = true;
      // Consumo los resultados sin guardarlos
      while( $connectionControl->db->more_results() ) {

        $connectionControl->db->next_result();
        $connectionControl->db->use_result();
      }
      $connectionControl->db->store_result();
    }

    return $ret;
  }


  /**
  * Get string of chars according prepare type (ex. i:integer, d:double, s:string, b:boolean)
  *
  * @param array $values_array
  *
  * @return string
  */
  public function getPrepareTypes( $values_array ) {

    $return_str = "";
    foreach( $values_array as $value ) {
      if(is_integer($value)) {
        $return_str.= 'i';
      }
      elseif(is_string($value)) {
        $return_str.= 's';
      }
      elseif(is_float($value)) {
        $return_str.= 'd';
      }
      elseif(is_bool($value)) {
        $return_str.= 'b';
      }
      elseif( $value === null ) {
        $return_str.= 's';
      }
    }

    return $return_str;
  }


  /**
  * Generates where clausule
  *
  * @param array $fiters
  *
  * @return string
  */
  public function getFilters( $filters ) {

    $where_str = "";
    $val_array = array();


    if($filters) {

      $VO = new $this->VO();


      foreach( $filters as $fkey => $filter_val ) {

        if( array_key_exists($fkey, $this->filters) ) {
          $fstr = " AND ".$this->filters[$fkey];


          $var_count = mb_substr_count( $fstr , '?' );
          for( $c=0; $c < $var_count; $c++ ) {

            if( is_array( $filter_val ) ) { // Value array for one filter
              foreach( $filter_val as $val ) {
                $val_array[] = $val;
              }


            }
            else { // one value
              $val_array[] = $filter_val;
            }

          }

          // create n '?' separed by comma to filter by array values
          if( is_array( $filter_val ) ) {
            $to_replace = implode(',', array_fill(0, count( $filter_val ), '?') );
            $fstr = str_replace('?', $to_replace, $fstr );
          }

          $where_str.=$fstr;

        }


      }
    }

    return array(
        'string' => " WHERE true".$where_str,
        'values' => $val_array
      );
  }


  /**
  * Generic List ittems
  *
  * @param object $connectionControl mysqli connection object
  * @param array $filters filters array
  * @param array $range query range
  * @param array $order order array
  * @param array
  * @param boolean $resolveDependences if want to resolve relationship dependences
  * @param boolean $cache save query result into cache
  *
  * @return object
  */
  public function listItems( &$connectionControl, $filters, $range, $order, $fields, $joinType, $resolveDependences = false, $groupBy = false, $cache = false ) {
    // SQL Query
    $VO = new $this->VO();

    // joins
    $mysqlDAORel = new MysqlDAORelationship();
    $joins = $mysqlDAORel->getVOJoins( $this->VO, $joinType , $resolveDependences, $filters);

    // where string for join queries
    $joinWhereArrays = $mysqlDAORel->getFilterArrays();

    // where string for main query
    $whereArray = $this->getFilters($filters);

    // merge where arrays and array values
    $allWhereArrays = $joinWhereArrays;
    $allWhereArrays[] = $whereArray;


    $allWhereARraysValues = array();
    foreach( $allWhereArrays as $wa ) {
      $allWhereARraysValues = array_merge( $allWhereARraysValues, $wa['values'] );
    }


    // order string
    $orderSTR = ($order)? $this->orderByString($order): "";

    // group by
    $groupBySTR = ($groupBy)?  " GROUP BY $groupBy": "";

    // range string
    $rangeSTR = ($range != array() && is_array($range) )? sprintf(" LIMIT %s, %s ", $range[0], $range[1]): "";


    if($resolveDependences) {
      $this->execSQL($connectionControl,'SET group_concat_max_len='.Cogumelo::getSetupValue( 'db:mysqlGroupconcatMaxLen' ).';');
    }

    $strSQL = "SELECT ".
      $this->getKeysToString($VO, $fields, $resolveDependences ) .
      " FROM `" .
      $VO::$tableName ."` " .
      $joins.
      $whereArray['string'] . $orderSTR . $rangeSTR . $groupBySTR .";";


//exit;
//var_dump($joinWhereArrays);
//exit;
    if ( $cache && DB_ALLOW_CACHE  )
    {
      $queryId = md5($strSQL.serialize( $allWhereArrays ));
      $cached =  new Cache();

      if($cache_data = $cached->getCache($queryId)  ) {
        // With cache, serving cache ...
        Cogumelo::debug('Using cache: cache Get with ID: '.$queryId );
        $queryID = $daoresult = new MysqlDAOResult( $this->VO , $cache_data, true); //is a cached result
      }
      else{
        // With cache, but not cached yet. Caching ...
        $res = $this->execSQL($connectionControl,$strSQL, $allWhereARraysValues );

        if( $res != COGUMELO_ERROR ) {
          Cogumelo::debug('Using cache: cache Set with ID: '.$queryId );
          $daoresult = new MysqlDAOResult( $this->VO , $res);
          $cached->setCache($queryId, $daoresult->fetchAll_RAW() );
        }
        else{
          $daoresult = COGUMELO_ERROR;
        }
      }
    }
    else {

      //  Without cache!
      $res = $this->execSQL($connectionControl,$strSQL, $allWhereARraysValues );

      if( $res != COGUMELO_ERROR ){
        $daoresult = new MysqlDAOResult( $this->VO , $res);
      }
      else{
        $daoresult = COGUMELO_ERROR;
      }
    }

    return $daoresult;


  }


  /**
  * Ket keys as string and set function AsText() to geo
  *
  * @return string
  */
  public function getKeysToString( $VO, $fields, $resolveDependences ) {

    $keys = explode(', ', $VO->getKeysToString($fields, $resolveDependences ));
    $procesedKeys = array();
    foreach( $keys as $key ) {

      $k1 = explode('.',$key);
      if( is_array( $k1 ) ){
        $k = end( $k1 );
      }
      else{
        $k = $key;
      }


      if( isset($VO::$cols[$k]) && $VO::$cols[$k]['type'] == 'GEOMETRY' ) {
        $procesedKeys[] = 'AsText('.$key.') as "'.$key.'" ';
      }
      else {
        $procesedKeys[] = $key;
      }
    }

//echo implode(',', $procesedKeys);
//exit;

    return implode(',', $procesedKeys);
  }


  /**
  * Generic List Count
  *
  * @param object $connectionControl mysqli connection object
  * @param array $filters filters array
  *
  * @return integer
  */
  public function listCount( &$connectionControl, $filters ) {
    $retVal = null;

    // where string and vars
    $whereArray = $this->getFilters($filters);

    // SQL Query
    $VO = new $this->VO();
    $StrSQL = "SELECT count(*) as number_elements FROM `" . $VO::$tableName . "` ".$whereArray['string'].";";

    $res = $this->execSQL($connectionControl,$StrSQL, $whereArray['values']);
    if( $res !== COGUMELO_ERROR )  {

        //$res->fetch_assoc();
        $row = $res->fetch_assoc();
        $retVal = $row['number_elements'];

    }
    else {
      $retVal = COGUMELO_ERROR;
    }


    return $retVal;
  }


  /**
  * Insert record
  *
  * @param object $connectionControl mysqli connection object
  * @param object $voObj VO or Model object
  *
  * @return mixed
  */
  public function create( &$connectionControl, $VOobj ) {

    $cols = array();
    foreach( $VOobj->data as $colk => $col ) {
      if( $VOobj->getter($colk) !== null) {
        $cols[$colk] = $col;
      }
    }


    $campos = '`'.implode('`,`', array_keys($cols)) .'`';


    $valArray = array();
    $answrs = "";
    foreach( array_keys($cols) as $colName ) {
      $val = $VOobj->getter($colName);
      $valArray[] = $val;


      if( $VOobj->getter($colName) != false && isset( $VOobj::$cols[$colName] ) && $VOobj::$cols[$colName]['type'] == 'GEOMETRY' ) {

        $answrs .= ', GeomFromText( ? )';
      }
      else {
        $answrs .= ', ?';
      }
    }

    $strSQL = "INSERT INTO `".$VOobj::$tableName."` (".$campos.") VALUES(".mb_substr($answrs,1).");";

    $res = $this->execSQL($connectionControl, $strSQL, $valArray);
    if($res != COGUMELO_ERROR) {
      $VOobj->setter($VOobj->getFirstPrimarykeyId(), $connectionControl->db->insert_id);

      return $VOobj;
    }
    else {
      return COGUMELO_ERROR;
    }
  }

  /**
  * Update record
  *
  * @param object $connectionControl mysqli connection object
  * @param object $voObj VO or Model object
  *
  * @return mixed
  */
  public function update( &$connectionControl, $VOobj ) {

    // primary key value
    $pkValue = $VOobj->getter( $VOobj->getFirstPrimarykeyId() );


    // add getter values to values array
    $setvalues = '';


    $valArray = array();
    foreach( $VOobj->data as $colk => $col ) {

      if( $VOobj->getter($colk) != false && isset( $VOobj::$cols[$colk] ) && $VOobj::$cols[$colk]['type'] == 'GEOMETRY' ) {
        $setvalues .= ', '.$colk.'= GeomFromText( ? ) ';
      }
      else {
        $setvalues .= ', '.$colk.'= ? ';
      }

      $valArray[] = $col;//$VOobj->getter($colk);
    }

//var_dump($setvalues);
//var_dump($valArray);

    // add primary key value to values array
    $valArray[] = $pkValue;

    $strSQL = "UPDATE `".$VOobj::$tableName."` SET ".mb_substr($setvalues, 1)." WHERE `".$VOobj->getFirstPrimarykeyId()."`= ?;";

    $res = $this->execSQL($connectionControl, $strSQL, $valArray);
    if( $res != COGUMELO_ERROR ) {
      return $VOobj;
    }
    else {
      return COGUMELO_ERROR;
    }
  }

  /**
  * Delete from key
  *
  * @param object $connectionControl mysqli connection object
  * @param string $key key to search
  * @param mixed $value value to search
  *
  * @return boolean
  */
  public function deleteFromKey( &$connectionControl, $key, $value ) {

    $VO = new $this->VO();
    // SQL Query
    $strSQL = "DELETE FROM `" . $VO::$tableName . "` WHERE `".$key."`=? ;";

    $res = $this->execSQL($connectionControl, $strSQL, array($value));
    if( $res != COGUMELO_ERROR ){
      return true;
    }
    else {
      return null;
    }
  }

  /**
  * Return list of question marks separated by comma
  *
  * @param array $elements
  *
  * @return string
  */
  public function getQuestionMarks( $elements ) {
    $qm = str_repeat( '?, ', count($elements)-1 ) . '?';
    return $qm;
  }

}
