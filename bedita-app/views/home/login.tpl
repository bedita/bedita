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
	 <div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
	 
	 
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
		<input class="big" style="width:103px" type="text" name="data[login][userid]" id="userid" class="{literal}{required:true}{/literal}" title="{t}Username is required{/t}"/>
		<label class="block" id="lpasswd" for="passwd">{t}Password{/t}</label>
		<input class="big" style="width:103px; margin-bottom:10px;" type="password" name="data[login][passwd]" id="passwd" class="{literal}{required:true}{/literal}" title="{t}Password is required{/t}"/>
		
		<input class="bemaincommands" type="submit" value="{t}Enter{/t}"/>
	</fieldset>
	</form>
</div>

<div class="quartacolonna" style="border-left:1px solid gray; padding:120px 0px 0px 10px; width:420px; left:440px; top:20px;">
<label class="block"><a href='javascript:void(0)' onClick="$('#pswforget').toggle('fast')">{t}Forgotten username or password?{/t}</a></label>
<div id="pswforget" style="display:none">
	{t}Write your email here{/t}:&nbsp;
	<br />
	<input class="big" style="width:153px" type="text" title="{t}Username is required{/t}"/>
	<input class="bemaincommands" type="submit" value="{t}Send{/t}"/>
	
	<hr />
	{if isset($conf->projectAdmin)}
	{t}or{/t} <label><a href="mailto:{$conf->projectAdmin}">{t}contact the project admin{/t}</a></label>{/if}
</div>


</div>


