<?php


/**
* MailSender Class
*
* Mail sender encapsulates the original phpmailer library to make less difficult it's use
*
* @author: pablinhob
* @author: jmpmato
*/
class MailSenderSmtp {

  var $phpmailer;

  public function __construct() {
    $this->phpmailer = new PHPMailer( true );

    //$this->phpmailer->IsSMTP();
    $this->phpmailer->SMTPKeepAlive = true;
    $this->phpmailer->CharSet = 'UTF-8';

    $this->phpmailer->Host = Cogumelo::getSetupValue( 'mail:host' );
    $this->phpmailer->Port = Cogumelo::getSetupValue( 'mail:port' );

    if( Cogumelo::getSetupValue( 'mail:auth' ) ) {
      $this->phpmailer->SMTPAuth = true;
      $this->phpmailer->Username = Cogumelo::getSetupValue( 'mail:user' );
      $this->phpmailer->Password = Cogumelo::getSetupValue( 'mail:pass' );
    }
    else {
      $this->phpmailer->SMTPAuth = false;
    }

    if( Cogumelo::getSetupValue( 'mail:secure' ) ) {
      $this->phpmailer->SMTPSecure = Cogumelo::getSetupValue( 'mail:secure' );
    }
  }


  /**
   * Send mail message
   *
   * @param mixed $adresses are string of array of strings with recipient of mail sent
   * @param string $subject is the subject of the mail
   * @param string $bodyPlain of the e-mail
   * @param string $bodyHtml of the e-mail
   * @param mixed $files string or array of strings of filepaths
   * @param string $from_name sender name. Default is specified in conf.
   * @param string $from_maiol sender e-mail. Default especified in conf.
   *
   * @return boolean $mailResult
   **/
  public function send( $adresses, $subject, $bodyPlain = false, $bodyHtml = false, $files = false, $from_name = false, $from_mail = false ) {
    $mailResult = false;

    if( !$from_name ){
      $from_name = Cogumelo::getSetupValue( 'mail:fromName' );
    }

    if( !$from_mail ) {
      $from_mail = Cogumelo::getSetupValue( 'mail:fromEmail' );
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

    if( $bodyHtml ) {
      $this->phpmailer->isHTML( true );
      $this->phpmailer->Body = $bodyHtml;
      if( $bodyPlain ) {
        $this->phpmailer->AltBody = $bodyPlain;
      }
    }
    else {
      $this->phpmailer->Body = $bodyPlain;
    }

    $mailResult = $this->phpmailer->Send();

    if( $mailResult ) {
      Cogumelo::debug( 'Mail Sent id='.$this->phpmailer->MessageID.' '.var_export($adresses, true), 3 );
    }
    else {
      Cogumelo::debug( 'Mail ERROR('.$this->phpmailer->MessageID.'): Adresses: '.var_export($adresses, true), 3 );
      Cogumelo::debug( 'Mail ERROR('.$this->phpmailer->MessageID.'): ErrorInfo: '.$this->phpmailer->ErrorInfo, 3 );
      Cogumelo::error( 'Error sending mail' );
    }

    $this->phpmailer->ClearAllRecipients();

    return $mailResult;
  }
}
