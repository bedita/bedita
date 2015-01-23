{if !empty($publications)}
	<ul class="menuleft insidecol bordered">
		{foreach from=$publications item=item}
			{if !empty($item.public_url)}
			<li style="padding-right:5px; word-break: break-all"><a target="_blank" href="{$item.public_url}" title="{$item.public_name|escape} | {$item.public_url}">
				{$item.public_name|escape|default:$item.public_url}</a>
			</li>
			{/if}
		{/foreach}
	</ul>
{/if}
