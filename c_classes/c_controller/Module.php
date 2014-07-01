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
* Module Class
*
* This class is the abstract class that haritages the module classes
* Learn more about modules in https://github.com/Innoto/cogumelo/wiki/Cogumelo-basics#wiki-modules
*
* @author: pablinhob
*/


Cogumelo::load('c_controller/ModuleController');

class Module
{
  private $urlPatterns = array();


  /**
  * @param string $load_path the path of module
  */
	static function load($load_path) {
		$module_name = get_called_class();

		if($file_to_include =  ModuleController::getRealFilePath('classes/'.$load_path.'.php', $module_name)) {
			require_once($file_to_include);
		}
		else {
			Cogumelo::error("PHP File '".$load_path."'  not found in module : ".$module_name);
		}
	}


  function deleteUrlPatterns() {
    $this->urlPatterns = array();
  }

  function addUrlPatterns( $regex, $dest ) {
    $this->urlPatterns[ $regex ] = $dest;
  }

  function setUrlPatternsFromArray( $arrayUrlPatterns ) {
    $this->deleteUrlPatterns();
    foreach ($arrayUrlPatterns as $key => $value) {
      $this->addUrlPatterns( $key, $value );
    }
  }

  function getUrlPatternsToArray() {
    return $this->urlPatterns;
  }

}