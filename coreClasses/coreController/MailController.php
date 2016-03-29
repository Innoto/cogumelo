<?php


/**
* MailController Class
*
* An interface to use Mailcontroller with smarty template library as in the rest of the views
*
* @author: pablinhob
*/

abstract class MailController
{
	var $templatecontrol;
	var $mailSender;
	var $mailBody;

	function __construct( $vars, $template, $module == false )
	{

		Cogumelo::load('coreView/Template.php');
		Cogumelo::load('coreController/MailSender.php');

		$this->templatecontrol = new Template();
		$this->mailSender = new MailSender();

		$this->parseMail($vars, $template, $module)
	}


  /*
	* @param array vars variables array
  * @param string $template tpl file path
	* @param string $module module name
  */
	function parseMail($vars, $template, $module == false) {

		foreach($vars as $varkey => $variable)
			$this->templatecontrol->assign($varkey, $variable);

		$this->templatecontrol->setTpl($template, $module);
		//$this->templatecontrol->clearAllAssign();

		$this->mailbody = $this->templatecontrol->execToString();
	}

	function send( $adresses, $subject='', $body='', $files = false, $from_name = cogumeloGetSetupValue( 'smtp:fromName' ), $from_mail = cogumeloGetSetupValue( 'smtp:fromMail' ) ) {
		return $this->MailSender( $adresses, $subject, $body, $files, $from_name, $from_mail);
	}

}
