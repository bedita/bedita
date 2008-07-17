{if isset($moduleList.translations) }
<div class="tab"><h2>{t}Translations{/t}</h2></div>
<fieldset id="translations">

{if !empty($object.LangText.status)}
<table class="indexlist bordered">	
<tr>
	<th>{t}Lang{/t}</th>
	<th>{t}Title{/t}</th>
	<th>{t}Status{/t}</th>
</tr>
{foreach from=$object.LangText.status item=i key=k}
<tr class="rowList" rel="{$html->url('/translations/view/')}{$object.id}/{$k}">
	<td>{$conf->langOptions[$k]}</td>
	<td>{$object.LangText.title.$k}</td>
	<td>{$i}</td>
</tr>
{/foreach}
</table>
{else}
{t}No translations found{/t}
{/if}

<br />
{if $moduleList.translations.flag & $conf->BEDITA_PERMS_MODIFY}
<input type="button" value="{t}add new translation{/t}" onclick="javascript:document.location='{$html->url('/translations/view/')}{$object.id}';"/>
{/if}

</fieldset>
{/if}
