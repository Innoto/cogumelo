<?
Cogumelo::load("c_controllers/module/Module");

class i18nGetLang extends Module
{

	function __construct(){

	}

	var $url_patterns = array(
		'/^(es\/?)(.*)/' => 'noendview:GetLang::setlang',
	);
}