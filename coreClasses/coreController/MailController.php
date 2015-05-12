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

	function __construct()
	{

		Cogumelo::load('coreView/Template.php');
		Cogumelo::load('coreController/MailSender.php');

		$this->templatecontrol = new Template();
		$this->mailSender = new MailSender();
	}


  /*
  * @param string $template tpl file path
  * @param array template variables array
  */
	function parseMail($template, $vars) {

		foreach($vars as $varkey => $variable)
			$this->templatecontrol->assign($varkey, $variable);

		$mailbody = $this->templatecontrol->setTpl($template);
		//$this->templatecontrol->clearAllAssign();

		return $mailbody;
	}

}

