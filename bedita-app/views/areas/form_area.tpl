{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
<script type="text/javascript">
<!--
{literal}
var langs = {
{/literal}
	{foreach name=i from=$conf->langOptions key=lang item=label}
	"{$lang}":	"{$label}" {if !($smarty.foreach.i.last)},{/if}
	{/foreach}
{literal}
} ;

var validate = null ;

$.validator.setDefaults({ 
	success: function(label) {
		// set &nbsp; as text for IE
		label.html("&nbsp;").addClass("checked");
	}
});

$(document).ready(function(){
	$("#updateform").validate();
	$('div.tabsContainer > ul').tabs();
	$('div.tabsContainer > ul > li > a').changeActiveTabs();
	$('.lang_flags').enableDisableTabs();
	$('#main_lang').mainLang();
	{/literal}
	{foreach key=val item=label from=$conf->langOptions name=langfe}
	{if $val!=$object_lang || empty($object.LangText.title[$val])}
		{literal}$('#area_langs_container > ul').tabs("disable",{/literal}{$smarty.foreach.langfe.iteration}{literal});{/literal}
	{/if}
	{/foreach}
	{literal}
});

{/literal}
//-->
</script>
<div id="containerPage">
<form action="{$html->url('/areas/saveArea')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{include file="../pages/form_header.tpl" delparam="deleteArea/"}
<div class="blockForm" id="errorForm"></div>
<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>
	<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	<input type="hidden" name="data[title]" value="{$object.title|default:''}"/>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Main language{/t}:</td>
		<td class="field">
			<select name="data[lang]" id="main_lang">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
			</select>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Languages versions{/t}:</td>
		<td class="field">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
				<input type="checkbox" name="data[lang_version]" class="lang_flags" title="{$smarty.foreach.langfe.index}" id="flag_{$val}"
					{if $val==$object_lang || !empty($object.LangText.title[$val])} checked="checked"{/if}
					{if $val==$object_lang} disabled="disabled"{/if}/>
				<img src="{$html->webroot}img/flags/{$val}.png" border="0" alt="{$val}"/>&nbsp;
			{/foreach}
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	</table>
	<div id="area_langs_container" class="tabsContainer">
		<ul>
			{foreach key=val item=label from=$conf->langOptions}
			<li><a href="#area_lang_{$val}"><span>{$label}</span></a></li>
			{/foreach}
		</ul>
		{foreach key=val item=label from=$conf->langOptions}
		<div id="area_lang_{$val}">
		<h3><img src="{$html->webroot}img/flags/{$val}.png" border="0" alt="{$val}"/></h3>
		<table class="tableForm" border="0">
		<tr>
			<td class="label">{t}Title{/t}:</td>
			<td class="field">
				<input {if $val==$object_lang}class="{literal}{required:true,minLength:1}{/literal}" title="{t}Title is required{/t}"{/if}
					type="text" name="data[LangText][{$val}][title]"
					value="{$object.LangText.title[$val]|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
			</td>
			<td class="status">&nbsp;</td>
		</tr>
		<tr>
			<td class="label">{t}Public name{/t}:</td>
			<td class="field">
				<input type="text" name="data[LangText][{$val}][public_name]" value="{$object.LangText.public_name[$val]|default:''|escape:'html'|escape:'quotes'}"
				{if $val==$object_lang}class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Public name is required (at least %1 alphanumerical char){/t}"{/if}/>
			</td>
			<td class="status">&nbsp;</td>
		</tr>
		<tr>
			<td class="label">{t}Description{/t}:</td>
			<td class="field">
				<textarea name="data[LangText][{$val}][description]"
				{if $val==$object_lang}class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Description is required (at least %1 alphanumerical char){/t}"{/if}>{$object.LangText.description[$val]|default:''|escape:'html'|escape:'quotes'}</textarea>
			</td>
			<td class="status">&nbsp;</td>
		</tr>
		</table>
		</div>
		{/foreach}
	</div>
	<hr/>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Nickname{/t}:</td>
		<td class="field">
			<input type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}status{/t}:</td>
		<td class="field">
			{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Contact email{/t}:</td>
		<td class="field">
			<input type="text" name="data[email]" value="{$object.email|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Creator{/t}:</td>
		<td class="field">
			<input type="text" name="data[creator]" value="{$object.creator|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Creator is required (at least %1 alphanumerical char){/t}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Publisher{/t}:</td>
		<td class="field">
			<input type="text" name="data[publisher]" value="{$object.publisher|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Publisher is required (at least %1 alphanumerical char){/t}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Rights{/t}:</td>
		<td class="field">
			<input type="text" name="data[rights]" value="{$object.rights|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Rights is required (at least %1 alphanumerical char){/t}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}License{/t}:</td>
		<td class="field">
			<input type="text" name="data[license]" value="{$object.license|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}License is required (at least %1 alphanumerical char){/t}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	</table>
</fieldset>
</div>
{include file="../pages/form_custom_properties.tpl" el=$object}
{include file="../pages/form_permissions.tpl" el=$object recursion=true}
</form>
</div>