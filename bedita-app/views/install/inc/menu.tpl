<div id="map" style="float:left">
<ul>
{foreach from=$steps key='key' item='i' name='step'}
	{if $smarty.foreach.step.index > $page-1}
		{assign var='mclass' value='todo'}
	{/if}
	{if $smarty.foreach.step.index == $page-1}
		{assign var='mclass' value='curr'}
	{/if}
	{if $smarty.foreach.step.index < $page-1}
		{assign var='mclass' value='done'}
	{/if}
	<li class="{$mclass|default:'todo'}">{$i}</li>
{/foreach}
</ul>
</div>
