<?php


Class DependencesController {


  //
  //  Vendor lib resolution
  //
  var $allDependencesComposer = array();
  var $allDependencesBower = array();
  var $allDependencesManual = array();

  public function installDependences() {

    Cogumelo::load('coreController/ModuleController.php');

    $this->loadDependences();

    $this->installDependencesBower($this->allDependencesBower);
    $this->installDependencesComposer($this->allDependencesComposer);
    $this->installDependencesManual($this->allDependencesManual);
  }


  public function loadDependences() {

    $moduleControl = new ModuleController(false, true);

    //Cargamos las dependencias de los modulos
    global $C_ENABLED_MODULES;
    foreach ( $C_ENABLED_MODULES as $mod ){
      $modUrl = ModuleController::getRealFilePath( $mod.".php" , $mod );
      require_once($modUrl);
      eval('class extClass'. $mod .' extends '.$mod. '{}');
      eval('$objMod'.$mod.' = new extClass'.$mod.'();');
      eval('$dependences = $objMod'.$mod.'->dependences;');

      $this->pushDependences($dependences);
    }

    //Cargamos dependencias de Cogumelo class
    $this->pushDependences(Cogumelo::$mainDependences);

    //Cargamos las dependencias de Base App (externas a los modulos).
    global $_C;
    $this->pushDependences($_C->dependences);
  }


  public function pushDependences( $dependences ) {
    //Hacemos una lista de las dependecias de todos los modulos
    foreach ( $dependences as $dependence ){

      //Diferenciamos entre instaladores
      switch($dependence['installer']){
        case "composer":
          $this->pushDependencesComposer ($dependence);
        break;
        case "bower":
          $this->pushDependencesBower ($dependence);
        break;
        case "manual":
          $this->pushDependencesManual ($dependence);
        break;
      }
    }   // end foreach
  }


  public function pushDependencesComposer( $dependence ) {

    if(!array_key_exists($dependence['id'], $this->allDependencesComposer)){
      $this->allDependencesComposer[$dependence['id']] = array($dependence['params']);
    }
    else{
      $diffAllDepend = array_diff($dependence['params'] , $this->allDependencesComposer[$dependence['id']][0]);

      if(!empty($diffAllDepend)){
        array_push($this->allDependencesComposer[$dependence['id']], array_diff($dependence['params'] , $this->allDependencesComposer[$dependence['id']][0])  );
      }
    }
  }


  public function pushDependencesBower( $dependence ) {
    if(!array_key_exists($dependence['id'], $this->allDependencesBower)){
      $this->allDependencesBower[$dependence['id']] = array($dependence['params']);
    }
    else{
      $diffAllDepend = array_diff($dependence['params'] , $this->allDependencesBower[$dependence['id']][0]);

      if(!empty($diffAllDepend)){
        array_push($this->allDependencesBower[$dependence['id']], array_diff($dependence['params'] , $this->allDependencesBower[$dependence['id']][0])  );
      }
    }
  }


  public function pushDependencesManual( $dependence ) {
    if(!array_key_exists($dependence['id'], $this->allDependencesManual)){
      $this->allDependencesManual[$dependence['id']] = array($dependence['params']);
    }
    else{
      $diffAllDepend = array_diff($dependence['params'] , $this->allDependencesManual[$dependence['id']][0]);

      if(!empty($diffAllDepend)){
        array_push($this->allDependencesManual[$dependence['id']], array_diff($dependence['params'] , $this->allDependencesManual[$dependence['id']][0])  );
      }
    }
  }


  public function installDependencesBower( $dependences ) {
    echo "\n === Bower dependences ===\n\n";

    if( !is_dir( DEPEN_BOWER_PATH ) ) {
      if( !mkdir( DEPEN_BOWER_PATH, 0755, true ) ) {
        echo "The destination folder does not exist and have permission to create \n";
      }
    }

    $jsonBowerRC = '{ "directory": "'.DEPEN_BOWER_PATH.'", '.
      ' "json": "'. PRJ_BASE_PATH . '/bower.json" }';
    $fh = fopen( PRJ_BASE_PATH . '/.bowerrc', 'w' );
      fwrite( $fh, $jsonBowerRC );
    fclose( $fh );

    $jsonBower = '{ "name": "cogumelo", "version": "1.0a", '.
      ' "homepage": "https://github.com/Innoto/cogumelo", "license": "GPLv2", "dependencies": {} }';
    $fh = fopen( PRJ_BASE_PATH . '/bower.json', 'w' );
      fwrite( $fh, $jsonBower );
    fclose( $fh );

    foreach( $dependences as $depKey => $dep ){
      foreach( $dep as $params ) {
        if( count($params) > 1 ) {
          $allparam = "";
          foreach( $params as $p ) {
            $allparam = $allparam." ".$p;
          }
        }
        else {
          $allparam = $params[0];
        }
        echo( "Exec... bower install ".$depKey."=".$allparam." --save\n" );
        exec( 'cd '.PRJ_BASE_PATH.' ; bower install '.$depKey.'='.$allparam.' --save' );
      } // end foreach
    } // end foreach

    echo "\n === Bower dependences: Done ===\n\n";
  }


  public function installDependencesComposer( $dependences ) {
    echo "\n === Composer dependences ===\n\n";

    if( !is_dir( DEPEN_COMPOSER_PATH ) ) {
      if( !mkdir( DEPEN_COMPOSER_PATH, 0755, true ) ) {
        echo "The destination folder does not exist and have permission to create \n";
      }
    }

    $finalArrayDep = array( "require" => array(), "config" => array( "vendor-dir" => DEPEN_COMPOSER_PATH ) );
    foreach( $dependences as $depKey => $dep ){
      foreach( $dep as $params ){
        $finalArrayDep['require'][$params[0]] = $params[1];
      }
    }

    $jsonencoded = json_encode( $finalArrayDep );
    $fh = fopen( PRJ_BASE_PATH . '/composer.json', 'w' );
      fwrite( $fh, $jsonencoded );
    fclose( $fh );

    echo("Exec... php composer.phar update\n\n");
    exec('cd '.PRJ_BASE_PATH.' ; php composer.phar update');
    echo("If the folder does not appear vendorServer dependencies run 'php composer.phar update' or 'composer update' and resolves conflicts.\n");

    echo "\n === Composer dependences: Done ===\n\n";
  }


  public function installDependencesManual( $dependences ) {
    echo "\n === Manual dependences ===\n\n";

    if( !is_dir( DEPEN_MANUAL_PATH ) ) {
      if( !mkdir( DEPEN_MANUAL_PATH, 0755, true ) ) {
        echo "The destination folder does not exist and have permission to create \n";
      }
    }

    foreach( $dependences as $depKey => $dep ){
      foreach( $dep as $params ) {
        echo "Installing ".$params[0]."\n";
        $manualCmd = 'cp -r '.DEPEN_MANUAL_REPOSITORY.'/'.$params[0].' '.DEPEN_MANUAL_PATH.'/';
        exec( $manualCmd );
      }
    }

    echo "\n === Manual dependences: Done ===\n\n";
  }


  //
  //  Includes
  //


  public function loadModuleIncludes( $moduleName ) {
    Cogumelo::load('coreController/ModuleController.php');
    ModuleController::getRealFilePath( $moduleName.'.php', $moduleName );

    //$this->loadCogumeloIncludes();

    $moduleInstance = new $moduleName();

    //$this->addVendorIncludeList( $moduleInstance->dependences );
    $dependences = array_filter( $moduleInstance->dependences,
      function( $dep ) {
        return( !isset( $dep['autoinclude'] ) || $dep['autoinclude'] !== false );
      }
    );
    //error_log( 'DependencesController::loadModuleIncludes : ' . print_r( $dependences, true ) );
    $this->addVendorIncludeList( $dependences );

    $this->addIncludeList( $moduleInstance->includesCommon, $moduleName );
  }


  public function loadModuleDependence( $moduleName, $idDependence, $installer = false ) {
    Cogumelo::load('coreController/ModuleController.php');
    ModuleController::getRealFilePath( $moduleName.'.php', $moduleName );

    $moduleInstance = new $moduleName();

    $dependences = array_filter( $moduleInstance->dependences,
      function( $dep ) use ( $idDependence, $installer ) {
        return( $dep['id'] === $idDependence && ( $installer === false || $dep['installer'] === $installer ) );
      }
    );
    //error_log( 'DependencesController::loadModuleDependence' . print_r( $dependences, true ) );
    $this->addVendorIncludeList( $dependences );
  }


  public function loadAppIncludes() {
    global $_C;

    //$this->loadCogumeloIncludes();
    $this->addVendorIncludeList( $_C->dependences );
    $this->addIncludeList( $_C->includesCommon );
  }


  public function loadCogumeloIncludes() {

    $this->addVendorIncludeList(CogumeloClass::$mainDependences);
  }



  public function addVendorIncludeList( $includes ) {
    if( count( $includes ) > 0) {

      foreach( $includes as $includeElement ) {

        $include_folder = '';

        if( $includeElement['installer'] == 'bower' ) {
          $installer = 'bower';
          $include_folder = $includeElement['id'];
        }
        else if( $includeElement['installer'] == 'composer' ) {
          $installer = 'composer';
          $include_folder = $includeElement['params'][0];
        }
        else if( $includeElement['installer'] == 'manual' ) {
          $installer = 'manual';
          $include_folder = $includeElement['params'][0];
        }

        if( isset( $includeElement['includes'] ) && count( $includeElement['includes'] ) > 0 ) {
          foreach( $includeElement['includes'] as $includeFile ) {

            switch ($this->typeIncludeFile( $includeFile )) {
              case 'serverScript':
                //Cogumelo::debug( 'Including vendor:'.SITE_PATH.'../httpdocs/vendorServer/'.$include_folder.'/'.$includeFile );
                require_once( DEPEN_COMPOSER_PATH.'/'.$include_folder.'/'.$includeFile );
                break;
              case 'clientScript':

                $this->addIncludeJS( $include_folder.'/'.$includeFile, 'vendor/'.$installer );

                break;
              case 'styles':
                $this->addIncludeCSS( $include_folder.'/'.$includeFile, 'vendor/'.$installer );
                break;
            }
          }
        }
      }
    }
  }



  public function addIncludeList( $includes, $module = false ) {

    if( count( $includes ) > 0) {
      foreach( $includes as $includeFile ) {

        switch($this->typeIncludeFile( $includeFile ) ) {
          case 'serverScript':
            if($module == false) {
              Cogumelo::load($includeFile);
            }
            else {
              eval($module.'::load("'. $includeFile .'");');
            }
            break;
          case 'clientScript':
            $this->addIncludeJS( $includeFile, $module );
            break;
          case 'styles':
            $this->addIncludeCSS( $includeFile, $module );
            break;
        }
      }
    }
  }


  public function typeIncludeFile( $includeFile ) {

    $type = false;

    if( $includeFile != '' ) {
      // css or less file
      if( substr($includeFile, -4) == '.css' || substr($includeFile, -5) == '.less') {
        $type = 'styles';
      }
      // javascript file
      else if( substr($includeFile, -3) == '.js' ) {
        $type = 'clientScript';
      }
      // php include
      else if( substr($includeFile, -4) == '.php' || substr($includeFile, -4) == '.inc')  {
        $type = 'serverScript';
      }
    }

    return $type;
  }


  public function addIncludeCSS( $includeFile, $module = false ) {
    global $cogumeloIncludesCSS;

    if( !isset( $cogumeloIncludesCSS ) ) {
      $cogumeloIncludesCSS = array();
    }

    if( !$this->isInIncludesArray($includeFile, $cogumeloIncludesCSS) ) {
      array_push($cogumeloIncludesCSS, array('src'=>$includeFile, 'module'=>$module ) );
    }
  }


  public function addIncludeJS( $includeFile, $module = false ) {
    global $cogumeloIncludesJS;

    if( !isset( $cogumeloIncludesJS ) ) {
      $cogumeloIncludesJS = array();
    }

    if( !$this->isInIncludesArray($includeFile, $cogumeloIncludesJS) ) {
      array_push($cogumeloIncludesJS, array('src'=>$includeFile, 'module'=>$module ) );
    }
  }


  public function isInIncludesArray( $file, $includesArray ) {
    $ret = false;

    if( count( $includesArray ) > 0 ) {
      foreach( $includesArray as $includedFile ) {
        if($includedFile['src'] == $file ) {
          $ret = true;
        }
      }
    }

    return $ret;
  }

}