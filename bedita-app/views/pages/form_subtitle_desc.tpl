<h2 class="showHideBlockButton">{t}Subtitle, description{/t}</h2>
<div class="blockForm" id="subtitle" style="display: none">
<fieldset>
	<div id="subtitle_langs_container" class="tabsContainer">
		<ul>
			{foreach key=val item=label from=$conf->langOptions}
			<li><a href="#subtitle_lang_{$val}"><span>{$label}</span></a></li>
			{/foreach}
		</ul>
		{foreach key=val item=label from=$conf->langOptions}
		<div id="subtitle_lang_{$val}">
		<h3><img src="{$html->webroot}img/flags/{$val}.png" border="0" alt="{$val}"/></h3>
		<table class="tableForm" border="0">
		<tr>
			<td class="label">{t}Description{/t}:</td>
			<td class="field">
				<textarea class="shortdesc" name="data[LangText][{$val}][description]">{$object.LangText.description[$val]|default:''|escape:'html'}</textarea>
			</td>
			<td class="status">&nbsp;</td>
		</tr>
		</table>
		</div>
		{/foreach}
	</div>
</fieldset>
</div>