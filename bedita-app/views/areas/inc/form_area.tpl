<fieldset id="properties">	

{literal}
<script language="JavaScript" type="text/javascript">
$(document).ready(function(){
	
	$(".autogrowarea").autogrow({
		lineHeight: 16
	});
	$(".areaform input[type='text'], .areaform textarea").width(340);
	

});
</script>
{/literal}

{include file="../common_inc/form_common_js.tpl"}

{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
	
	<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
	<input type="hidden" name="data[title]" value="{$object.title|default:''}"/>
	<input type="hidden" name="data[fixed]" value="{$object.fixed|default:0}"/>
	

	<table class="areaform" style="margin-bottom:10px">

		<tr>
			<th>{t}title{/t}:</th>
			<td>
				<input id="titleBEObject" class="{literal}{required:true,minLength:1}{/literal}" title="{t}Title is required{/t}"	type="text" name="data[title]"	value="{$object.title|default:''|escape:'html'|escape:'quotes'}" />
			</td>
		</tr>
		<tr>
			<th>{t}public name{/t}:</th>
			<td>
				<input type="text" name="data[public_name]" value="{$object.public_name|default:''|escape:'html'|escape:'quotes'}""/>
			</td>
		</tr>
		<tr>
			<th>{t}description{/t}:</th>
			<td>
				<textarea class="autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'|escape:'quotes'}</textarea>
			</td>
		</tr>
		<tr>
			<th>{t}status{/t}:</th>
			<td id="status">
				{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator=" "}
			</td>
		</tr>
		<tr>
			<th>
				syndicate:</th>
			<td>
				<div class="ico_rss" style="float:left; vertical-align:middle; margin-right:10px; width:24px; height:24px;">&nbsp;</div>
				<input style="margin-top:4px" type="checkbox" name="data[syndicate]" value="on" {if $object.syndicate|default:'off'=='on'}checked{/if} />
			</td>
		</tr>
	</table>
	


<div class="tab"><h2>{t}More properties{/t}</h2></div>

<div>
	<table class="areaform">
		<tr>
			<th>{t}main language{/t}:</th>
			<td>
				<select name="data[lang]" id="main_lang">
				{foreach key=val item=label from=$conf->langOptions name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
				{/foreach}
				{foreach key=val item=label from=$conf->langsIso name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
				{/foreach}
				</select>
			</td>
		</tr>
	<tr>
		<th>{t}nickname{/t}:</th>
		<td>
			<input id="nicknameBEObject" type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}" />
		</td>
	</tr>
			</tr>
				<tr>
				<th>id:</th>
				<td>{$object.id|default:null}</td>
			</tr>
	</table>
	
	<hr />
	
	<table class="areaform">
	<tr>
		<th>{t}public url{/t}:</th>
		<td>
			<input type="text" name="data[public_url]" value="{$object.public_url|default:''}""/>
		</td>
	</tr>
	
	<tr>
		<th>{t}staging url{/t}:</th>
		<td>
			<input type="text" name="data[staging_url]" value="{$object.staging_url|default:''}""/>
		</td>
	</tr>
	<tr>
		<th>{t}contact email{/t}:</th>
		<td>
			<input type="text" name="data[email]" value="{$object.email|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>
		</td>
	</tr>
	</table>
	
	<hr />
	
	<table class="areaform">
	<tr>
		<th>{t}creator{/t}:</th>
		<td>
			<input type="text" name="data[creator]" value="{$object.creator|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Creator is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}publisher{/t}:</th>
		<td>
			<input type="text" name="data[publisher]" value="{$object.publisher|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Publisher is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}rights{/t}:</th>
		<td>
			<input type="text" name="data[rights]" value="{$object.rights|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}Rights is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	<tr>
		<th>{t}license{/t}:</th>
		<td>
			<input type="text" name="data[license]" value="{$object.license|default:''|escape:'html'|escape:'quotes'}"
			class="{literal}{required:true,minLength:1}{/literal}" title="{t 1='1'}License is required (at least %1 alphanumerical char){/t}"/>
		</td>
		
	</tr>
	</table>

</div>

<div class="tab"><h2>{t}Statistics{/t}</h2></div>
	<fieldset>
		
		<h2>Google analytics</h2>
		<input type="hidden" name="data[stats_provider]" value="{$object.stats_provider|default:''}" />
		<textarea name="data[stats_code]" class="shortdesc autogrowarea" style="font-size:0.8em; color:gray; width:470px;">{$object.stats_code|default:''}</textarea>
		<a href="https://www.google.com/analytics/reporting/?reset=1&id=" target="_blank">
			› access statistics
		</a>
	</fieldset>


	{include file="../common_inc/form_translations.tpl" object=$object|default:null}
	<hr />
	
	{include file="../common_inc/form_custom_properties.tpl"}
	
	{include file="../common_inc/form_permissions.tpl" el=$object|default:null recursion=true}
	

