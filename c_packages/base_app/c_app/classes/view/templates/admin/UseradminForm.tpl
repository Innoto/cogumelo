<div style="height:10px;"></div>
<form id="Useradmin" action="{$site_url}admin/action/useradmin" method="post">
	<div id="adminform">	
		<div id="UseradminError" style="background-color:red;"></div>
		{if $editMode} 	 	
			<input type="hidden" id="id" name="id" maxlength="30" value="{$id}"/>
		{/if}
		
		<p>
			<label>{T_('Nombre Completo')}</label>
			<input type="text0" id="name" name="name" maxlength="30" value="{$name}"/>
		</p>
		<p>
			<label>{T_('Login de usuario')}</label>
			<input type="text"  id="login" name="login" maxlength="12" value="{$login}"/>
		</p>
		
		{if $editMode}
			<p>**{T_('Deja en blanco la contraseña si no deseas cambiarla')}**</p> 	
		{/if}
		<p>
			<label>{T_('Contraseña')}</label>
			<input type="password" id="passwd1" name="passwd1" maxlength="12" value=""/>
		</p>
		<p>
			<label>{T_('Repite Contraseña')}</label>
			<input type="password" id="passwd2" name="passwd2" maxlength="12" value=""/>
		</p>
		
		<p>
			<span><input id="cancel" type="button" class="formBTN" onclick="defaultUseradmin();" value="Cancelar" /></span>
			<span><input id="submit" type="submit" class="formBTN" value="{T_('Guardar')}" /></span>
		</p>
	</div>
</form> 

