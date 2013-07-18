<?php

Cogumelo::load('c_model/mysql/MysqlDAO');

//
//	Mysql MysqlFromVOtoDB DAO
//

class MysqlFromVOtoDBDAO extends MysqlDAO
{

	static $conversion_types = array(
		'INT' => 'INT',
		'BIGINT' => 'BIGINT',
		'FLOAT' => 'FLOAT',
		'DATETIME' => 'DATETIME',
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

	function createSchemaDB($connection){

		$resultado =  array();

		$strSQL0 = "DROP DATABASE IF EXISTS ". DB_NAME ;
		$strSQL1 = "CREATE DATABASE ". DB_NAME ;
		$strSQL2 = "
					GRANT 
						SELECT, 
						INSERT, 
						UPDATE, 
						DELETE, 
						INDEX, 
						LOCK TABLES, 
						CREATE VIEW, 
						SHOW VIEW 
					ON ". DB_NAME .".* 
					TO '". DB_USER ."'@'localhost' IDENTIFIED BY '". DB_PASSWORD ."' ";

		$resultado[] = $this->execSQL($connection, $strSQL0, array() );
		$resultado[] = $this->execSQL($connection, $strSQL1, array() );
		$resultado[] = $this->execSQL($connection, $strSQL2, array() );

		return $resultado;
	}



	function dropTable($connection, $vo_name) {
		$this->execSQL($connection, $this->getDropSQL($connection, $vo_name) , array() );
	}

	function createTable($connection, $vo_name) {

		$strSQL = $this->getTableSQL($connection, $vo_name);
		$this->execSQL($connection, $strSQL, array() );

	}


	// Sql generation methods

	function getDropSQL($connection, $vo_name) {
		$vo= new $vo_name();

		$strSQL = $this->getTableSQL($connection, $vo_name);
		return "DROP TABLE IF EXISTS  ".$vo::$tableName.";";

	}

	function getTableSQL($connection, $vo_name){
		$VO = new $vo_name();

		$primarykeys = array();
		$autoincrements = array();
		$lines = array();

		foreach($VO::$cols as $colkey => $col) {
			$size = (array_key_exists('size', $col))? '('.$col['size'].') ': '';
			if(array_key_exists('primarykey', $col)){  
				$primarykeys[] = $colkey;
				$lines[] = '`'.$colkey.'` '.$this::$conversion_types[$col['type']].$size.' NOT NULL auto_increment ';
			}
			else{
				$lines[] = '`'.$colkey.'` '.$this::$conversion_types[$col['type']].$size;
			}
		}

		$primarykeys_str = (sizeof($primarykeys)>0)? ', PRIMARY KEY  USING BTREE (`'.implode(',',$primarykeys).'`)' : '';
		$strSQL = "CREATE TABLE ".$VO::$tableName." (\n".implode(" ,\n", $lines).' '.$primarykeys_str.')'." ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Generated with love and Cogumelo`s dev module';";
		
		return $strSQL;
	}
		
}