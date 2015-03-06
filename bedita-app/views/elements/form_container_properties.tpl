<script type="text/javascript"
$(document).ready(function(){
	$('#properties_langs_container > ul').tabs();
});
</script>
<h2 class="showHideBlockButton">{t}Properties{/t}</h2>
<div class="blockForm" id="properties">
<fieldset>
	<input type="hidden" name="data[title]" value="{$object.title|default:''}" />

	<div id="properties_langs_container">
		<ul>
			{foreach key=val item=label from=$conf->langOptions}
			<li><a href="#property_lang_{$val}"><span>{$label}</span></a></li>
			{/foreach}
		</ul>
		{foreach key=val item=label from=$conf->langOptions}
		<div id="property_lang_{$val}">
		<h3><img src="{$html->webroot}img/flags/{$val}.png" border="0" alt="{$val}"/></h3>
		<table class="tableForm" border="0">
		<tr>
			<td class="label">{t}title{/t}:</td>
			<td class="field">
				<input {if $val==$object_lang}class="{ required:true,minLength:1}" title="{t}Title is required{/t}"{/if}
					type="text" name="data[LangText][{$val}][title]"
					value="{$object.LangText.title[$val]|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
			</td>
			<td class="status">&nbsp;</td>
		</tr>
		</table>
		</div>
		{/foreach}
	</div>

	<hr/>
	<span style="font-weight:bold;">{t}status{/t}:</span>:
	{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator=" "}
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}default language{/t}:</td>
		<td>
			<select name="data[lang]">
			{assign var=object_lang value=$object.lang|default:$conf->defaultLang}{html_options options=$conf->langOptions selected=$object.lang|default:$conf->defaultLang}
			</select>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="label">{t}nickname{/t}:</td>
		<td class="field">
			<input type="text" name="data[nickname]" value="{$object.nickname|default:''|escape:'html'|escape:'quotes'}"/>&nbsp;
		</td>
	</tr>
	</table>

	{if ($object)}
	<hr/>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}created{/t}:</td><td>{$object.created|date_format:$conf->datePattern}</td>
		<td class="label">{t}from{/t}:</td><td>{$object.UserCreated.userid|default:""|escape}</td>
	</tr>
	<tr>
		<td class="label">{t}last modified{/t}:</td><td>{$object.modified|date_format:$conf->datePattern}</td>
		<td class="label">{t}from{/t}:</td><td>{$object.UserModified.userid|default:""|escape}</td>
	</tr>
	<tr>
		<td class="label">{t}IP{/t}:</td><td>{$object.ip_created}</td>
	</tr>
	</table>
	{/if}
</fieldset>
</div>