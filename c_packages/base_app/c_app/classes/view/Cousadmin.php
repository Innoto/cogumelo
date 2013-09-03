<?php


Cogumelo::load('c_view/View');
Cogumelo::load('controllers/data/CousaController');
Cogumelo::load('c_view/Table');


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
		//$cousas = $this->cousacontrol->listItems();


		$FAKE_POST = array('cogumelo_table' =>
				'{"ilters":[],"range":[1,20], "method":false}' );

		$tabla = new Table($FAKE_POST);

		// set col names
		$tabla->setCol('id', 'Id');
		$tabla->setCol('name', 'Nome da cousa');
		$tabla->setCol('fingers', "Númerod de dedos");


		$tabla->allowMethods(array('delete'));
		// print table json
		$tabla->return_table_json($this->cousacontrol);


/*		while($cou = $cousas->fetch()) {
			echo "<br>";
			var_dump($cou);
		}
*/

	}


	function mostra_cousa($url = false) {

	var_dump($this->cousacontrol->find($url) );

	}

	function crea() {

		$novacousa = array('name'=> 'Cousa Adams', 'fingers' => 5,'hobby' => 'tocar o piano');

		$this->cousacontrol->create($novacousa);

		echo "Creado nova entrada para cousa";
	}
}

