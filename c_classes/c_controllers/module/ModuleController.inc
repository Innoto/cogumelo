<?php 

require_once(COGUMELO_LOCATION."/c_classes/c_controllers/request/RequestController.inc");

class ModuleController
{

	var $url_path;
	var $module_paths = array();

	function __construct($url_path) {
		$this->url_path = $url_path;
		$this->setModules();
		foreach($this->module_paths as $mp_id => $mp) {
			// exec modulos
			$this->execModule( $mp_id );
		}
	}

	function setModules() {
		global $C_ENABLED_MODULES;
		
		if (!is_array($C_ENABLED_MODULES)) {
			return;
		}

		foreach ($C_ENABLED_MODULES as $module_name) {
			if( $module_main_class = self::getRealFilePath($module_name.'.inc' ,$module_name) ) {
				$this->module_paths[$module_name] = dirname($module_main_class); // get module.inc container
			}
			else {
				$this->module_paths[$module_name] = false;
				Cogumelo::error("Module not found: ".$module_name);
			}
			
		}
	}

	function execModule($module_name) {

		if($this->module_paths[$module_name] == false) {
			Cogumelo::error("Module '".$module_name. "' not found.");
		}
		else {
echo "aser";
			$mod_path = $this->module_paths[$module_name];

			require_once($mod_path.'/'.$module_name.'.inc');
			$modulo = new $module_name();

    		$this->request = new RequestController($modulo->url_patterns, $this->url_path,  $mod_path );

			$this->url_path = $this->request->getLeftoeverUrl();

			Cogumelo::debug("Module load ".$module_name);
		}
	}

	function getLeftUrl() {
		return $this->url_path;
	}



	static function getRealFilePath($file_relative_path, $module = false) {

		if(!$module) {
			return  SITE_PATH.$file_relative_path;
		}
		else{
			global $C_ENABLED_MODULES;

			if(in_array($module, $C_ENABLED_MODULES)) {

				if( file_exists(SITE_PATH.'/modules/'.$module.'/'.$file_relative_path) ) { //check if exist on app module
					return SITE_PATH.'/modules/'.$module.'/'.$file_relative_path;
				}
				else
				if( file_exists( COGUMELO_LOCATION.'/c_modules/'.$module.'/'.$file_relative_path ) ) { //check if exist on core module
					return  COGUMELO_LOCATION.'/c_modules/'.$module.'/'.$file_relative_path;
				}
				else {
					Cogumelo::error("ModuleController: '".$file_relative_path."'' not found into module '".$module."' ");
				}
			}
			else {
				Cogumelo::error('ModuleController: Module named as "'.$module.'" is not enabled. Add it to $C_ENABLED_MODULES setup.inc array' );
			}
		}
		return false;
	}



}