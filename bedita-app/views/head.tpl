<script type="text/javascript">
<!--
{literal}

$(document).ready(function(){
	var tempArray = new Array() ;
{/literal}

{assign var ="baseUrl"  value=$html->url('/')}
{section name="m" loop=$moduleList}
	{assign var ="path"  value=$moduleList[m].path}
	{assign var ="color" value=$moduleList[m].color}
	{literal}

	$("#{/literal}{$path}{literal}_gest_menux").attr("style", "background-color:{/literal}{$color}{literal}; color: white; ") ;
	$("#{/literal}{$path}{literal}_gest_menux").bind("mouseover", function(e) 	{ oldBGColor = this.style.backgroundColor; this.style.backgroundColor = "{/literal}{$color}{literal}" ; }) ;
	$("#{/literal}{$path}{literal}_gest_menux").bind("mouseout", function(e) 	{ this.style.backgroundColor = oldBGColor ; }) ;
	$("#{/literal}{$path}{literal}_gest_menux").bind("click", function(e) 		{ if(e.cancelBubble) return false  ; document.location ='{/literal}{$baseUrl}{$path}{literal}' ; }) ;
	{/literal}

{/section}

{literal}
});

{/literal}
//-->
</script>

{strip}

<div id="headerPage">
	<div class="beditaButton" onclick= "javascript:document.location ='{$html->url('/')}'">
		<span style="font:bold 17px Verdana">B.Edita</span><br/><b>&gt;</b>
		<a href="{$html->url('/authentications/logout')}">esci</a><br/><br/>
		<p><b>Consorzio BEdita</b><br/>2007</p>
	</div>
	{section name="m" loop=$moduleList}
		{if ($moduleList[m].status == 'on')}
			{if ($moduleList[m].flag & BEDITA_PERMS_READ)}
	<div class="gest_menux" id="{$moduleList[m].path}_gest_menux">
			     {if (stripos($html->here, $moduleList[m].path) !== false)}
     	<i> * {t}{$moduleList[m].label}{/t}</i>
     			{else}
     	{t}{$moduleList[m].label}{/t}
				{/if}
	</div>
			{else}
     <div class="gest_menux" style="background-color:#DDDDDD; color: white; ">
		{t}{$moduleList[m].label}{/t}
	</div>
			{/if}
		{/if}
	{/section}
</div>

{include file="messages.tpl"}
{/strip}