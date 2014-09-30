<?php


/**
* MailSender Class
*
* Mail sender encapsulates the original phpmailer library to make less difficult it's use
*
* @author: pablinhob
*/

Cogumelo::vendorLoad('phpmailer/phpmailer/class.phpmailer.php');


Class MailSender 
{
	var $phpmailer;
	
	function __construct()
	{
		$this->phpmailer = new PHPMailer();
		
		//$this->phpmailer->IsSMTP();	
	 	$this->phpmailer->SMTPAuth = SMTP_AUTH;
	 	$this->phpmailer->Host = SMTP_HOST;
	 	$this->phpmailer->Port = SMTP_PORT;
	 	$this->phpmailer->Username = SMTP_USER;
	 	$this->phpmailer->Password = SMTP_PASS;
	 	$this->phpmailer->SMTPKeepAlive=true;
	 	$this->phpmailer->CharSet = "UTF-8";

	}


  /*
  * @param mixed $adresses are string of array of strings with recipient of mail sent
  * @param string $subject is the subject of the mail
  * @param string $body of the e-mail
  * @prarm mixed $files string or array of strings of filepaths
  * @param string $from_name sender name. Default is specified in conf.
  * @param string $from_maiol sender e-mail. Default especified in conf.
  */
	function send($adresses, $subject='', $body='', $files = false, $from_name = SYS_MAIL_FROM_NAME, $from_mail = SYS_MAIL_FROM_EMAIL)
	{
		// If $adresses is an array of adresses include all into mail
		if( is_array($adresses) )
			foreach($adresses as $adress)	$this->phpmailer->AddAddress($adress);
		else
			$this->phpmailer->AddAddress($adresses);
		
		if($files) {
			if( is_array($files) )
				foreach($files as $file) $this->phpmailer->AddAttachment($file);
			else
				$this->phpmailer->AddAttachment($files);
		}	
		$this->phpmailer->FromName = $from_name;
		$this->phpmailer->From = $from_mail;

		$this->phpmailer->Subject = $subject;
		$this->phpmailer->isHTML(true);
		$this->phpmailer->Body = $body;
		
		if(!$this->phpmailer->Send()){
			return false;
			Cogumelo::error('Error Sending mail');
		}
		else{
			return true;
			Cogumelo::debug("Mail Sent id=".$this->phpmailer->MessageID." ".var_export($adresses, true) ,3);
		}
			
		$this->phpmailer->ClearAllRecipients();
	}
}

