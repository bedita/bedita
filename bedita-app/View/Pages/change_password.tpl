<div class="primacolonna">
	 <div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>


	<div class="insidecol colophon">

		{$view->element('colophon')}

	</div>

</div>


<div class="secondacolonna">

	<div class="modules">
	   <label class="admin">{t}Change Password{/t}</label>
	</div>

	


</div>

{if $this->Session->read("userToChangePwd")}

	<div style="width:180px; margin-left:310px; padding-top:25px;">
	{t}Hi{/t} {$this->Session->read("userToChangePwd.User.realname")},<br/>
	{t}to change your password fill the following form{/t}
	<form action="#" method="post" name="loginForm" id="loginForm" class="cmxform" style="padding-left:5px;">
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