{foreach from=$editors name=i item=item}
	<li rel="{$item.User.id}">
		<em>{$item.User.realname}</em>
	</li>
{/foreach}