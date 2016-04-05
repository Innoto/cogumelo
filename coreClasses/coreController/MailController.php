<?php


/**
* MailController Class
*
* An interface to use Mailcontroller with smarty template library as in the rest of the views
*
* @author: pablinhob
* @author: jmpmato
*/

class MailController {
  private $senderController = false;
  private $bodyPlain = false;
  private $bodyHtml = false;
  private $mailFiles = false;

  public function __construct( $vars = false, $tplFile = false, $module = false ) {
    switch( Cogumelo::getSetupValue( 'mail:type' ) ) {
      case 'gmail':
        Cogumelo::load('coreController/MailSenderSmtp.php');
        $this->senderController = new MailSenderGmail();
        break;
      default: // SMTP
        Cogumelo::load('coreController/MailSenderSmtp.php');
        $this->senderController = new MailSenderSmtp();
        break;
    }

    if( $tplFile ) {
      $this->parseMail($vars, $tplFile, $module);
    }
  }

  /**
   * Reestablece los valores iniciales
   **/
  public function clear() {
    $senderController = false;
    $bodyPlain = false;
    $bodyHtml = false;
    $mailFiles = false;
  }

  /**
   * Tranforma un Template a String (puede asignar variables antes)
   * @param Template Object
   * @param array (name,value) to Template
   **/
  public function templateToString( $objTemplate, $vars = false ) {
    if( is_array($vars) && count($vars) > 0 ) {
      foreach( $vars as $key => $value ) {
        $objTemplate->assign( $key, $value );
      }
    }

    return( $objTemplate->execToString() );
  }

  /**
   * Prepara los datos en un template y lo establece en el bodyHtml
   * @param array vars variables array
   * @param string $template tpl file path
   * @param string $module module name
   **/
  public function parseMail( $vars, $tplFile, $module = false ) {
    Cogumelo::load('coreView/Template.php');
    $objTemplate = new Template();
    $objTemplate->setTpl($tplFile, $module);
    $this->setBodyHtml( $objTemplate, $vars );
  }

  /**
   * Establece el body con los contenidos indicados o la compilación de los templates
   * @param mixed Template object or string
   * @param mixed Template object or string
   * @param mixed vars to Template object
   **/
  public function setBody( $bodyPlain, $bodyHtml = false, $vars = false ) {
    $this->setBodyPlain( $bodyPlain, $vars );
    $this->setBodyHtml( $bodyHtml, $vars );
  }

  /**
   * Establece el bodyPlain con el contenido indicado o la compilación del template
   * @param mixed Template object or string
   **/
  public function setBodyPlain( $bodyPlain, $vars = false ) {
    if( is_object ( $bodyPlain ) && get_class( $bodyPlain )==='Template'  ) {
      $bodyPlain = $this->templateToString( $bodyPlain, $vars );
    }
    $this->bodyPlain = $bodyPlain;
  }

  /**
   * Establece el bodyHtml con el contenido indicado o la compilación del template
   * @param mixed Template object or string
   */
  public function setBodyHtml( $bodyHtml, $vars = false ) {
    if( is_object ( $bodyHtml ) && get_class( $bodyHtml )==='Template' ) {
      $bodyHtml = $this->templateToString( $bodyHtml, $vars );
    }
    $this->bodyHtml = $bodyHtml;
  }

  /**
   * Establece los ficheros a añadir al correo
   * @param array files paths
   **/
  public function setFiles( $files ) {
    $this->mailFiles = $files;
  }


  /**
   * Envía el correo con esta cabecera y los datos previamente definidos
   * @param mixed $adresses are string of array of strings with recipient of mail sent
   * @param string $subject is the subject of the mail
   * @param string $from_name sender name. Default is specified in conf.
   * @param string $from_mail sender e-mail. Default especified in conf.
   **/
  public function send( $adresses, $subject, $from_name = false, $from_mail = false )  {
    // send( $adresses, $subject, $bodyPlain = false, $bodyHtml = false, $files = false, $from_name = false, $from_mail = false )
    $mailSenderResult = false;

    if( $this->bodyPlain || $this->bodyHtml ) {
      $mailSenderResult = $this->senderController->send( $adresses, $subject,
        $this->bodyPlain, $this->bodyHtml, $this->mailFiles, $from_name, $from_mail );
    }
    else {
      Cogumelo::error( 'Error sending mail: No body.' );
    }

    return $mailSenderResult;
  }

}
