<form action="{$html->url('/pages/subscribeNewsletter')}" method="post">
	Email: <input type="text" name="data[newsletter_email]" size="30"/>
	<br/>Mode:
	<select name="data[mode]">
		<option value="0">{t}basic - no email confirmation, email subscribed feedback only{/t}</option>
		<option value="1">{t}advanced - with email confirmation{/t}</option>
	</select>
	{if !empty($groupsByArea)}
	<br/>Newsletter(s):
	{foreach from=$groupsByArea item='i' name='g'}
	<br/><input type="checkbox" name="data[joinGroup][{$smarty.foreach.g.iteration}][mail_group_id]" value="{$i.id}"/> {$i.group_name}
	{/foreach}
	{/if}
	<br/><input type="submit" value="{t}subscribe{/t}"/>
</form>