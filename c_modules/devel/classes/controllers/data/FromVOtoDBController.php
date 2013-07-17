<?php

Cogumelo::load('c_controllers/data/DataController');


//
// Useradmin Controller Class
//
class  FromVOtoDBController extends DataController
{
	var $data;
	var $voClasses = array();


	function __construct($usuario=false, $password = false)
	{	
		$this->data = new Facade("FromVOtoDB", "devel");

		if($usuario) {
			$this->data->develMode($usuario, $password);
		}
	}

	
	function createTables(){

		$returnStrArray = array();
		foreach($this->listVOs() as $vo) {
			$this->data->dropTable($vo);
			$returnStrArray[] = $this->data->createTable($vo);
		}

		return $returnStrArray;
	}


	function getTablesSQL(){
		$returnStrArray = array();
		foreach($this->listVOs() as $vo) {
			$returnStrArray[] = $this->data->dropTable($vo);
			$returnStrArray[] = $this->data->getTableSQL($vo);
		}

		return $returnStrArray;
	}

	function listVOs() {
		$voarray = array();

		// VOs iinto application
		$voarray = $voarray = array_merge($voarray, $this->scanVOs( SITE_PATH.'classes/model/') ) ; // scan app model dir

		// VOs from Module
		global $C_ENABLED_MODULES;
		foreach($C_ENABLED_MODULES as $modulename) {
			$this->scanVOs( COGUMELO_LOCATION.'c_modules/'.$modulename.'/classes/model/');
			$this->scanVOs( SITE_PATH.'../modules/'.$modulename.'/classes/model/');
		}

		return $voarray;

	}
	function scanVOs($dir) {
		$vos = array();


		if(!file_exists($dir))
			return;

		// VO's from APP
		if ($handle = opendir( $dir )) {
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