{if !empty($mailgroups)}
	<form action="{$html->url('/hashjob/newsletter_subscribe')}" method="post">
		Email: <input type="text" name="data[newsletter_email]" size="30"/>
		<br/>Newsletter(s):
		{foreach from=$mailgroups item='i' name='g'}
		<br/><input type="checkbox" name="data[joinGroup][{$smarty.foreach.g.index}][mail_group_id]" value="{$i.MailGroup.id}"/> {$i.MailGroup.group_name}
		{/foreach}
		<br/><input type="submit" value="{t}subscribe{/t}"/>
	</form>
{else}
	{t}No newsletter to subscribe in{/t}
{/if}