<?php




Class DependencesController {


  //
  //  Vendor lib resolution
  //
  var $allDependencesComposer = array();
  var $allDependencesBower = array();

  function installDependences()
  {

    Cogumelo::load('c_controller/ModuleController');

    $this->loadDependences();    
    //Descomentar para ver las depen a instalar 
    //error_log( print_r( "ALLBOWER", true));
    //error_log( print_r( $allDependencesBower, true));
    //error_log( print_r( "ALLCOMPOSER", true));
    //error_log( print_r( $allDependencesComposer, true));

    $this->installDependencesBower($this->allDependencesBower);
    $this->installDependencesComposer($this->allDependencesComposer);    
  }
  
  function loadDependences(){

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
  
  
  
  function pushDependences($dependences)
  {
    //Hacemos una lista de las dependecias de todos los modulos
    foreach ( $dependences as $dependence ){     

      //Diferenciamos entre instaladores
      switch($dependence['installer']){
        case "composer":
          $this->pushDependencesComposer($dependence);
        break;
        case "bower":
          $this->pushDependencesBower($dependence);    
        break;
      }
    }   // end foreach             

    
  }
  
  
  function pushDependencesComposer($dependence)
  {

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
  
  function pushDependencesBower($dependence)
  {
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
  
  function installDependencesBower($dependences)
  {
    //Instala las dependecias con Bower
    
    exec('rm bower.json');
    exec('echo "{\"name\": \"cogumelo\", \"version\": \"1.0a\", \"homepage\": \"https://github.com/Innoto/cogumelo\", \"license\": \"GPLv2\", \"dependencies\": {} }" > bower.json');

    foreach( $dependences as $depKey => $dep ){
      foreach( $dep as $params ){                
        if(count($params) > 1){
          $allparam = "";
          foreach( $params as $p ){
            $allparam = $allparam." ".$p;
          } // end foreach
          echo("Exec ... bower install ".$depKey."=".$allparam." --save\n");
          exec('bower install '.$depKey.'='.$allparam.' --save');          
        }
        else{
          echo("Exec ... bower install ".$depKey."=".$params[0]." --save\n");
          exec('bower install '.$depKey.'='.$params[0].' --save');

        }
      }       // end foreach
    }   // end foreach
    
  }

  function installDependencesComposer($dependences)
  {
    
    $finalArrayDep = array("require" => array(), "config" => array("vendor-dir" => "httpdocs/vendorServer"));
    foreach( $dependences as $depKey => $dep ){
      foreach( $dep as $params ){   
        $finalArrayDep['require'][$params[0]] = $params[1];
      }
    }
    $jsonencoded = json_encode($finalArrayDep);
    $fh = fopen("composer.json", 'w');
      fwrite($fh, $jsonencoded);
    fclose($fh);
    echo("Exec ... php composer.phar update\n\n");          
    exec('php composer.phar update');
    echo("If the folder does not appear vendorServer dependencies run 'php composer.phar update' or 'composer update' and resolves conflicts.\n");
    
  }


  //
  //  Includes
  //


  function loadModuleIncludes($moduleName) {

    Cogumelo::load('c_controller/ModuleController');
    ModuleController::getRealFilePath( $moduleName.'.php', $moduleName );

    $this->loadCogumeloIncludes();

    $moduleInstance = new $moduleName();

    $this->addVendorIncludeList($moduleInstance->dependences);
    $this->addIncludeList($moduleInstance->includesCommon, $moduleName );
  }

  function loadAppIncludes() {
    global $_C;

    $this->loadCogumeloIncludes();
    $this->addVendorIncludeList( $_C->dependences );
    $this->addIncludeList( $_C->includesCommon );
  }

  function loadCogumeloIncludes() {
    global $cogumeloIncludesLoaded;

    if( $cogumeloIncludesLoaded != true ) {
      $this->addVendorIncludeList(Cogumelo::$mainDependences);
    }

    $cogumeloIncludesLoaded =  true;
  }



  function addVendorIncludeList( $includes ) {

    if( sizeof( $includes ) > 0) {
      
      foreach ($includes as $includeElement) {

        $include_folder = '';

        if( $includeElement['installer'] == 'bower' ) {
          $include_folder = $includeElement['id'];
        }
        else if( $includeElement['installer'] == 'composer' ) {
          $include_folder = $includeElement['params'][0];
        }

        if( sizeof( $includeElement['includes'] ) > 0 ) {
          foreach( $includeElement['includes'] as $includeFile ) { 
            $type = $this->typeIncludeFile( $includeFile );

            echo "VendorIncludes ";
            if( $type == 'serverScript' ) {
              require_once( SITE_PATH.'../httpdocs/vendorServer/'.$include_folder.'/'.$includeFile );
            }
            else
            if( $type == 'clientScript' ) {
              echo MEDIASERVER_HOST.'vendor/'.$include_folder.'/'.$includeFile ;
            }
            else
            if( $type == 'styles' ) {
              echo MEDIASERVER_HOST.'vendor/'.$include_folder.'/'.$includeFile ;
            }

            echo "<br>";
          }
        }
      }

    }
    
  }



  function addIncludeList( $includes, $module=false) {

    if( sizeof( $includes ) > 0) { 
      foreach ($includes as $includeElement) { 

        $type = $this->typeIncludeFile( $includeElement );


        echo "include '".$module."' ";

        if( $type == 'serverScript' ) {
          echo $type.' '.$includeFile ;
        }
        else
        if( $type == 'clientScript' ) {
          echo $type.' '.$includeFile ;
        }
        else
        if( $type == 'styles' ) {
          echo $type.' '.$includeFile ;
        }

        echo "<br>";
      }
    }
  }





  function typeIncludeFile( $includeFile ) {

    $type = false;

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

    return $type;

  }


/*
  function addIncludeCSS( $includeFile ) {
    global $cogumeloIncludesCSS;

    if( !isset( $cogumeloIncludesCSS ) ) {
      $cogumeloIncludesCSS = array();
    }

    if( !in_array($cogumeloIncludesCSS) ) {
      array_push($cogumeloIncludesCSS, $includeFile);
    }

  }
  

  function addIncludeJS( $includeFile ) {
    global $cogumeloIncludesJS;

    if( !isset( $cogumeloIncludesJS ) ) {
      $cogumeloIncludesJS = array();
    }

    if( !in_array($cogumeloIncludesJS) ) {
      array_push($cogumeloIncludesJS, $includeFile);
    }
  }*/
}