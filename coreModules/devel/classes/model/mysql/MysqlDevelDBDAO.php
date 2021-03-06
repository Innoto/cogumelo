<?php

Cogumelo::load('coreModel/mysql/MysqlDAO.php');

/**
 *  Mysql MysqlDevelDBDAO DAO
 */
class MysqlDevelDBDAO extends MysqlDAO {

  static $conversion_types = array(
    'TINYINT' => 'TINYINT',
    'SMALLINT' => 'SMALLINT',
    'INT' => 'INT',
    'BIGINT' => 'BIGINT',
    'FLOAT' => 'FLOAT',
    'DATETIME' => 'DATETIME',
    'TIMESTAMP' => 'TIMESTAMP',
    'BOOLEAN' => 'BIT',
    'CHAR' => 'CHAR',
    'VARCHAR' => 'VARCHAR',
    'TEXT' => 'TEXT',
    'LONGTEXT' => 'LONGTEXT',

    // GIS DATA
    'GEOMETRY' => 'GEOMETRY',
    'POINT' => 'POINT',
    'LINESTRING' => 'LINESTRING',
    'POLYGON' => 'POLYGON',
    'MULTIPOINT' => 'MULTIPOINT',
    'MULTILINESTRING' => 'MULTILINESTRING',
    'MULTIPOLYGON' => 'MULTIPOLYGON',
    'GEOMETRYCOLLECTION' => 'GEOMETRYCOLLECTION'
  );

  public function createSchemaDB( $connection ) {
    $resultado =  array();

    $strSQL0 = "DROP DATABASE IF EXISTS ". Cogumelo::getSetupValue( 'db:name' ) ;

    $strSQL1 = "CREATE DATABASE ". Cogumelo::getSetupValue( 'db:name' ).
      ' DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci';

    $strSQL2 = "GRANT ".
        "SELECT, ".
        "INSERT, ".
        "UPDATE, ".
        "DELETE, ".
        "INDEX, ".
        "LOCK TABLES, ".
        "ALTER,".
        "ALTER ROUTINE,".
        "CREATE, ".
        "DROP, ".
        "SHOW VIEW, ".
        "CREATE VIEW ".
      "ON ". Cogumelo::getSetupValue( 'db:name' ) .".* ".
      "TO '". Cogumelo::getSetupValue( 'db:user' ) ."'@'localhost' IDENTIFIED BY '". Cogumelo::getSetupValue( 'db:password' ) ."' ";

    $resultado[] = $this->execSQL( $connection, $strSQL0, array() );
    $resultado[] = $this->execSQL( $connection, $strSQL1, array() );
    $resultado[] = $this->execSQL( $connection, $strSQL2, array() );

    return $resultado;
  }

  public function aditionalExec( $connection, $strSQL, $noExecute = true   ) {
    $ret = false;


    if( $noExecute === false) {
      $ret = $this->rawExecSQL( $connection, $strSQL, array() );
    }
    else {
      echo $strSQL;
      $ret = true;
    }

    return $ret;
  }


  public function safeExecSQL(  $connection, $strSQL, $noExecute = true ) {
    $retSQL = false;
    if( $noExecute === false) {
      $this->execSQL( $connection, $strSQL0, array() );
    }
    else {
      echo $retSQL;
    }

  }

  public function checkTableExist( $connection, $vo_obj ) {
    return $this->aditionalExec($connection, 'DESCRIBE `'.$vo_obj::$tableName.'`;', false);
  }

  public function checkSQLTableExist( $connection, $tableName ) {
    return $this->aditionalExec($connection, 'DESCRIBE `'.$tableName.'`;', false);
  }

  public function dropTable( $connection, $vo_name, $noExecute = true  ) {
    $ret = false;

    if( $noExecute === false ) {
      $this->execSQL( $connection, $this->getDropSQL( $connection, $vo_name ) , array() );

    }
    else {
      $ret = $this->getDropSQL( $connection, $vo_name );

    }

    return $ret;
  }


  public function createTable( $connection, $vo_name, $noExecute = true ) {
    $ret = false;

    if( $noExecute === false ) {
      $this->execSQL( $connection, $this->getTableSQL( $connection, $vo_name ), array() );
    }
    else {
      $ret = $this->getTableSQL( $connection, $vo_name );
    }

    return $ret;
  }


  public function insertTableValues( $connection, $vo_name ){
    $res = $this->getInsertTableSQL( $connection, $vo_name );
    if( !empty($res) ) {
      foreach( $res as $resKey => $resValue ) {
        $this->execSQL( $connection, $resValue['strSQL'], $resValue['valuesSQL'] );
      }
    }
  }



  // Sql generation methods

  public function getDropSQL( $connection, $vo_name ) {
    $vo= new $vo_name();

    $strSQL = $this->getTableSQL($connection, $vo_name);
    return "DROP TABLE IF EXISTS  ".$vo::$tableName.";";
  }

  public function getTableSQL( $connection, $vo_name ) {
    $VO = new $vo_name();

    $primarykeys = array();
    $indexes = array();
    $uniques = array();
    $lines = array();

    foreach( $VO::$cols as $colkey => $col ) {

      // INDEXES
      if(
        $col['type'] == 'FOREIGN' ||
        ( isset($col['index']) && $col['index'] == true )) {
        $indexes[] = $colkey;
      }

      if( isset( $col['multilang'] ) && $col['multilang'] == true &&  $col['type'] != 'FOREIGN'  ) {

        foreach( array_keys(Cogumelo::getSetupValue( 'lang:available' )) as $langKey ) {

          $retMLC = $this->multilangCols( $colkey.'_'.$langKey, $col,  $primarykeys, $uniques, $lines );
          $primarykeys = $retMLC['primarykeys'];
          $uniques = $retMLC['uniques'];
          $lines = $retMLC['lines'];

        }
      }
      else {
        $retMLC = $this->multilangCols( $colkey, $col,  $primarykeys, $uniques, $lines );

        $primarykeys = $retMLC['primarykeys'];
        $uniques = $retMLC['uniques'];
        $lines = $retMLC['lines'];
      }

    }

    $uniques_str = ( count($uniques)>0 )? ', UNIQUE ('.implode(',',$uniques).')' : '';
    $primarykeys_str = ( count($primarykeys)>0 )? ', PRIMARY KEY  USING BTREE (`'.implode(',',$primarykeys).'`)' : '';
    $indexes_str = '';
    if( count($indexes) ) {
      foreach( $indexes as $index ) {
        $indexes_str .= ",INDEX  (`".$index."`)";
      }
    }
    $strSQL = "CREATE TABLE ".$VO::$tableName." (\n".implode(" ,\n", $lines).' '.$uniques_str.$primarykeys_str.$indexes_str.')'.
      " ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Generated by Cogumelo devel, ref:".$vo_name."';";

    return $strSQL;
  }


  public function multilangCols( $colkey, $col, $primarykeys, $uniques, $lines ) {

    $extrapkey = "";
    $type = "";
    $size = "";

    if( array_key_exists('default', $col ) && $col['default'] !== null ) {
      if( is_numeric($col['default']) ){
        $extrapkey = ' DEFAULT '.$col['default'].' ';
      }
      else {
        $extrapkey = " DEFAULT '".$col['default']."' ";
      }

    }
    if( array_key_exists('customDefault', $col ) && $col['customDefault'] !== null ) {
      $extrapkey = ' '.$col['customDefault'].' ';
    }
    else {
      // is primary key
      if( array_key_exists('primarykey', $col ) ) {
        $primarykeys[] = $colkey;
      }

      // is autoincrement
      if(  array_key_exists('autoincrement', $col ) ){
        $extrapkey=' NOT NULL auto_increment ';
      }

      // is unique
      if(  array_key_exists('unique', $col ) ){
        $uniques[] = $colkey;
      }
    }

    if( $col['type'] == "FOREIGN" ) { // is a foreign key
      eval( '$foreign_col = '.$col['vo'].'::$cols[\''.$col['key'].'\'];' );
      $type = $this::$conversion_types[$foreign_col['type']].$size;
    }
    else {
      $size = (array_key_exists('size', $col))? '('.$col['size'].') ': '';
      $type = $this::$conversion_types[$col['type']].$size;
    }

    $lines[] = '`'.$colkey.'` '.$type.$extrapkey;

    return array(
      'primarykeys' => $primarykeys,
      'uniques' => $uniques,
      'lines' => $lines
    );
  }




  public function getInsertTableSQL( $connection, $vo_name, $vo_route = false ) {
    $VO = new $vo_name();
    $primarykey = $VO->getFirstPrimarykeyId();
    $valuesSQL = array();
    $res = array();

    if( isset($VO::$insertValues) ){

      foreach( $VO::$insertValues as $insertKey => $insertValue ) {
        if( array_key_exists($primarykey, $insertValue) ) {
          $insertArrayValues = $insertValue;
          unset($insertArrayValues[$primarykey]);
          $insertStringValues = implode(',', array_keys($insertArrayValues));
          $valuesSQL = array_values($insertArrayValues);
          $infoSQLValues = implode(',', array_values($insertArrayValues));

          $strSQL = "INSERT INTO ".$VO::$tableName." (".$insertStringValues. ") VALUES (".$this->getQuestionMarks($insertArrayValues)."); ";
          $infoSQL = "INSERT INTO ".$VO::$tableName." (".$insertStringValues. ") VALUES (".$infoSQLValues."); ";

          array_push($res, array('strSQL' => $strSQL, 'valuesSQL' => $valuesSQL, 'infoSQL' => $infoSQL ));
        }
        else {
          $valuesSQL = array_values($insertValue);
          $infoSQLValues = implode(',', array_values($insertValue));
          $strSQL = "INSERT INTO ".$VO::$tableName." (".implode(',', array_keys($insertValue)). ") VALUES (".$this->getQuestionMarks($insertValue)."); ";
          $infoSQL = "INSERT INTO ".$VO::$tableName." (".implode(',', array_keys($insertValue)). ") VALUES (".$infoSQLValues."); ";
          array_push($res, array('strSQL' => $strSQL, 'valuesSQL' => $valuesSQL, 'infoSQL' => $infoSQL ));
        }
      }
    }
    return $res;
  }

}
