{if isset($moduleList.translations)}

<div class="tab"><h2 {if empty($object.LangText)}class="empty"{/if}>{t}Translations{/t}</h2></div>
<fieldset id="translations">

{if !empty($object.LangText.status)}
<table class="indexlist bordered">	
<tr>
	<th>{t}lang{/t}</th>
	<th>{t}title{/t}</th>
	<th>{t}status{/t}</th>
</tr>
{foreach from=$object.LangText.status item=i key=k}
<tr>
	<td><a href="{$html->url('/translations/view/')}{$object.id}/{$k}">{$conf->langOptions[$k]}</a></td>
	<td><a href="{$html->url('/translations/view/')}{$object.id}/{$k}">{$object.LangText.title.$k|default:""|escape}</a></td>
	<td><a href="{$html->url('/translations/view/')}{$object.id}/{$k}">{$i}</a></td>
</tr>
{/foreach}
</table>
{else}
{t}No translations found{/t}
{/if}

<br />
{if $moduleList.translations.flag & $conf->BEDITA_PERMS_MODIFY}
<input type="button" value="{t}create new translation{/t}" onclick="javascript:document.location='{$html->url('/translations/view/')}{$object.id}';"/>
{/if}

</fieldset>
{/if}
