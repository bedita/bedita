<form action="{$html->url('/hashjob/user_sign_up')}" method="post">
	username:<input type="text" name="data[User][userid]" size="30"/><br/>
	password: <input type="password" name="data[User][passwd]" size="30"/><br/>
	retype password: <input type="password" name="pwd" size="30"/><br/>
	email: <input type="email" name="data[User][email]" size="30"/><br/>
	nome: <input type="text" name="data[Card][name]" size="30"/><br/>
	<br/><input type="submit" value="{t}sign up{/t}"/>
</form>