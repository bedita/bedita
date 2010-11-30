{if $session->read("prevNext") && !empty($object.id)}
{assign var="prevNext" value=$session->read("prevNext")}
<div class="listobjnav">
	{if $prevNext[$object.id].prev|default:''}
	<a title="prev" href="{$html->url('/')}{$currentModule.url}/view/{$prevNext[$object.id].prev|default:''}">
		‹
	</a>
	{else} ‹ {/if}

	{if $prevNext[$object.id].next|default:''}
	<a title="next" href="{$html->url('/')}{$currentModule.url}/view/{$prevNext[$object.id].next|default:''}">
		›
	</a> 
	{else} › {/if}

	<div style="margin-top:5px; color:#666 !important; font-size:10px !important; text-align:center">
		{foreach name=c from=$prevNext item=item key=key}
			{if ($key==$object.id)}{$smarty.foreach.c.iteration} / {$prevNext|@count}{/if}
		{/foreach}
	</div>
	
</div>

{/if}
