<?php
/*
Cogumelo v0.3 - Innoto S.L.
Copyright (C) 2012 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

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




// Project location
define('SITE_PATH', getcwd().'/../c_app/');

// cogumelo core Location
set_include_path('.:'.SITE_PATH);

if ( $_HOSTS['remote_host'] == '127.0.0.1' ) {
	require_once("conf/setup.dev.inc"); 
}
else {
	require_once("conf/setup.final.inc"); 
}
require_once(COGUMELO_LOCATION."/c_classes/CogumeloClass.inc");
require_once(SITE_PATH."/Cogumelo.inc");

global $_C;
$_C =Cogumelo::get();
$_C->exec();