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

	
<div class="primacolonna">
	 <div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->version}</label></div>
	 
	 
	<div class="insidecol colophon">	
	
		{include file="../common_inc/colophon.tpl"}
	
	</div>
	 
</div>


<div class="secondacolonna">

	<div class="modules">
	   <label class="admin">{t}Login{/t}</label>
	</div> 
	
	{include file="../common_inc/messages.tpl"}
	

</div>


<div style="width:180px; margin-left:310px; padding-top:25px;">
<form action="{$html->url('/authentications/login')}" method="post" name="loginForm" id="loginForm" class="cmxform" style="padding-left:5px;">
	<fieldset>
		<input type="hidden" name="data[login][URLOK]" value="{$beurl->here()}" id="loginURLOK" />
		
		<label class="block" id="luserid" for="userid">{t}Username{/t}</label>
		<input class="big" style="width:103px" type="text" name="data[login][userid]" id="userid" class="{literal}{required:true}{/literal}" title="{t}Username is required{/t}"/></td>
		<label class="block" id="lpasswd" for="passwd">{t}Password{/t}</label>
		<input class="big" style="width:103px; margin-bottom:10px;" type="password" name="data[login][passwd]" id="passwd" class="{literal}{required:true}{/literal}" title="{t}Password is required{/t}"/>
		
		<input class="bemaincommands" type="submit" value="{t}Enter{/t}"/>
	</fieldset>
	</form>
</div>

<div class="quartacolonna" style="border-left:1px solid gray; padding:5px 0px 0px 10px; width:420px; left:440px; top:20px;">
	<div style="display:none">
	<label class="block">{t}Hai dimenticato username o password?{/t}</label>
	Scrivi qui la tua <label>{t}email{/t}:&nbsp;</label>
	<br /><input class="big" style="width:153px" type="text" title="{t}Username is required{/t}"/></td>
	<input class="bemaincommands" type="submit" value="{t}Send{/t}"/>
</div>


</div>


