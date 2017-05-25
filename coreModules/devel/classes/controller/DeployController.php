<?php

//
// Deploy Controller Class
//
class  DeployController {

  public function __construct(  ) {

    global $C_ENABLED_MODULES;

    foreach( $C_ENABLED_MODULES as $modulo ) {
      $this->deployModule( $modulo );

    }

  }

  private function deployModule( $modulo ) {


    if( $modulo::checkRegisteredVersion() != false ) {
      $modulo::moduleRC();
      $modulo::moduleDeploy( true ); // true is first time execution

    }
    else {
      $modulo::moduleDeploy();
    }

    $modulo::register(); //register or update current version



    foreach( $listaModelos as $modelo ) {
      deployModel($modelo) {

      }
    }



  }

  private function deployModel( $modelo ) {
    if( $modeloRexistrado ) {
      xerar Taboa // se procede
      xerar sql extra  de inicialización
    }
    else {


      foreach( $this->getDeploysList() as $deploy ){

        $deployResult=this->executaDeploy()

        if( $deployResult['success'] == true ) {
          model::registerVersion( $deploy['version'] );
          echo "- deploy"
        }
        else {

        }

      }
    }

  }

  private function getDeployList() {
    coller versións de deploy dende a versión rexistrada actualmetne ata a versión do módulo
  }


}
