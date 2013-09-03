<?

Cogumelo::load('c_controllers/data/DataController');
testmodule::load('model/CousaVO');

//
// Cousa Controller Class
//
class  CousaController extends DataController
{
	var $data;

	function __construct()
	{	
		$this->data = new Facade("Cousa", "testmodule");
		$this->voClass = 'CousaVO';
	}
}