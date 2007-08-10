{*
Template Home page.
*}
{php}$vs = &$this->get_template_vars() ;{/php}

<script type="text/javascript">
{literal}
<!--
var rules = new Array();
rules[0]='loginUserid:Login|required';
rules[1]='loginPasswd:Password|minlength|6';
-->
{/literal}
</script>

</head>
<body>

<div id = "sxPageLogin">
	<div class="beditaButton" onClick = "document.location = {$html->url('/')}">
		<b style="font:bold 17px Verdana">B.Edita</b><br><b>&#155;</b> 
		<a href="{$html->url('/authentications/logout')}">esci</a><br><br><p>
		<b>Consorzio BEdita</b>
		<br>2007</p>
	</div>
	<div class="menuLeft">
		<h1 onClick="window.location='./'" class="login"><a href="./">Login</a></h1>
	</div>
</div>

<div id="dxPageLogin">
<br/><br/><br/><br/>


<form action="{$html->url('/authentications/login')}" method="post" name="loginForm" id="loginForm">

{assign var="URL" value=$beurl->here()}

{formHelper fnc="hidden" args="'login/URLOK', array('value' => '$URL')"}
{formHelper fnc="hidden" args="'login/URLERROR', array('value' => '$URL')"}

<table border="0" cellspacing="8" cellpadding="0">
<tr>
	<td colspan="2">
		<p>{t}Backend user restricted area{/t}</p>
		<div id="errorsDiv">{if ($session->check('Message.flash'))}{$session->flash()}{/if}</div>
	</td>
</tr>
<tr> 
	<td>{t}Username{/t}</td>
	<td>{formHelper fnc="text" args="'login/userid', array('style' => 'width: 150px')"}</td>
</tr>
<tr> 
	<td>{t}Password{/t}</td>
	<td>{formHelper fnc="password" args="'login/passwd', array('style' => 'width: 150px')"}</td>
</tr>
<tr>
	<td>&nbsp;</td> 
	<td><input type="submit" value="{t}Enter{/t}" onclick="if(!checkOnSubmit('loginForm',rules)) return false;"/>
	</td>
</tr>
</table>

</form>
