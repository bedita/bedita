{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}

<script type="text/javascript">
<!--
{literal}
$.validator.setDefaults({ 
	/*submitHandler: function() { alert("submitted!"); },*/
	success: function(label) { label.html("&nbsp;").addClass("checked");}
});
$().ready(function() { 
	$("#loginform").validate(); 
});
{/literal}
//-->
</script>

</head>
<body>

<div id="loginStatusBox">
	<div class="beditaButton" onClick = "document.location ='{$html->url('/')}'">
		<span style="font:bold 17px Verdana">{t}B.Edita{/t}</span><br/><b>&gt;</b>
		<a href="{$html->url('/authentications/logout')}">{t}Exit{/t}</a><br/><br/><p>
		<b>{t}Consorzio BEdita{/t}</b>
		<br/>2007</p>
	</div>
	<div class="menuLeft">
		<h1 onClick="window.location='./'" class="login"><a href="./">{t}Login{/t}</a></h1>
	</div>
</div>

<form action="{$html->url('/authentications/login')}" method="post" name="loginForm" id="loginForm" class="cmxform">
<fieldset>

<input type="hidden" name="data[login][URLOK]" value="{$beurl->here()}" id="loginURLOK" />

<table border="0" cellspacing="8" cellpadding="0">
<tr>
	<td colspan="3">
		<p>{t}Backend user restricted area{/t}</p>
		<div id="errorsDiv">{if ($session->check('Message.flash'))}{$session->flash()}{/if}</div>
	</td>
</tr>
<tr>
	<td class="label"><label id="luserid" for="userid">{t}Username{/t}</label></td>
	<td class="field"><input type="text" name="data[login][userid]" id="userid" class="{literal}{required:true}{/literal}" title="{t}Username is required{/t}"/></td>
	<td class="status">&#160;</td>
</tr>
<tr>
	<td class="label"><label id="lpasswd" for="passwd">{t}Password{/t}</label></td>
	<td class="field"><input type="password" name="data[login][passwd]" id="passwd" class="{literal}{required:true}{/literal}" title="{t}Password is required{/t}"/></td>
	<td class="status">&#160;</td>
</tr>
<tr>
	<td class="label">&nbsp;</td>
	<td class="field" colspan="2"><input class="submit" type="submit" value="{t}Enter{/t}"/></td>
</tr>
</table>

</fieldset>
</form>

{include file="../layout_parts/messages.tpl"}