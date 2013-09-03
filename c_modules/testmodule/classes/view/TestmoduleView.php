<?php

Cogumelo::load('c_view/View');
testmodule::load('controllers/ola');
class TestmoduleView extends View
{

	function __construct($base_dir){
		parent::__construct($base_dir);
	}

	function accessCheck() {
		return true;
	}

	function inicio() {
		$this->template->setTpl("test.tpl", 'testmodule');
		$this->template->addCss("css/common.css", 'testmodule');
		$this->template->addCss("css/common2.css", 'testmodule');
		$this->template->exec();
	}
}