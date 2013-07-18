<?php


Cogumelo::load('c_view/View');
Cogumelo::load('c_view/Table');
testmodule::load('controllers/data/CousaController');


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


	    $this->template->setTpl("cousadmin.tpl", "testmodule");

	    $this->template->addJs('vendor_lib/jQuery.js', 'client_essentials');
	    $this->template->addJs('vendor_lib/Class.js', 'client_essentials');
	    $this->template->addJs('vendor_lib/jquery.address.js', 'client_essentials');
	   	$this->template->addJs('lib/cogumelo.table.js', 'client_essentials');
	    $this->template->exec();
		}




	//
	// Actions
	//

	function mostra_cousa($url = false) {

	var_dump($this->cousacontrol->find($url) );

	}

	function crea() {

		$novacousa = array('name'=> 'Cousa Adams', 'fingers' => 5,'hobby' => 'tocar o piano');

		$this->cousacontrol->create($novacousa);

		echo "Creado nova entrada para cousa";
	}


	// 
	//	Tablas
	//


	function cousa_tabla() {

		$FAKE_POST = array('cogumelo_table' =>
				'{"filters_common":[],"filters":[],"range":[1,20], "order":[], "method":false}' );


		$tabla = new Table($FAKE_POST);

		// set col names
		$tabla->setCol('id', 'Id');
		$tabla->setCol('name', 'Nome da cousa');
		$tabla->setCol('fingers', "Númerod de dedos");

		// print table json
		$tabla->return_table_json($this->cousacontrol);

	}


}

