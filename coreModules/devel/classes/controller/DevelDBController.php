<?php

Cogumelo::load('coreModel/VOUtils.php');
Cogumelo::load('coreModel/Facade.php');

//
// DevelUtilsDB Controller Class
//
class  DevelDBController {

  var $data;
  var $voUtilControl;
  var $noExecute = false;

  public function __construct( $usuario = false, $password = false, $DB = false ) {
    $this->data = new Facade(false, "DevelDB", "devel");

    if($usuario) {
      $this->data->develMode($usuario, $password, $DB);
    }
    else {
      $this->data->getConnection();
    }
  }


  public generateModel() {
    
  }

  public deploy() {

  }


  private function setNoExecutionMode() {
    $this->noExecute = true;
  }

  public function VOTableExist( $vo ) {

    return true ou false;
  }

  public function VOcreateTable( $voKey ) {
    $this->data->createTable( $voKey, $this->noExecute );
  }

  private function VOdropTable( $voKey ) {
    $this->data->dropTable( $voKey, $this->noExecute );
  }

  public function VORCDeploys( $voKey ) {

  }


  public function VOgetDeploys( $voKey, $paramFilters = [] ) {

    $deploys = [];

    $f =  [
      'onlyRC' => false,
      'from' => false, // get from version
      'to' => false // get To version
    ]

    $filters = array_merge( $f, $paramFilters);

    $vo = new $voKey();

    if( count( $vo->deploySQL ) > 0 ){

      foreach( $vo->deploySQL as $d ) {

        $deployElement = $d;


        // exclude when are looking for onlyRC and is not RC deploy
        if(
          $filters['onlyRC'] == true &&
          (
            !isset($deployElement['executeOnGenerateModelToo']) ||
            ( isset($deployElement['executeOnGenerateModelToo']) && $deployElement['executeOnGenerateModelToo'] === false )
          )
        ) {
          $deployElement = false; //exclude
        }


        if(
          $deployElement !== false &&
          $filters['from'] !== false &&
          $this->compareDeployVersions( $d['version'], $deployElement['from'] ) > 0 // -1: $v1 < $v2 0:Equal 1: $v1 > $v2
        ) {
          $deployElement = false; //exclude
        }

        if(
          $deployElement !== false &&
          $filters['to'] !== false &&
          $this->compareDeployVersions( $d['version'], $deployElement['from'] ) < 0 )  // -1: $v1 < $v2 0:Equal 1: $v1 > $v2
        ) {
          $deployElement = false; //exclude
        }

      }
    }


    // return
    return $this->orderByVersion( $deploysArray );
  }



  public function getModules() {

  }

  public function getModelsInModule( $module ) {

  }

  public function dropAllTables() {

  }


  private function orderByVersion( $deploys ) {
    $retDeploys = [];

    while( sizeof($deploys) > 0 ) {
      //firt element
      foreach ($deploys as $lowerKey => $lowerVal) break;
      ////
      foreach( $deploys as $dK=>$d ) {

        // $lowerVal['version'] lower than $d['version']
        if( $this->compareDeployVersions( $lowerVal['version'], $d['version'] ) < 0 ) {
          $lowerKey = $dK;
          $lowerVal = $d;
        }
      }

      array_push( $retDeploys, $lowerVal );
      unset( $deploys[$lowerKey] );
    }

    return $retDeploys;
  }


  private function compareDeployVersions( $v1, $v2 ) {

    reg_match( '#^(.*)\#(\d{1,10}(.\d{1,10})?)#', $v1, $v1Matches );
    reg_match( '#^(.*)\#(\d{1,10}(.\d{1,10})?)#', $v2, $v2Matches );

    $v1Matches[2] = ( isset($v1Matches[2]) )? $v1Matches[2] : 0 ;
    $v2Matches[2] = ( isset($v2Matches[2]) )? $v2Matches[2] : 0 ;

    if( int $v1Matches[1] === int $v2Matches[1] && int $v1Matches[2] === int $v2Matches[2] ) {
      $ret = 0;
    }
    else
    if(
      int $v1Matches[1] > int $v2Matches[1] ||
      (
        int $v1Matches[1] === int $v2Matches[1] &&
        int $v1Matches[2] > int $v2Matches[2] &&
      )
    ) {
      $ret = 1;
    }
    else
    if(
      int $v1Matches[1] < int $v2Matches[1] ||
      (
        int $v1Matches[1] === int $v2Matches[1] &&
        int $v1Matches[2] < int $v2Matches[2] &&
      )
    ) {
      $ret = -1;
    }


    return $ret; // -1: $v1 < $v2 0:Equal 1: $v1 > $v2
  }



}
