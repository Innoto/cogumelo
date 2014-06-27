<?php
/*
Cogumelo v1.0a - Innoto S.L.
Copyright (C) 2013 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@innoto.es>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
USA.

*/



/**
* MailSender Class
*
* Mail sender encapsulates the original phpmailer library to make less difficult it's use
*
* @author: pablinhob
*/

Cogumelo::Load('c_vendor/PHPMailer/class.phpmailer.php');


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

