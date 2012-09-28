{if $this->Session->read("prevNext") && !empty($object.id)}
{assign var="prevNext" value=$this->Session->read("prevNext")}
<div class="listobjnav">
	{if $prevNext[$object.id].prev|default:''}
	<a title="prev" href="{$this->Html->url('/')}{$currentModule.url}/view/{$prevNext[$object.id].prev|default:''}">
		‹
	</a>
	{else} ‹ {/if}

	{if $prevNext[$object.id].next|default:''}
	<a title="next" href="{$this->Html->url('/')}{$currentModule.url}/view/{$prevNext[$object.id].next|default:''}">
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
