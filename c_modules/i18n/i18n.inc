<?
Cogumelo::load("c_controllers/module/Module");

class i18n extends Module
{

	function __construct(){

	}

	var $url_patterns = array(
		'/^(es\/)(.*)/' => 'noendview:LangView::setlang',
	);
}