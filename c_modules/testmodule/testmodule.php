<?php

Cogumelo::load("c_controller/Module");

class testmodule extends Module
{

	var $url_patterns = array(
					'#^cousa/mostrar\/?(.*)$#' => 'view:Cousadmin::mostra_cousa',
			'#^cousa/crear$#' => 'view:Cousadmin::crea',
			'#^cousa$#' => 'view:Cousadmin::lista',
      '#^tabla$#' => 'view:Cousadmin::cousa_tabla',
		'#^testmodule#' => 'view:TestmoduleView::inicio',
    
	);
}