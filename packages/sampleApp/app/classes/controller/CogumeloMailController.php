<?php

Cogumelo::load('coreController/MailController.php');

Class CogumeloMailController extends MailController
{
  function CogumeloSendMail($email)
  {
    $template = "mail.tpl";
    $subject = "Probando Mailing Cogumelo";

    $this->mailSender->Send( $email, $subject, $this->parseMail($template, array()));
  }
}