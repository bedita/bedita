{if !empty($publications)}
	<ul class="menuleft insidecol bordered">
		{foreach from=$publications item=item}
			{if !empty($item.public_url)}
			<li><a target="_blank" href="{$item.public_url}" title="{$item.public_name} | {$item.public_url}">
				{$item.public_name|default:$item.public_url}</a>
			</li>
			{/if}
		{/foreach}
	</ul>
{/if}
