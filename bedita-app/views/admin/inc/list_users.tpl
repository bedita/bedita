{include file="./inc/toolbar.tpl" label_items='system users'}

<form action="{$html->url('/admin/users')}" method="post" name="userForm" id="userForm">

<table class="indexlist">
	<tr>
		<th>{$paginator->sort('User', 'userid')}</th>
		<th>{$paginator->sort('Name', 'realname')}</th>
		<th>{$paginator->sort('Valid', 'valid')}</th>
		<th>{$paginator->sort('Created', 'created')}</th>
		<th>{$paginator->sort('Last login', 'last_login')}</th>
		<th>{t}Action{/t}</th>
	</tr>
	{foreach from=$users item=u}
	<tr>
		<td><a href="{$html->url('/admin/viewUser/')}{$u.User.id}">{$u.User.userid}</a></td>
		<td>{$u.User.realname}</td>
		<td>{$u.User.valid}</td>
		<td>{$u.User.created|date_format:$conf->dateTimePattern}</td>
		<td>{$u.User.last_login|date_format:$conf->dateTimePattern}</td>
		<td>
			{if $module_modify eq '1' && $BEAuthUser.userid ne $u.User.userid}
			<input type="button" name="removeUser" value="{t}Remove{/t}" id="user_{$u.User.id}" onclick="javascript:delUserDialog('{$u.User.userid}',{$u.User.id});"/>
			{/if}
		</td>
	{/foreach}
</table>

<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div>
	<input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label>
	<hr />
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
</div>

</form>