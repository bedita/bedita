{if !empty($u)}
<tr>
	<td>
		<input type="hidden" name="data[users][{$u.id}]" value="{$u.id}" />
		<a href="{$html->url('/users/viewUser/')}{$u.id}">{$u.userid|escape}</a>
	</td>
	<td>
		{$u.realname|escape}
	</td>
	<td>
		{$u.email}
	</td>
	<td>
		{$u.auth_type|default:'bedita'}
	</td>
	<td class="commands">
		<a class="BEbutton remove">x</a>
	</td>
</tr>
{/if}