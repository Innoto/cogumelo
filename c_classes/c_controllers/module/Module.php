<?php

Cogumelo::load('c_controllers/module/ModuleController');

class Module 
{

	// includes a file from current module
	//	Looks allways before on same path on module application 
	static function load($load_path) {
		$module_name = get_called_class();
		
		if($file_to_include =  ModuleController::getRealFilePath('classes/'.$load_path.'.php', $module_name)) {
			require_once($file_to_include);
		}
		else {
			Cogumelo::error("PHP File '".$load_path."'  not found in module : ".$module_name);
		}

	}

}