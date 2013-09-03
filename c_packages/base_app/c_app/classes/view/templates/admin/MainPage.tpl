<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title></title>
	<script>
		var site_url = '{$site_url}';
	</script>
	<link REL="stylesheet" TYPE="text/css" HREF="{$site_url}/Media/css/AdminStyles.css">

	
	 <!--[if IE 7]>
		<link type="text/css" href="{$site_url}Media/css/admin_ie7.css" rel="stylesheet"></link>
     <![endif]-->

</head>
<body>
	<div id="MainHeader">
		<div id="logoMain">
			<div class="logo" > <H1>LOGO</H1></div>
		</div>
	</div>
	
	<div id="MainMenu">		
			{foreach from=$menu key=section_name item=caption}
				<div {if $section==$section_name}class="MainMenuSelected"{/if}>
					<a href="{$site_url}admin/section/{$section_name}">{$caption}</a>
				</div>
			{/foreach}
	 			<div id="logout" class="MainMenuEspecial"><a href="{$site_url}admin/action/logout">Desconexión</a></div>
	 		<hr class="clear"/>
	 </div>
	
	
	<div id="MainContent">
		
		{* If is Main Page Or a specific section *}
		{if !$section}
				<div id="ResumeContainer">
				
				</div>		
			
		{else}
				<div id="{$section}Container">
					
				</div>
		{/if}		
	</div>
	

	{* General Javascript Includes *}
		<script type="text/javascript" src="{$site_url}Media/VendorScript/jQuery/jQuery.js"></script>
		<script type="text/javascript" src="{$site_url}Media/VendorScript/jQuery/Class-0.0.2.js"></script>
		<script type="text/javascript" src="{$site_url}Media/VendorScript/jQuery/jQuery.address.js"></script>		
			
	{if $section == "useradmin"}
		<script type="text/javascript" src="{$site_url}Media/Script/lib/Table/Table.js"></script>
		<script type="text/javascript" src="{$site_url}Media/Script/lib/QuickForm/QuickForm.js"></script>
		<script type="text/javascript" src="{$site_url}Media/Script/admin_Useradmin.js"></script>
	{/if}

	{if $section == "result"}

		<script type="text/javascript" src="{$site_url}Media/Script/lib/QuickForm/QuickForm.js"></script>
		<script type="text/javascript" src="{$site_url}Media/Script/admin_Result.js"></script>

	{/if}
	
</body>
</html>

