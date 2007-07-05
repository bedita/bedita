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
	<div class="beditaButton" onClick = "document.location ='/'">
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

{formHelper fnc="create" args="'authentication', array('action' => '/login', 'type' => 'POST')"}

{assign var="URL" value=$beurl->here()}

{formHelper fnc="hidden" args="'login/URLOK', array('value' => '$URL')"}
{formHelper fnc="hidden" args="'login/URLERROR', array('value' => '$URL')"}

<table border="0" cellspacing="8" cellpadding="0">
<tr>
	<td colspan="2">
		<p>Area riservata ai gestori.</p>
		<div id="errorsDiv">{if ($session->check('Message.flash'))}{$session->flash()}{/if}</div>
	</td>
</tr>
<tr> 
	<td>Username</td>
	<td>{formHelper fnc="text" args="'login/userid', array('style' => 'width: 150px')"}</td>
</tr>
<tr> 
	<td>Password</td>
	<td>{formHelper fnc="password" args="'login/passwd', array('style' => 'width: 150px')"}</td>
</tr>
<tr>
	<td>&nbsp;</td> 
	<td>{formHelper fnc="submit" args="'entra', array('onclick' => 'if(!checkOnSubmit(\'authenticationAddForm\',rules)) return false;')"}</td>
</tr>
</table>

</form>
