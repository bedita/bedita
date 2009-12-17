<script type="text/javascript">
{literal}
<!--
var rules = new Array();
rules[0]='loginUserid:Login|required';
rules[1]='loginPasswd:Password|minlength|6';
//-->
{/literal}
</script>

<div id="loginStatusBox">
	<div class="beditaButton" onClick = "document.location ='{$html->url('/')}'">
		<span style="font:bold 17px Verdana">BEdita</span><br/><b>&gt;</b>
		<a href="{$html->url('/authentications/logout')}">{t}Exit{/t}</a><br/><br/><p>
	</div>
	<div class="menuLeft">
		<h1 onClick="window.location='./'" class="login"><a href="./">{t}Change Password{/t}</a></h1>
	</div>
</div>

<form action="{$html->url('/authentications/changePasswd')}" method="post" name="loginForm" id="loginForm">

<table border="0" cellspacing="8" cellpadding="0">
<tr>
	<td colspan="2">
		<p>{t}Backend user restricted area{/t}</p>
		<div id="errorsDiv">{if ($session->check('Message.flash'))}{$session->flash()}{/if}</div>
	</td>
</tr>
<tr>
	<td>{t}Username{/t}</td>
	<td><b>$user.User.userid</b>
		<input type='hidden' value="$user.User.userid" name="user[User][userid]"/>
	</td>
</tr>
<tr>
	<td>{t}New password{/t}</td>
	<td>{formHelper fnc="password" args="'login/passwd', array('style' => 'width: 150px')"}</td>
</tr>
<tr>
	<td>{t}Confirm password{/t}</td>
	<td>{formHelper fnc="password" args="'login/passwd2', array('style' => 'width: 150px')"}</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="{t}Change{/t}" onclick="if(!checkOnSubmit('loginForm',rules)) return false;"/></td>
</tr>
</table>

</form>