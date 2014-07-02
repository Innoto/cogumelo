<?php
/*
Cogumelo v0.5 - Innoto S.L.
Copyright (C) 2013 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

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

Cogumelo::load('c_view/Template');


abstract class View {
  var $first_execution = true;
  var $template;

  function __construct($teplates_dir) {
    if($this->first_execution) {

      $first_execution = false;

      $this->template = new Template($teplates_dir);

      if(!$this->accessCheck()){
        Cogumelo::error('Acess error on view '. get_called_class() );
        exit;
      }
      else {
        Cogumelo::debug('accessCheck OK '. get_called_class() );
      }
    }
  }

  /**
  * Evaluar las condiciones de acceso y reportar si se puede continuar
  * @return bool : true -> Access allowed
  */
  function accessCheck() {

    Cogumelo::error('Es necesario definir el método "accessCheck" en el View con los controles de'.
      ' restricción de acceso.');

    return false;
  }

}

