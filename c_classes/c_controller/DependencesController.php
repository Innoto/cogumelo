<?php

Cogumelo::load('c_controller/ModuleController');


Class DependencesController {


  //
  //  Vendor lib resolution
  //
  var $allDependencesComposer = array();
  var $allDependencesBower = array();

  function installDependences()
  {
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



}