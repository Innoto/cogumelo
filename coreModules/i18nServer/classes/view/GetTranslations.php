<?php

Cogumelo::load('coreView/View.php');

class GetTranslations extends View
{

  public function __construct( $base_dir ) {
    parent::__construct( $base_dir );
  }

  /**
  * Evaluate the access conditions and report if can continue
  * @return bool : true -> Access allowed
  */
  public function accessCheck() {
    return true;
  }

  // load json from i18n
  public function getJson() {

    global $C_LANG;

    $langArray = Cogumelo::getSetupValue( 'lang:available' );
    $lang = $langArray[ $C_LANG ]['i18n'];

    $json_path = Cogumelo::getSetupValue( 'i18n:localePath' ).'/'.$lang.'/LC_MESSAGES/translation.json';

    if (file_exists($json_path)){
      header( 'application/javascript' );
      $json = file_get_contents($json_path);
      $res = 'var jsonTraduccions='.$json.';
        function __(cadea){
          var res = cadea;
          $.each(jsonTraduccions, function(i,item){
            if(i === cadea){
              if (item)
                res=item;
              return false;
            }
          });
          return res;
        }';
      print($res);
    }
  }
}
