<?php


/**
* MailController Class
*
* An interface to use Mailcontroller with smarty template library as in the rest of the views
*
* @author: pablinhob
*/

class MailController
{
	var $templatecontrol;
	var $mailSender;
	var $mailBody;
	var $mailFiles = false;

	function __construct( $vars, $template, $module = false )	{

		Cogumelo::load('coreView/Template.php');
		Cogumelo::load('coreController/MailSender.php');

		$this->templatecontrol = new Template();
		$this->mailSender = new MailSender();

		$this->parseMail($vars, $template, $module);
	}


  /*
	* @param array vars variables array
  * @param string $template tpl file path
	* @param string $module module name
  */
	function parseMail($vars, $template, $module = false) {


		if( is_array($vars) && sizeof($vars) > 0 ) {
			foreach($vars as $varkey => $variable) {
				$this->templatecontrol->assign($varkey, $variable);
			}
		}

		$this->templatecontrol->setTpl($template, $module);
		//$this->templatecontrol->clearAllAssign();

		$this->mailBody = $this->templatecontrol->execToString();
	}

	/*
	* @param array files paths
  */
	function setFiles( $files ) {
		$this->mailFiles = $files;
	}


	/*
  * @param mixed $adresses are string of array of strings with recipient of mail sent
  * @param string $subject is the subject of the mail
  * @param string $from_name sender name. Default is specified in conf.
  * @param string $from_mail sender e-mail. Default especified in conf.
  */
	function send($adresses, $subject='', $from_name = false, $from_mail = false )	{


		if( $from_mail == false ) {

			$from_mail = cogumeloGetSetupValue( 'smtp:fromName' );
		}

		if( $from_mail == false) {
			$from_mail = cogumeloGetSetupValue( 'smtp:fromMail' );
		}

		return $this->mailSender->send( $adresses, $subject, $this->mailBody, $this->mailFiles, $from_name, $from_mail);
	}

}
