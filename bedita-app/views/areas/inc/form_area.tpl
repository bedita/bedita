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
	{if $val!=$object_lang && empty($object.LangText.title[$val])}
		{literal}$('#area_langs_container > ul').tabs("disable",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
	{elseif $val==$object_lang}
		{literal}$('#area_langs_container > ul').tabs("select",{/literal}{$smarty.foreach.langfe.index}{literal});{/literal}
	{/if}
	{/foreach}
	{literal}
});

{/literal}
//-->
</script>


<form action="{$html->url('/areas/saveArea')}" method="post" name="updateForm" id="updateForm" class="cmxform">


<div class="tab"><h2>{t}Properties{/t}</h2></div>

<fieldset id="properties">	

	
	<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	<input type="hidden" name="data[title]" value="{$object.title|default:''}"/>
	
	<div id="properties_langs_choice" class="tabsContainer">
		
		<span class="label">&nbsp;{t}Main language{/t}:</span>
		<span class="field">
			<select name="data[lang]" id="main_lang">
			{foreach key=val item=label from=$conf->langOptions name=langfe}
			<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
			{/foreach}
			</select>
		</span>
		<span class="label" style="margin-left: 16px;">{t}Versions{/t}:</span>
		
		
	</div>
	
	<div id="area_langs_container" class="tabsContainer">
				
			
		<div id="area_lang_{$val}">
		
		<table class="tableForm" border="0">
		<tr>
			<th>{t}Title{/t}:</th>
			<td>
				<input class="{literal}{required:true,minLength:1}{/literal}" title="{t}Title is required{/t}"
					type="text" name="data[title]"
					value="{$object.title|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
			</td>
			
		</tr>
		<tr>
			<th>{t}Public name{/t}:</th>
			<td>
				<input type="text" name="data[public_name]" value="{$object.public_name|default:''|escape:'html'|escape:'quotes'}"
					class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Public name is required (at least %1 alphanumerical char){/t}"/>
			</td>
			
		</tr>
		<tr>
			<th>{t}Description{/t}:</th>
			<td>
				<textarea name="data[description]"
					class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Description is required (at least %1 alphanumerical char){/t}">{$object.description|default:''|escape:'html'|escape:'quotes'}</textarea>
			</td>
			
		</tr>
		</table>
		</div>
		
	</div>
	

	
	<table class="bordered">
	<tr>
		<th>{t}Nickname{/t}:</th>
		<td>
			<input type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}status{/t}:</th>
		<td>
			{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
		</td>
		
	</tr>
	<tr>
		<th>{t}Contact email{/t}:</th>
		<td>
			<input type="text" name="data[email]" value="{$object.email|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}Creator{/t}:</th>
		<td>
			<input type="text" name="data[creator]" value="{$object.creator|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Creator is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}Publisher{/t}:</th>
		<td>
			<input type="text" name="data[publisher]" value="{$object.publisher|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Publisher is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}Rights{/t}:</th>
		<td>
			<input type="text" name="data[rights]" value="{$object.rights|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Rights is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}License{/t}:</th>
		<td>
			<input type="text" name="data[license]" value="{$object.license|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}License is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	</table>

</fieldset>

	
{include file="../common_inc/form_custom_properties.tpl" el=$object}
{include file="../common_inc/form_permissions.tpl" el=$object recursion=true}


	
	

</form>
