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
* Cache Class
*
* This class encapsulates the memcached library
*
* @author: pablinhob
*/


class Cache {


  var $mc = false;

  function __construct() {
    $this->mc = new Memcached();
    
    global $MEMCACHED_HOST_ARRAY;
    foreach( $MEMCACHED_HOST_ARRAY as $host) {
      $this->mc->addServer($host['host'], $host['port']);
    }

  }

  /*
  * @param string $query is the query string
  * @param array $variables the variables for the query prepared statment
  */
  function getCache($query){
    return $this->mc->get( $query); 
  }


  /*
  * @param string $query is the query string
  * @param array $variables the variables for the query prepared statment
  */
  function setCache($query, $data){
    return $this->mc->set( $query, $data, MEMCACHED_EXPIRATION_TIME); 
  }

}




