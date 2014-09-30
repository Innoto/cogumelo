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
* RequestController Class
*
* this is the controller that cogumelo class and modules sys use to manage http requests
*
* @author: pablinhob
*/
class RequestController
{
  var $url_path;
  var $include_base_path;
  var $leftover_url = "";
  var $is_last_request = false;
  var $urlPatterns = array();


  function __construct($urlPatterns, $url_path, $include_base_path = false) {
    $this->urlPatterns = $urlPatterns;
    $this->url_path = $url_path;

    if($include_base_path) {
      $this->include_base_path = $include_base_path;
    }
    else {
      $this->include_base_path = SITE_PATH;
      $this->is_last_request = true; // is last request on app
    }

    $this->exec();
  }


  function exec() {
    foreach($this->urlPatterns as $url_pattern_key => $url_pattern_action){
      if(preg_match( $url_pattern_key, $this->url_path, $m_url) ) {
        if(array_key_exists(1, $m_url)) {
          $this->readPatternAction($m_url[1], $url_pattern_action);
        }
        else {
          $this->readPatternAction("", $url_pattern_action);
        }
        if(array_key_exists(2, $m_url)) {
          $this->leftover_url = $m_url[2];
        }
        return;
      }
    }

    // if is last request and any pattern found
    if( $this->is_last_request ) {
      Cogumelo::error("URI not found ".$_SERVER['REQUEST_URI']);
      self::redirect(SITE_URL_CURRENT.'404');
    }
    else {
      $this->leftover_url = $this->url_path;
    }
  }


  private function readPatternAction($url_path, $url_pattern_action) {
    if(preg_match('^(redirect:)(.*)^', $url_pattern_action, $m)) {
      self::redirect($m[2]);
    }
    else if(preg_match('^(noendview:)(.*)^', $url_pattern_action, $m)) {
      $this->leftover_url = $url_path;
      $this->view($url_path ,$m[2]);
    }
    else if(preg_match('^(view:)(.*)^', $url_pattern_action, $m)) {
      $this->leftover_url = "";
      $this->view($url_path ,$m[2]);
      exit;
    }
    else {
      Cogumelo::error(__METHOD__." error. No valid pattern = '$matched'");
    }
  }


  public static function redirect($redirect_url) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: {$redirect_url}");
    exit;
  }


  function view($url_path, $view_reference) {
    preg_match('^(.*)::(.*)^', $view_reference,$m);
    $classname = $m[1];
    $methodname = $m[2];
    // require class script from views folder
    include( $this->include_base_path .'/classes/view/'. $classname.'.php' );

    eval( '$current_view = new '.$classname.'( $this->include_base_path );' );

    if($url_path == '') {
      eval('$current_view->'.$methodname.'();');
    }
    else {
      eval('$current_view->'.$methodname.'("'.$url_path.'");');
    }
  }


  function getLeftoeverUrl() {
    return $this->leftover_url;
  }


}
