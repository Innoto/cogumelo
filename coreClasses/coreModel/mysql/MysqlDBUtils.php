<?php


/**
* DBUtils
*
* @package Cogumelo Model
*/
class MysqlDBUtils
{

  static function DecodeGeometry( $arg ) {
    $rg = $arg[0];
    $ret = false;

    if( preg_match("#(.*)\((.*)\)#", $rg, $matches) ) {

      // type can be (POINT,POLYGON,LINESTRING)
      $ret = array();
      $ret['type'] = $matches[1];
      $ret['data'] = explode( ',', $matches[2] );

      foreach ($ret['data'] as $k => $val) {
        $ret['data'][$k] = explode(' ', $val);
      }
    }


    return $ret;
  }



  static function EncodeGeometry( $arg ) {
    $ret = false;
    $rg = $arg[0];

    if( isset( $rg['type'] ) && isset( $rg['data'] ) ) {

      $spatialChain = '';

      if( $rg['type'] == 'POINT' ) {
        $spatialChain = $rg['data'][0].' '.$rg['data'][1]
      }
      else if( $rg['type'] == 'POLYGON' || $rg['type'] == 'LINESTRING' ) {
        $comma = '';
        foreach ( $rg['data'] as $val ) {
          $spatialChain .= $comma.implode(' ', $val);

          $comma = ',';
        }
      }


      $ret = $rg['type'].'('.$spatialChain.')';
    }

    return $ret;
  }

}
