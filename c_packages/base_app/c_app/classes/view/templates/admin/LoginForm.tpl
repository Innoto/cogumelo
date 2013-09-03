<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link type="text/css" rel="stylesheet" href="{$site_url}Media/Script/lib/QuickForm/QuickForm.css"></link>
	<link REL="stylesheet" TYPE="text/css" HREF="{$site_url}Media/css/AdminStyles.css">
	<title>Login</title>
	<script>
		var site_url = '{$site_url}';
	</script>
</head>
<body>
	<div id="logologin">
		<div class="logo" ></div>
	</div>
	<div id="UseradminLoginContainer">
 	 <form id="UseradminLogin" action="{$site_url}admin/action/loginuseradmin" method="post" >
		<div id="UseradminLoginError" style="display:none;"></div>
		<p>
			<label id="login_label">{T_('Login de usuario')}</label>
			<input size="12" id="login" maxlength="12" name="login" value=""/>
		</p>
		<p>
			<label id="passwd_label">{T_('Contraseña')}</label>
			<input id="passwd" type="password" size="12" maxlength="12" name="passwd" value=""/>
		</p>
		<p>
			<input id="submit" type="submit" class="formbutton" value="{T_('Entrar')} &gt;" />
		</p>

	 </form> 
	</div>
	

	{* General Javascript Includes *}
	
	<script type="text/javascript" src="{$site_url}Media/VendorScript/jQuery/jQuery.js"></script>
	<script type="text/javascript" src="{$site_url}Media/VendorScript/jQuery/Class-0.0.2.js"></script>
	<script type="text/javascript" src="{$site_url}Media/VendorScript/jQuery/jQuery.address.js"></script>		
			
	{* Section Javascript Includes *}
	<script type="text/javascript" src="{$site_url}Media/Script/lib/QuickForm/QuickForm.js"></script>
	<script type="text/javascript" src="{$site_url}Media/Script/admin_Login.js"></script>


</body>
</html>
