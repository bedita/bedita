<form action="{$html->here}" method="post">
	<label>username</label>
	<br />
	<input type="text" name="login[userid]" />
	<br />
	<label>password</label>
	<br />
	<input type="password" name="login[passwd]" />
	<br />
	<input style="margin:10px 0px 10px 0px" type="submit" value="{t}submit{/t}" />
</form>

<form action="{$html->here}" method="post">
	<input type="hidden" name="login[auth_type]" value="facebook" />
	<input type="submit" value="{t}facebook{/t}" />
</form>

<form action="{$html->here}" method="post">
	<input type="hidden" name="login[auth_type]" value="twitter" />
	<input type="submit" value="{t}twitter{/t}" />
</form>

<form action="{$html->here}" method="post">
	<input type="hidden" name="login[auth_type]" value="google" />
	<input type="submit" value="{t}google{/t}" />
</form>