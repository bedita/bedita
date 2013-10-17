<div id="map" style="float:left">
<ul>
{foreach from=$steps key='key' item='i' name='step'}
	{if $smarty.foreach.step.iteration > $page}
		{assign var='mclass' value='todo'}
	{/if}
	{if $smarty.foreach.step.iteration == $page}
		{assign var='mclass' value='curr'}
	{/if}
	{if $smarty.foreach.step.iteration < $page}
		{assign var='mclass' value='done'}
	{/if}
	<li class="{$mclass|default:'todo'}">{$i}</li>
{/foreach}
</ul>
</div>
