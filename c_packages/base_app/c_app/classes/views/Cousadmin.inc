<?php


Cogumelo::load('c_view/View');
Cogumelo::load('controllers/data/CousaController');


class Cousadmin extends View
{

	var $cousacontrol;
	function __construct($base_dir){
		parent::__construct($base_dir);

		$this->cousacontrol = new CousaController();
	}

	function accessCheck() {
		return true;
	}


	function lista() {
		var_dump($this->cousacontrol->listCount());
	}


	function crea() {

		$novacousa = array('name'=> 'Cousa Adams', 'fingers' => 5,'hobby' => 'tocar o piano');

		echo "Creado nova entrada para cousa";
	}
}

