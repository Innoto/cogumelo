<?php

Cogumelo::load('c_controller/DataController');


//
// DevelUtilsDB Controller Class
//
class  DevelDBController extends DataController
{
	var $data;

	var $voReferences = array();


	function __construct($usuario=false, $password = false, $DB = false)
	{	
		$this->data = new Facade("DevelDB", "devel");

		if($usuario) {
			$this->data->develMode($usuario, $password, $DB);
		}
	}

	
	function createTables(){

		$returnStrArray = array();
		foreach($this->listVOs() as $vo) {
			$returnStrArray[] = $this->data->dropTable($vo);
			$returnStrArray[] = $this->data->createTable($vo);
		}

		return $returnStrArray;
	}


	function getTablesSQL(){
		$returnStrArray = array();

		foreach($this->listVOs() as $vo) {
			$returnStrArray[] = "#VO File: ".$this->voReferences[$vo].$vo.".php";
			$returnStrArray[] = $this->data->getDropSQL($vo);
			$returnStrArray[] = $this->data->getTableSQL($vo);

		}

		return $returnStrArray;
	}

	function listVOs() {
		$voarray = array();

		// VOs iinto application
		$voarray = array_merge($voarray, $this->scanVOs( SITE_PATH.'classes/model/') ) ; // scan app model dir

		// VOs from Module
		global $C_ENABLED_MODULES;
		foreach($C_ENABLED_MODULES as $modulename) {
			$voarray = array_merge($voarray, $this->scanVOs( SITE_PATH.'../modules/'.$modulename.'/classes/model/'));
			$voarray = array_merge($voarray, $this->scanVOs( COGUMELO_LOCATION.'/c_modules/'.$modulename.'/classes/model/'));
		}

		return array_unique($voarray);
	}

	function scanVOs($dir) {
		//cogumelo::debug($dir);
		$vos = array();


		if(!file_exists($dir))
			return $vos;

		// VO's from APP
		if ($handle = opendir( $dir )) {
			while (false !== ($file = readdir($handle))) {
			    if ($file != "." && $file != "..") {
			    	if(substr($file, -6) == 'VO.php'){
			    		$class_vo_name = substr($file, 0,-4);

			    		// prevent reload an existing vo in other place
			    		if (!class_exists($class_vo_name)) {
				        	require_once($dir.$file);
				        	$vos[] =  $class_vo_name;
				        	$this->voReferences[$class_vo_name] = $dir;
				        }
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
