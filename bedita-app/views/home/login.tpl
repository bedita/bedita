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
	 <div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
	 
	 
	 	<div class="insidecol">
	
		<a href="css-class.html">› sed diam nonum</a>
		<br />
		<a href="testinside.html">› nibh euismod</a>
		<br />
		<a href="testinside2.html">› nonummy nibh</a>
		<hr />
		un software di <strong>Chialab</strong> and <strong>Channelweb</strong>

	</div>
	 
</div>


<div class="secondacolonna">

	<div class="modules">
	   <label class="admin">{t}Login{/t}</label>
	</div> 
	
	{include file="../common_inc/messages.tpl"}
	

</div>


<div style="width:180px; margin-left:310px; padding-top:25px;">
<form action="{$html->url('/authentications/login')}" method="post" name="loginForm" id="loginForm" class="cmxform">
	<fieldset>
		<input type="hidden" name="data[login][URLOK]" value="{$beurl->here()}" id="loginURLOK" />
		
		<label class="block" id="luserid" for="userid">{t}Username{/t}</label>
		<input style="width:103px" type="text" name="data[login][userid]" id="userid" class="{literal}{required:true}{/literal}" title="{t}Username is required{/t}"/></td>
		<label class="block" id="lpasswd" for="passwd">{t}Password{/t}</label>
		<input style="width:103px" type="password" name="data[login][passwd]" id="passwd" class="{literal}{required:true}{/literal}" title="{t}Password is required{/t}"/>
		
		<input class="bemaincommands" type="submit" value="{t}Enter{/t}"/>
	</fieldset>
</div>

