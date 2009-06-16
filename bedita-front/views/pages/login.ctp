<form action="<?php e($this->here);?>" method="post">
	<label>username</label>
	<br />
	<input type="text" name="login[userid]" />
	<br />
	<label>password</label>
	<br />
	<input type="password" name="login[passwd]" />
	<br />
	<input type="hidden" name="backURL" value="<?php e($beurl->here());?>"/>
	<input style="margin:10px 0px 10px 0px" type="submit" value="<?php __("submit");?>" />
</form>