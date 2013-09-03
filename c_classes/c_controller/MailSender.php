<?php
/*
Cogumelo v0.2 - Innoto S.L.
Copyright (C) 2010 Innoto Gestión para el Desarrollo Social S.L. <mapinfo@map-experience.com>

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

Class MailSender 
{
	var $phpmailer;
	
	function __construct()
	{
		Cogumelo::Load('c_vendor/PHPMailer/class.phpmailer.php');
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
			Cogumelo::Error('Error Sending mail', 3000);
		}
		else{
			return true;
			Cogumelo::Log("Mail Sent id=".$this->phpmailer->MessageID." ".var_export($adresses, true) ,3);
		}
			
		$this->phpmailer->ClearAllRecipients();
	}
}


?>