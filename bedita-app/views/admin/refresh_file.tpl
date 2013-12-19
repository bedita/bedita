{if !empty($log) && !empty($log[0])}
<ul>
{assign_associative var='optsError' format='<span class="highlight">\1</span>'}
{assign_associative var='optsException' format='<span class="highlight">\1</span>'}
{foreach from=$log item='logrow' key='kk'}
	{assign var='logrow1' value=$text->highlight($logrow,'Error',$optsError)}
	{assign var='logrow2' value=$text->highlight($logrow1,'Exception',$optsException)}
	<li>{$logrow2}</li>
{/foreach}
</ul>
{else}
	<br/>{t}File is empty{/t}
{/if}
