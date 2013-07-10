<?

Cogumelo::load('c_controllers/data/DataController');
Cogumelo::load('model/UseradminVO');


//
// Cousa Controller Class
//
class  CousaController extends DataController
{
	var $data;

	function __construct()
	{	
		$this->data = new Facade("Cousa");
		$this->voClass = 'CousaVO';
	}
}