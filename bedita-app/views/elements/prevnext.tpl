{if $session->read("prevNext") && !empty($object.id)}
{assign var="prevNext" value=$session->read("prevNext")}
<div class="listobjnav">
	{if $prevNext[$object.id].prev|default:''}
	<a title="prev" href="{$html->url('/')}{$currentModule.path}/view/{$prevNext[$object.id].prev|default:''}">
		‹
	</a>
	{else} ‹ {/if}

	{if $prevNext[$object.id].next|default:''}
	<a title="next" href="{$html->url('/')}{$currentModule.path}/view/{$prevNext[$object.id].next|default:''}">
		›
	</a> 
	{else} › {/if}
</div>
{/if}

