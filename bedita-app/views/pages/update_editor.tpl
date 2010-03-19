{if $editors|@count > 1}
<script>
	$(".secondacolonna .modules label").addClass("concurrentuser").attr("title","Warning! More users are editing this document");
</script>	

{t}Concurrent editors:{/t}
<ul>
{foreach from=$editors name=i item=item}
	<li rel="{$item.User.id}">
		<b>{$item.User.realname}</b>
	</li>
{/foreach}
</ul>
{/if}