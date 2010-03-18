{if $session->flash('info')}

{elseif $session->flash('error')}

{/if}

{if $session->read("userToChangePwd")}

	<div>
	{t}Hi{/t} {$session->read("userToChangePwd.User.realname")},<br/>
	{t}to change your password fill the following form{/t}
	<form action="#" method="post" name="loginForm" id="loginForm">

			<label class="block" for="passwd">{t}Password{/t}</label>
			<input class="big" tabindex="1" type="password" name="data[User][passwd]" id="passwd"/>

			<label class="block" for="pwd">{t}Rewrite password{/t}</label>
			<input class="big" tabindex="2" type="password" name="pwd" id="pwd"/>

			<input tabindex="3" type="submit" value="{t}change{/t}"/>

		</form>
	</div>

{/if}