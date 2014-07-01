<?php

Cogumelo::load("c_controller/Module");

class testmodule extends Module
{

  function __construct() {
    $this->addUrlPatterns( '#^cousa/mostrar\/?(.*)$#', 'view:Cousadmin::mostra_cousa' );
    $this->addUrlPatterns( '#^cousa/crear$#', 'view:Cousadmin::crea' );
    $this->addUrlPatterns( '#^lista_plana$#', 'view:Cousadmin::lista_plana' );
    $this->addUrlPatterns( '#^cousa_tabla$#', 'view:Cousadmin::cousa_tabla' );
    $this->addUrlPatterns( '#^testmodule#', 'view:TestmoduleView::inicio' );
/*
$this->setUrlPatternsFromArray(
  array(
    '#^cousa/mostrar\/?(.*)$#' => 'view:Cousadmin::mostra_cousa',
    '#^cousa/crear$#' => 'view:Cousadmin::crea',
    '#^lista_plana$#' => 'view:Cousadmin::lista_plana',
    '#^cousa_tabla$#' => 'view:Cousadmin::cousa_tabla',
    '#^testmodule#' => 'view:TestmoduleView::inicio'
  )
);
*/
  }


}