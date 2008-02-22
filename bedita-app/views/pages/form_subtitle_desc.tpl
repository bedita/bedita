<h2 class="showHideBlockButton">{t}Subtitle, description{/t}</h2>
<div class="blockForm" id="subtitle" style="display: none">
<fieldset>
	<table class="tableForm" border="0">
	<tr id="ShortDesc_TR_{$object.lang|default:$conf->defaultLang}">
		<td class="label">{t}Description{/t}:</td>
		<td class="field"><textarea class="shortdesc" name="data[description]">{$object.description|default:''|escape:'html'}</textarea></td>
		<td class="status">	</td>
	</tr>
	{if (isset($object.LangText.description))}
	{foreach name=i from=$object.LangText.description key=lang item=text}
	<tr>
		<td class="label"></td>
		<td class="field">
			<input type='hidden' value='shortdesc' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<textarea class="shortdesc" name="data[LangText][{$smarty.foreach.i.iteration}][txt]">{$text|escape:'html'}</textarea>
		</td>
		<td class="status">
			<select name="data[LangText][{$smarty.foreach.i.iteration}][lang]">
			{html_options options=$conf->langOptions selected=$lang}
			</select>
			&nbsp;&nbsp;
			<input type="button" name="delete" value=" x " onclick="$('../..', this).remove() ;"/>
		</td>
	</tr>
	{/foreach}
	{/if}
	</table>
</fieldset>
</div>