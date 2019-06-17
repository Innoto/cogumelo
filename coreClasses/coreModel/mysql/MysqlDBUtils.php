<?php


/**
* DBUtils
*
* @package Cogumelo Model
*/
class MysqlDBUtils
{

  static function decodeGeometry( $arg ) {
    $rg = $arg[0];
    $ret = false;


    if( preg_match("#(.*)\((.*)\)#", $rg, $matches) ) {

      // type can be (POINT,POLYGON,LINESTRING)
      $ret = array();
      $ret['type'] = $matches[1];


      if( $ret['type'] == 'POINT') {
        //$ret['data'] = array_map( 'floatval', explode(' ', $matches[2]));
        foreach( explode(' ', $matches[2]) as $val ) {
          $ret[ 'data' ][] = ''.number_format((float)$val, 4, '.', '').'';
        }
      }
      else if( $ret['type'] == 'POLYGON' ) {
        $ret['data'] = explode( ',', $matches[2] );
        foreach ($ret['data'] as $k => $val) {

          $ret['data'][$k] = '(('.explode(' ', $val).'))';
        }
      }
      else {
        $ret['data'] = explode( ',', $matches[2] );
        foreach ($ret['data'] as $k => $val) {
          $ret['data'][$k] = explode(' ', $val);
        }
      }
    }


    return $ret;
  }



  static function encodeGeometry( $arg ) {
    $ret = false;
    $rg = $arg[0];

    if( isset( $rg['type'] ) && isset( $rg['data'] ) ) {

      $spatialChain = '';

      if( $rg['type'] == 'POINT' ) {
        $spatialChain = $rg['data'][0].' '.$rg['data'][1];

        if( ctype_space($spatialChain) ) {
          $ret = null;
        }
        else {
          $ret = $rg['type'].'('.$spatialChain.')';
        }

      }
      else if( $rg['type'] == 'POLYGON') {
        $comma = '';
        foreach ( $rg['data'] as $val ) {
          $spatialChain .= $comma.''.str_replace(',', '.', implode(' ', $val) ).'';
          $comma = ',';
        }
        $ret = $rg['type'].'(('.$spatialChain.'))';
      }
      else if( $rg['type'] == 'LINESTRING' ) {
        $comma = '';
        foreach ( $rg['data'] as $val ) {
          $spatialChain .= $comma.implode(' ', $val);

          $comma = ',';
        }

        $ret = $rg['type'].'(('.$spatialChain.'))';
      }

    }

    return $ret;
  }

}
