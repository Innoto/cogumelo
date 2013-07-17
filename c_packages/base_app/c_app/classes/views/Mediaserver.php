<?

Cogumelo::load('c_view/View');


class Mediaserver extends View
{

	function __construct($base_dir){
		parent::__construct($base_dir);
	}

	function accessCheck() {
		return true;
	}

	function application($url_path=''){
		if($url_path == ''){
			Cogumelo::error('Mediaserver received empty request');
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}
		$this->serveContent($url_path);
	}

	function module($url_path='') {

		global $C_ENABLED_MODULES;

		$url_exp = explode('/', $url_path);
		if(in_array($url_exp[1], $C_ENABLED_MODULES )) {
			$this->serveContent(str_replace($url_exp.'/', '', $url_path), $url_exp[1]);
		}
		else {
			Cogumelo::error('Module named as "'.$url_exp[1].'" is not enabled. Add it to $C_ENABLED_MODULES setup.php array' );
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}
	}

	function serveContent($path, $module=false){

		if( substr($path, -3) == '.js' ) {
			$this->serveJS($path, $module);
		}
		else
		if( substr($path, -4) == '.tpl' || substr($path, -4) == '.php' || substr($path, -4) == '.php' ) {
			Cogumelo::error('Mediaserver module not allowed to serve .tpl .php or .php files ');
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}
		else {
			$this->serveAnyFile($path, $module);
		}

	}


	function serveJS($js_path, $module=false){
		$this->template->setTpl($js_path, $module);
		$this->template->exec();
	}

	function serveAnyFile($file_path, $module=false) {

		if(file_exists(SITE_PATH.'templates/'.$file_path) && $module == false) {
			$ful_path_file = SITE_PATH.'templates/'.$file_path;
		}
		else
		if( file_exists(SITE_PATH.'/modules/'.$module.'/templates/'.$file_path) ) { //check if exist on app module
			$ful_path_file = SITE_PATH.'/modules/'.$module.'/templates/'.$file_path;
		}
		else
		if( file_exists( COGUMELO_LOCATION.'/modules/'.$module.'/templates/'.$file_path ) ) { //check if exist on core module
			$ful_path_file = COGUMELO_LOCATION.'/modules/'.$module.'/templates/'.$file_path;
		}
		else {
			Cogumelo::error("file: '".$file_path."'' not found" );
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}


		header("Content-Type: application/octet-stream");
		set_time_limit(0);
		$file = @fopen($ful_path_file,"rb");
		while(!feof($file))
		{
			print(@fread($file, 1024*8));
			ob_flush();
			flush();
		}
	}
}
