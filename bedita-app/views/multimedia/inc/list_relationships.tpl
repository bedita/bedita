
<div class="tab"><h2>{t}Referenced in{/t}</h2></div>
<fieldset id="relationships">
	{if empty($object.relations)}
		{t}No references{/t}
	{else}
		{foreach from=$object.relations key="name" item="related"}
		<h3>{$name}:</h3>
			{foreach from=$related item="o"}
			<ul class="bordered">
			
				<li><span title="{$o.ObjectType.name}" class="listrecent {$o.ObjectType.module_name}">&nbsp;</span>
				<a href="{$html->url('/')}{$o.ObjectType.module_name}/view/{$o.id}">{$o.title|default:'<i>[no title]</i>'}</a></li>
			
			</ul>
			{/foreach}
		{/foreach}
	{/if}


</fieldset>
