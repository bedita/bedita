<div id="containerPage">
	<div class="FormPageHeader"><h1>{if isset($user)}{t}User settings{/t}{else}{t}New user{/t}{/if}</h1></div>
	<div id="mainForm">
		<form action="{$html->url('/admin/saveUser')}" method="post" name="userForm" id="userForm">
		<table border="0" cellspacing="8" cellpadding="0">
		<tr>
		 	{if isset($user)}
			<input type="hidden" name="data[User][id]" value="{$user.User.id}"/>
			{/if}
			<td>{t}User name{/t}</td>
			<td><input type="text" name="data[User][userid]" value="{$user.User.userid}"/>&nbsp;</td>
		</tr>
		<tr>
			<td>{t}Real name{/t}</td>
			<td><input type="text" name="data[User][realname]" value="{$user.User.realname}"/>&nbsp;</td>
		</tr>
		<tr>
			<td>{t}Email{/t}</td>
			<td><input type="text" name="data[User][email]" value="{$user.User.email}"/>&nbsp;</td>
		</tr>
		<tr>
		 	{if isset($user)}
			<td>{t}New password{/t}</td>
			<td><input type="password" name="data[User][passwd-new]" value=""/>&nbsp;</td>
		 	{else}
			<td>{t}Password{/t}</td>
			<td><input type="password" name="data[User][passwd]" value=""/>&nbsp;</td>
			{/if}
		</tr>
		<tr>
			<td>{t}Confirm password{/t}</td>
			<td><input type="password" name="data[User][passwd-confirm]" value=""/>&nbsp;</td>
		</tr>
		<tr>
			<td>{t}Groups{/t}</td>
			<td><table>
				{foreach from=$formGroups key=gname item=u}
				<tr>
					<td><input type="checkbox" name="data[groups][{$gname}]" {if $u == 1}checked="checked"{/if}></td>
					<td>{$gname}</td>
				</tr>
				{/foreach}
			</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="save" class="submit" value="{if isset($user)}{t}Modify{/t}{else}{t}Create{/t}{/if}" />
			</td> 
		</tr>
  		</tbody>
		</table>
		</form>
	</div>
</div>