<?php
Cogumelo::load("c_controller/Module");

class i18nGetLang extends Module
{

	function __construct(){
    $this->addUrlPatterns( '#^(es/?)(.*)$#', 'noendview:GetLang::setlang' );
	}

}