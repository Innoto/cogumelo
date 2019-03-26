<?php

/**
 * PHPMD: Suppress all warnings from these rules.
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.StaticAccess)
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class GarbageCollectionController {

  public function __construct() {
    // Cogumelo::debug( __METHOD__ );

  }



  /**
    Busca elementos abandonados
    @param array $params Parametros
    @return bool
   */
  public function garbageCollection() {
    // Cogumelo::debug( __METHOD__ );

    // Importante: Precargamos los modelos
    VOUtils::listVOs();

    // $this->garbageCollectionModule('filedata');
    // $this->garbageCollectionModule('geozzy');

    global $C_ENABLED_MODULES;
    foreach( $C_ENABLED_MODULES as $moduleName ) {
      $this->garbageCollectionModule( $moduleName );
    }

    // error_log("\n\n  RESUMO:\n\n");
    error_log("\nProceso general de garbageCollection finalizado.\n");
  } // function garbageCollection()

  private function garbageCollectionModule( $moduleName ) {
    $result = false;
    $done = false;

    $moduleFile = ModuleController::getRealFilePath( $moduleName.'.php', $moduleName );
    if( !empty( $moduleFile ) ) {
      require_once( $moduleFile );
      if( class_exists( $moduleName ) ) {
        $moduleObj = new $moduleName();
        if( method_exists( $moduleObj, 'garbageCollection' ) ) {
          error_log("Garbage Collection $moduleName - Lanzando * * * * * * * * * *\n");
          $result = $moduleObj->garbageCollection();
          $done = true;
          error_log("\nGarbage Collection $moduleName - Finalizado\n");
        }
      }
    }

    // if(!$done) { error_log("\nGarbage Collection $moduleName - NO tiene\n"); }

    return $result;
  }


  public function listModelIds( $modelName ) {
    // Cogumelo::debug( __METHOD__ );
    $modelIds = [];

    // error_log("\n\nBuscando todos los ID de $modelName:\n");

    $objModel = new $modelName();
    $listModel = $objModel->listItems( [ 'fields' => ['id'] ] );
    if( is_object( $listModel ) ) {
      while( $dataObj = $listModel->fetch() ) {
        $modelIds[] = $dataObj->getter('id');
      }
    }

    return $modelIds;
  }

  public function listModelUsedIds( $modelName ) {
    // Cogumelo::debug( __METHOD__ );
    $usedIds = [];

    $relations = $this->listModelRelations( $modelName );

    // error_log("\n\nBuscando todas as referencias a $modelName:\n");

    foreach( $relations['from'] as $voName => $voRelKeys ) {
      // error_log("$modelName <- $voName ( ".implode( ',', $voRelKeys )." )");
      $objModel = new $voName();
      $listModel = $objModel->listItems( [ 'fields' => $voRelKeys ] );
      if( is_object( $listModel ) ) {
        while( $dataObj = $listModel->fetch() ) {
          foreach( $voRelKeys as $key ) {
            $idRefer = $dataObj->getter( $key );
            if( !empty( $idRefer ) ) {
              $usedIds[ $idRefer ] = true;
            }
          }
        }
      }
    }

    return $usedIds;
  }

  public function listModelRelations( $modelName ) {
    // Cogumelo::debug( __METHOD__ );
    $relations = [
      'to' => [],
      'from' => [],
    ];

    $relsData = ( json_decode( json_encode( VOUtils::getRelObj( $modelName ) ), true ) )['relationship'];
    foreach( $relsData as $keyModelTo => $relInfos ) {

      // print_r( $relInfos );

      list( $relKey, $relModel ) = explode( '.', $keyModelTo );
      if( $relKey !== 'id' ) {
        $relations['to'][ $relModel ][] = $relKey;
      }
      else {
        $relations['from'][ $relModel ][] = $relInfos['relatedWithId'];
      }
    }

    return $relations;
  }
} // GarbageCollectionController
