<?php

Cogumelo::load('c_view/View');
Cogumelo::load('c_vendor/jsmin/jsmin.php');
Cogumelo::load('c_controller/ModuleController');

class MediaserverView extends View
{

	function __construct($base_dir){
		parent::__construct($base_dir);
	}

	function accessCheck() {
		return true;
	}

	// load media from app
	function application($url_path=''){
		if($url_path == ''){
			Cogumelo::error('Mediaserver receives empty request');
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}
		$this->serveContent($url_path);
	}
	
	//load media from a module
	function module($url_path=''){
		
		preg_match('#/(.*?)/(.*)#', $url_path, $result);

		if( $result != array() ) {
			$this->servecontent($result[2], $result[1]);
		}
		else {
			Cogumelo::error('Mediaserver module receives empty request');
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}
			
	}

	function serveContent($path, $module=false){




		if(! $real_file_path = ModuleController::getRealFilePath('classes/view/templates/'.$path, $module ) ) {
			//RequestController::redirect(SITE_URL_CURRENT.'/404');
		}


		if( substr($path, -4) == '.tpl' || substr($path, -4) == '.php' || substr($path, -4) == '.inc' ) {
			Cogumelo::error('Somebody try to load, but not allowed to serve .tpl .php or .inc files ');
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}
		else {

			// if conf requires minify files
			if(MINIMIFY_FILES) {
				if(substr($path, -4) == '.css' ) {
					$this->serveMinifyCache('css', $real_file_path);
				}
				else
				if(substr($path, -3) == '.js') {
					$this->serveMinifyCache('js', $real_file_path);
				}
				else{
						$this->serveRawFile($real_file_path);
				}
			}
			else {
				Cogumelo::log($real_file_path);
				$this->serveRawFile($real_file_path);
			}			
		}
	}


	function serveRawFile($real_file_path) 
	{


		if( file_exists($real_file_path) ){

      if(substr($real_file_path, -4) == '.css') {
        header('content-type: text/css; charset=utf-8');
      }
      else
      if(substr($real_file_path, -3) == '.js') {
        header('content-type: text/js; charset=utf-8');
      }
      else
      if(substr($real_file_path, -4) == '.jpg' || substr($real_file_path, -5) == '.jpeg') {
        header('Content-Type: image/jpeg');
      }
      else
      if(substr($real_file_path, -4) == '.png') {
        header('Content-Type: image/png');
      }      
      else{
        header('Content-Type: application/octet-stream');
      }

      header('Content-Disposition: attachment; filename='.basename($real_file_path));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($real_file_path));
      ob_clean();
      flush();
      readfile($real_file_path);
      exit;
    } else {
			Cogumelo::error("Mediaserver couldn't load ".$cache_filename);
			RequestController::redirect(SITE_URL_CURRENT.'/404');
		}
	}


	function serveMinifyCache($type, $real_file_path){

		header("Content-Type: application/octet-stream");

        // creating secure name for cache file         
        $cache_filename = MINIMIFY_CACHE_PATH."/".str_replace('/','', $real_file_path);
		@$content =file_get_contents($cache_filename);

		if( ! $content ) {
			if($type == 'js'){
				$content = JSMin::minify(file_get_contents( $real_file_path ));
			}
			else
			if($type == 'css') {
				$content = CssMin::minify( file_get_contents( $real_file_path ));
			}

			if( $fp = fopen($cache_filename, 'w') ){
				if (flock($fp, LOCK_EX)) {  // acquire an exclusive lock
					fwrite($fp, $content);
					fflush($fp);            // flush output before releasing the lock
				    flock($fp, LOCK_UN); //unlock
				}
		         	fclose($fp);
	       	}
	       	else {
	       		Cogumelo::error('Cannot create cache file into '.MINIMIFY_CACHE_PATH.' for file '.$real_file_path);
	       	}
		}

		echo $content;
	}



}
