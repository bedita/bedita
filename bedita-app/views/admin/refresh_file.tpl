{if !empty($log) && !empty($log[0])}
<ul>
{foreach from=$log item='logrow' key='kk'}
	<li>{$logrow}</li>
{/foreach}
</ul>
{else}
	<br/>{t}File is empty{/t}
{/if}
