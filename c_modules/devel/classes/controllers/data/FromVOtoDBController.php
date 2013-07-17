<?php

Cogumelo::load('c_controllers/data/DataController');


//
// Useradmin Controller Class
//
class  FromVOtoDBController extends DataController
{
	var $data;

	var $voClasses = array();


	function __construct($usuario, $password)
	{	
		$this->data = new Facade("FromVOtoDB", "devel");
		$this->data->develMode($usuario, $password);
	}

	
	function createTables(){

		$returnStr = "";
		foreach($this->listVOs() as $vo) {
			$this->data->dropTable($vo);
			$returnStr .= $this->data->createTable($vo);
		}

		return $returnStr;
	}


	function getTablesSQL(){

		$returnStr = "";
		foreach($this->listVOs() as $vo) {
			$returnStr .= $this->data->getTableSQL($vo);
		}

		return $returnStr;
	}

	function listVOs() {
		$voarray = array();

		// VOs iinto application
		$voarray = array_merge($voarray, $this->scanVOs(SITE_PATH.'classes/model/')); // scan app model dir

		// VOs from Module

		return $voarray;

	}
	function scanVOs($dir) {
		$vos = array();

		if ($handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != "..") {
		        	if(substr($file, -6) == 'VO.php'){
		        		require_once($dir.$file);
		            	$vos[] = substr($file, 0,-4) ;
		            }
		        }
		    }
		    closedir($handle);
		}
		return $vos;
	}


	function createSchemaDB() {
		return $this->data->createSchemaDB();
	}

}