<?php


/**
* MailSender Class
*
* Mail sender encapsulates the original phpmailer library to make less difficult it's use
*
* @author: pablinhob
*/
class MailSender {

  var $phpmailer;

  public function __construct() {

    echo "\nCAMBIO: Ahora hay multiples MailSender, dependiendo de 'type' de la conf. de 'mail'\n\n";
    die( "\nCAMBIO: Ahora hay multiples MailSender, dependiendo de 'type' de la conf. de 'mail'\n\n" );



    /*
    $this->phpmailer = new PHPMailer( true );

    //$this->phpmailer->IsSMTP();
    $this->phpmailer->SMTPAuth = Cogumelo::getSetupValue( 'smtp:auth' );
    $this->phpmailer->Host = Cogumelo::getSetupValue( 'smtp:host' );
    $this->phpmailer->Port = Cogumelo::getSetupValue( 'smtp:port' );
    $this->phpmailer->Username = Cogumelo::getSetupValue( 'smtp:user' );
    $this->phpmailer->Password = Cogumelo::getSetupValue( 'smtp:pass' );
    $this->phpmailer->SMTPKeepAlive = true;
    $this->phpmailer->CharSet = 'UTF-8';
    */
  }


  /**
   * Send mail message
   *
   * @param mixed $adresses are string of array of strings with recipient of mail sent
   * @param string $subject is the subject of the mail
   * @param string $body of the e-mail
   * @param mixed $files string or array of strings of filepaths
   * @param string $from_name sender name. Default is specified in conf.
   * @param string $from_maiol sender e-mail. Default especified in conf.
   *
   * @return boolean $mailResult
   **/
  /*
  public function send( $adresses, $subject = '', $body = '', $files = false, $from_name = false, $from_mail = false ) {
    $mailResult = false;

    if( $from_name == false ){
      $from_name = Cogumelo::getSetupValue( 'smtp:fromName' );
    }

    if( $from_mail == false ) {
      $from_mail = Cogumelo::getSetupValue( 'smtp:fromMail' );
    }


    // If $adresses is an array of adresses include all into mail
    if( is_array($adresses) ) {
      foreach( $adresses as $adress ) {
        $this->phpmailer->AddAddress($adress);
      }
    }
    else {
      $this->phpmailer->AddAddress($adresses);
    }

    if( $files ) {
      if( is_array($files) ) {
        foreach( $files as $file ) {
          $this->phpmailer->AddAttachment($file);
        }
      }
      else {
        $this->phpmailer->AddAttachment($files);
      }
    }

    $this->phpmailer->SetFrom( $from_mail, $from_name );
    $this->phpmailer->AddReplyTo( $from_mail, $from_name );

    $this->phpmailer->Subject = $subject;
    $this->phpmailer->isHTML( true );
    $this->phpmailer->Body = $body;

    $mailResult = $this->phpmailer->Send();

    if( $mailResult ) {
      Cogumelo::debug( 'Mail Sent id='.$this->phpmailer->MessageID.' '.var_export($adresses, true), 3 );
    }
    else {
      Cogumelo::debug( 'Mail ERROR('.$this->phpmailer->MessageID.'): Adresses: '.var_export($adresses, true), 3 );
      Cogumelo::debug( 'Mail ERROR('.$this->phpmailer->MessageID.'): ErrorInfo: '.$this->phpmailer->ErrorInfo, 3 );
      Cogumelo::error( 'Error Sending mail' );
    }

    $this->phpmailer->ClearAllRecipients();

    return $mailResult;
  }
  */
}
