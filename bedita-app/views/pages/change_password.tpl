<div class="primacolonna">
	 <div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''}</label></div>


	<div class="insidecol colophon">

		{$view->element('colophon')}
        {$conf->colophon|default:''}

	</div>

</div>


<div class="secondacolonna">

	<div class="modules">
	   <label class="admin">{t}Change Password{/t}</label>
	</div>

	


</div>

{if $session->read("userToChangePwd")}

	<div class="login">
	{t}Hi{/t} {$session->read("userToChangePwd.User.realname|escape")},<br/>
	{t}to change your password fill the following form{/t}
	<form action="#" method="post" name="loginForm" id="loginForm" class="cmxform" style="padding-left:5px;">
		{$beForm->csrf()}
		<fieldset>

			<label class="block" for="passwd">{t}Password{/t}</label>
			<input class="big" tabindex="1" style="width:103px; margin-bottom:10px;" type="password" name="data[User][passwd]" id="passwd" title="{t}Password is required{/t}"/>

			<label class="block" for="pwd">{t}Rewrite password{/t}</label>
			<input class="big" tabindex="2" style="width:103px; margin-bottom:10px;" type="password" name="pwd" id="pwd" title="{t}Password is required{/t}"/>

			<input class="bemaincommands" tabindex="3" type="submit" value="{t}change{/t}"/>
		</fieldset>
		</form>
	</div>

{/if}
