<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	
	{/literal}

	{assign var ="baseUrl"  value=$html->url('/')}
	{section name="m" loop=$moduleList}
		{assign var ="path"  value=$moduleList[m].path}
		{if $path == $moduleName}
		{assign var ="color" value=$moduleList[m].color}
		{else}
			{assign var ="color" value="transparent"}
		{/if}
		{literal}
		
		$("#{/literal}{$path}{literal}_gest_menux").attr("style", "background-color:{/literal}{$color}{literal}; color: white; ") ;
		$("#{/literal}{$path}{literal}_gest_menux").bind("mouseover", function(e) 	{ oldBGColor = this.style.backgroundColor; this.style.backgroundColor = "{/literal}{$moduleList[m].color}{literal}" ; }) ;
		$("#{/literal}{$path}{literal}_gest_menux").bind("mouseout", function(e) 	{ this.style.backgroundColor = oldBGColor ; }) ;
		$("#{/literal}{$path}{literal}_gest_menux").bind("click", function(e) { 
			if(e.cancelBubble) return false  ; 
			document.location ='{/literal}{$baseUrl}{$path}{literal}' ; 
		}) ;
		{/literal}
	
	{/section}
	
	{literal}
	
});

// variables for jquery.changealert.js
var html = " \
	<span id='_hndVisualAlert'><\/span> \
	<input type='checkbox' id='_hndChkbox'> \
	<a id='_cmdCheck' href='#'>{/literal}{t}Remind{/t}{literal}<\/a> \
	<br/> \
	{/literal}{t}Check if you want be notified, whenever you try to leave the page and you changed some data in the form{/t} ({t}changes would be lost if you leave the page{/t}){literal}.\
	";
var datachanged = "* {/literal}{t}data changed{/t}{literal}<br/>";
var changeAlertMessage = "{/literal}{t}The change will be lost. Do you want to continue{/t}{literal}?" ;
{/literal}
//-->
</script>

{strip}

<div id="headerPage">
	<div class="beditaButton" onclick= "javascript:document.location ='{$html->url('/')}'">
		<span style="font:bold 17px Verdana">B.Edita</span>
		<p><b>›</b> <a href="{$html->url('/authentications/logout')}">{t}Exit{/t}</a></p>
		<p><b>BEdita</b><br/>2007</p>
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

{include file="layout_parts/messages.tpl"}
{/strip}