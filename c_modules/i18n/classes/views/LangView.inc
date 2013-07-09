<?

Cogumelo::load('c_view/View');

class LangView extends View
{

	function __construct($base_dir){
		parent::__construct($base_dir);
	}

	function accessCheck() {
		return true;
	}

	// load media from app
	function setlang($url_path=''){
		echo "setlang";
	}
}