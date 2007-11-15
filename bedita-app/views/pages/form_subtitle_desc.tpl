<fieldset>
	<table class="tableForm" border="0">
	<tr id="SubTitle_TR_{$object.lang|default:$conf->lang}">
		<td><textarea name="data[subtitle]" class="subtitle">{$object.subtitle|default:''|escape:'html'}</textarea></td>
		{if ($object)}
		<td><input class="cmdField" id="cmdTranslateSubtitle" type="button" value="lang ..."/></td>
		{/if}
	</tr>
	{if (isset($object.LangText.subtitle))}
	{foreach name=i from=$object.LangText.subtitle key=lang item=text}
	<tr>
		<td>
			<input type='hidden' value='subtitle' name="data[LangText][{$smarty.foreach.i.iteration}][name]"/>
			<textarea class="subtitle" name="data[LangText][{$smarty.foreach.i.iteration}][txt]">{$text|escape:'html'}</textarea>
		</td>
		<td>
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