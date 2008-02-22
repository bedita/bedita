<script type="text/javascript">
<!--
var urlDelete = "{$html->url('deleteArea/')}" ;
var message = "{t}Are you sure that you want to delete the publication?{/t}" ;

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
	$("#updateForm").validate();
	
	// submit delete
	$("#delBEObject").bind("click", function() {
		if(!confirm(message)) {
			return false ;
		}
		$("#updateForm").attr("action", urlDelete)
		$("#updateForm").submit();
	}) ;
	
	// Aggiunta traduzioni linguistiche dei campi
	$("#cmdTranslateTitle").addTranslateField('title', langs) ;
	$("#cmdTranslateSubTitle").addTranslateField('subtitle', langs) ;
	$("#cmdTranslateShortDesc").addTranslateField('shortdesc', langs) ;
	$("#cmdTranslateLongDesc").addTranslateField('longdesc', langs) ;
});

{/literal}
//-->
</script>
<div id="containerPage">
<form action="{$html->url('/areas/saveArea')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<fieldset><input  type="hidden" name="data[id]" value="{$object.id|default:''}"/></fieldset>
{include file="../pages/form_header.tpl"}
<div class="blockForm" id="errorForm"></div>
<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Title{/t}:</td>
		<td class="field">
			<input class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Title is required (at least %1 alphanumerical char){/t}" id="titleInput"  type="text" 
				name="data[title]" value="{$object.title|default:''|escape:'html'|escape:'quotes'}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Public name{/t}:</td>
		<td class="field">
			<input type="text" name="data[public_name]" value="{$object.public_name|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Public name is required (at least %1 alphanumerical char){/t}"/>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
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
		<td class="label">{t}Language{/t}:</td>
		<td class="field">
			<select name="data[lang]">
			{html_options options=$conf->langOptions selected=$object.lang|default:$conf->defaultLang}
			</select>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}Description{/t}:</td>
		<td class="field">
			<textarea name="data[description]"
			class="{literal}{required:true,minLength:1}{/literal}" 
			title="{t 1='1'}Description is required (at least %1 alphanumerical char){/t}">{$object.description|default:''|escape:'html'|escape:'quotes'}</textarea>
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