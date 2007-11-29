<fieldset>
	<table class="tableForm" border="0">
	<tr>
		<td class="label">{t}Language{/t}:</td>
		<td class="field">
			<select name="data[lang]">
			{html_options options=$conf->langOptions selected=$object.lang|default:$conf->lang}
			</select>
		</td>
		<td class="status">&nbsp;</td>
	</tr>
	<tr id="SubTitle_TR_{$object.lang|default:$conf->lang}">
		<td class="label">{t}Subtitle{/t}:</td>
		<td class="field"><textarea class="subtitle" name="data[subtitle]" class="subtitle">{$object.subtitle|default:''|escape:'html'}</textarea></td>
		<td class="status">{if ($object)}<input class="cmdField" id="cmdTranslateSubTitle" type="button" value="lang ..."/>{/if}</td>
	</tr>
	{if (isset($object.LangText.subtitle))}
	{foreach name=i from=$object.LangText.subtitle key=lang item=text}
	<tr>
		<td class="label"></td>
		<td class="field">
			<input type='hidden' value='subtitle' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<textarea class="subtitle" name="data[LangText][{$smarty.foreach.i.iteration}][txt]">{$text|escape:'html'}</textarea>
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
	<tr id="ShortDesc_TR_{$object.lang|default:$conf->lang}">
		<td class="label">{t}Description{/t}:</td>
		<td class="field"><textarea class="shortdesc" name="data[shortDesc]">{$object.shortDesc|default:''|escape:'html'}</textarea></td>
		<td class="status">{if ($object)}<input class="cmdField" id="cmdTranslateShortDesc" type="button" value="lang ..."/>{/if}</td>
	</tr>
	{if (isset($object.LangText.shortDesc))}
	{foreach name=i from=$object.LangText.shortDesc key=lang item=text}
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