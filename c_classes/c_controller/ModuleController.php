<?php
/*
Cogumelo v1.0a - Innoto S.L.
Copyright (C) 2013 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@innoto.es>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
USA.

*/


/**
* ModuleController Class
*
* Controls all features of module system
* Learn more about modules in https://github.com/Innoto/cogumelo/wiki/Cogumelo-basics#wiki-modules
*
* @author: pablinhob
*/



require_once(COGUMELO_LOCATION."/c_classes/c_controller/RequestController.php");

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
			if( $module_main_class = self::getRealFilePath($module_name.'.php' ,$module_name) ) {
				$this->module_paths[$module_name] = dirname($module_main_class); // get module.php container
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
			$mod_path = $this->module_paths[$module_name];

			require_once($mod_path.'/'.$module_name.'.php');
			$modulo = new $module_name();


    		$this->request = new RequestController($modulo->url_patterns, $this->url_path,  $mod_path );


			$this->url_path = $this->request->getLeftoeverUrl();

			Cogumelo::debug("Module loaded: ".$module_name);
			
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
				Cogumelo::error('ModuleController: Module named as "'.$module.'" is not enabled. Add it to $C_ENABLED_MODULES setup.php array' );
			}
		}
		return false;
	}



}