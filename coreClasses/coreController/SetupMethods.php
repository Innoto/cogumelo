<?php

class SetupMethods {

  public function setSetupValue( $path, $value ) {
    // error_log( 'COGUMELO::setSetupValue: '.$path );
    global $CGMLCONF;

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

    return $CGMLCONF;
  }

  public function getSetupValue( $path = '' ) {
    // error_log( 'Cogumelo::getSetupValue: '.$path );
    global $CGMLCONF;
    $value = null;

    $parts = explode( ':', $path );
    $stack = ( $parts[0] === '' ) ? '' : '[\'' . implode( '\'][\'', $parts ) . '\']';
    $fai = '$valid = isset( $CGMLCONF'. $stack .' );';
    eval( $fai );
    if( $valid ) {
      $fai = '$value = $CGMLCONF'. $stack .';';
      eval( $fai );
    }

    return $value;
  }

}