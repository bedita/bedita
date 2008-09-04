
<div class="tab"><h2>{t}Embedded in{/t}</h2></div>
<fieldset id="relationships">
	{if !empty($object.relations.attach)}
		{foreach from=$object.relations.attach item="o"}
		<ul class="bordered">
		
			<li><span title="{$o.ObjectType.name}" class="listrecent {$o.ObjectType.module}">&nbsp;</span>
			<a href="{$html->url('/')}{$o.ObjectType.module}/view/{$o.id}">{$o.title}</a></li>
		
		</ul>
		{/foreach}

	{else}
		{t}Embedded in no objects{/t}
	{/if}


</fieldset>
