<?php

class SetupMethods {

  public function setSetupValue( $path, $value ) {
    // error_log( 'COGUMELO::setSetupValue: '.$path );
    global $CGMLCONF;
    $result = false;

    if( !isset( $CGMLCONF ) || !is_array( $CGMLCONF ) ) {
      $CGMLCONF = [ 'cogumelo' => [] ];
    }

    $parts = explode( ':', $path );
    $stack = '';
    foreach( $parts as $key ) {
      $valid = false;
      $stackPrev = $stack;
      $stack .= '[\''.$key.'\']';
      $fai = '$valid = isset( $CGMLCONF'. $stack .');';
      eval( $fai );
      if( !$valid ) {
        $fai = '$isArray = is_array( $CGMLCONF'. $stackPrev .');';
        eval( $fai );
        if( $isArray ) {
          $fai = '$CGMLCONF'. $stack .' = null;';
          eval( $fai );
        }
        else {
          $fai = '$CGMLCONF'. $stackPrev .' = array( $key => null );';
          eval( $fai );
        }
      }
    }
    $fai = '$CGMLCONF'. $stack .' = $value;';
    eval( $fai );
    $result = true; // TODO: Controlar que se ha guardado bien el valor

    return $result;
  }

  public function getSetupValue( $path ) {
    // error_log( 'Cogumelo::getSetupValue: '.$path );
    global $CGMLCONF;
    $value = null;

    if( $this->issetSetupValue( $path ) ) {
      $parts = explode( ':', $path );
      $stack = ( $parts[0] === '' ) ? '' : '[\'' . implode( '\'][\'', $parts ) . '\']';
      $fai = '$value = $CGMLCONF'. $stack .';';
      eval( $fai );
    }

    return $value;
  }

  public function issetSetupValue( $path ) {
    // error_log( 'Cogumelo::issetSetupValue: '.$path );
    global $CGMLCONF;
    $valid = false;

    $parts = explode( ':', $path );
    $stack = ( $parts[0] === '' ) ? '' : '[\'' . implode( '\'][\'', $parts ) . '\']';
    $fai = '$valid = isset( $CGMLCONF'. $stack .' );';
    eval( $fai );

    return $valid;
  }

  public function createSetupValue( $path, $value ) {
    // error_log( 'COGUMELO::createSetupValue: '.$path );
    $result = false;

    if( !$this->issetSetupValue( $path ) ) {
      $result = $this->setSetupValue( $path, $value );
    }

    return $result;
  }

  public function updateSetupValue( $path, $value ) {
    // error_log( 'COGUMELO::updateSetupValue: '.$path );
    $result = false;

    if( $this->issetSetupValue( $path ) ) {
      $result = $this->setSetupValue( $path, $value );
    }

    return $result;
  }

  public function addSetupValue( $path, $value ) {
    // error_log( 'COGUMELO::addSetupValue: '.$path );
    return $this->mergeSetupValue( $path, [ $value ] );
  }

  public function mergeSetupValue( $path, $addArray ) {
    // error_log( 'COGUMELO::mergeSetupValue: '.$path );
    $result = false;

    // IMPORTANTE: No se repiten valores con Key de tipo Integer

    if( is_array( $addArray ) ) {
      if( $this->issetSetupValue( $path ) ) {
        $prevArray = $this->getSetupValue( $path );
        if( !is_array( $prevArray ) ) {
          $prevArray = [ $prevArray ];
        }
        $result = $this->setSetupValue( $path, $this->addNonDuplicates( $prevArray, $addArray ) );
      }
      else {
        $result = $this->setSetupValue( $path, $addArray );
      }
    }

    return $result;
  }

  private function addNonDuplicates( $prevArray, $addArray ) {
    // error_log( 'COGUMELO::addNonDuplicates PRE: '. json_encode($prevArray) );
    // error_log( 'COGUMELO::addNonDuplicates ADD: '. json_encode($addArray) );

    $noHashValues = [];
    $intKeys = array_filter( array_keys( $prevArray ), 'is_integer' );

    if( count( $intKeys ) > 0 ) {
      $resultArray = $prevArray;

      foreach( $intKeys as $keyInt ) {
        $noHashValues[] = $prevArray[ $keyInt ];
      }

      foreach( $addArray as $key => $value ) {
        if( gettype( $key ) === 'integer' ) {
          if( !in_array( $value, $noHashValues ) ) {
            $resultArray[] = $value;
          }
        }
        else {
          $resultArray[ $key ] = $value;
        }
      }
    }
    else {
      $resultArray = array_merge ( $prevArray, $addArray );
    }

    // error_log( 'COGUMELO::addNonDuplicates RET: '. json_encode($resultArray) );

    return $resultArray;
  }

}