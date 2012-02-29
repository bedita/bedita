<form action="{$html->here}" method="post">
	<label>username</label>
	<br />
	<input type="text" name="login[userid]" />
	<br />
	<label>password</label>
	<br />
	<input type="password" name="login[passwd]" />
	<br />
	<input type="hidden" name="backURL" value="{$beurl->here()}"/>
	<input style="margin:10px 0px 10px 0px" type="submit" value="{t}submit{/t}" />
</form>