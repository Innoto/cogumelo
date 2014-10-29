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

		Cogumelo::load('c_view/Template.php');
		Cogumelo::load('c_controller/MailSender.php');

		$this->templatecontrol = new Template('/home/proxectos/cogumelo/c_packages/base_app/httpdocs/../c_app/');
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

?>