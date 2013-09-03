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

abstract class MailController 
{
	var $templatecontrol;
	var $mailSender;
	
	function __construct()
	{
		Cogumelo::Load('c_view/Template');
		Cogumelo::Load('c_controller/MailSender');
		
		$this->templatecontrol = new Template();
		$this->mailSender = new MailSender();
	}
	
	// parse template with variables 
	function parseMail($template, $vars) {
		
		foreach($vars as $varkey => $variable)
			$this->templatecontrol->assign($varkey, $variable);	
		
		$mailbody = $this->templatecontrol->fetch($template);
		$this->templatecontrol->clearAllAssign();
		
		return $mailbody;
	}	
	
}

?>