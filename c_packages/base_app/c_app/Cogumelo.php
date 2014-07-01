<?php


class Cogumelo extends CogumeloClass
{

  function __construct()
  {
    parent::__construct();

  }

  var $url_patterns = array(
    /*
    '#^cousa/mostrar\/?(.*)$#' => 'view:Cousadmin::mostra_cousa',
    '#^cousa/crear$#' => 'view:Cousadmin::crea',
    '#^cousa$#' => 'view:Cousadmin::lista',
*/
    //'#^admin\/?(.*)$#' => 'view:Adminview::metodo',
    //'#^dev$#' => 'view:DevView::main',
    // default views

    '#^getobj$#' => 'view:Adminview::getobj',
    '#^setobj$#' => 'view:Adminview::setobj',

    '#^404$#' => 'view:MasterView::page404',
    '#^$#' => 'view:MasterView::master' // App home url
  );

}
