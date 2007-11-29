<div id="containerPage">
	<div class="FormPageHeader"><h1>{t}Users admin{/t}</h1></div>
	<div id="mainForm">
		<form action="{$html->url('/admin/users')}" method="post" name="userForm" id="userForm">
		<table class="indexList">
		<thead><tr><th>{t}User{/t}</th>
				<th>{t}Name{/t}</th>
				<th>{t}Email{/t}</th>
				<th>{t}Valid{/t}</th>
				<th>{t}Created{/t}</th>
				<th>{t}Last login{/t}</th>
				<th>{t}Actions{/t}</th>
				</tr>
		</thead>
		<tbody>
		{foreach from=$users item=u}
		<tr class="rowList">
			<td><a href="{$html->url('/admin/viewUser/')}{$u.User.id}">{$u.User.userid}</a></td>
			<td>{$u.User.realname}</td>
			<td>{$u.User.email}</td>
			<td>{$u.User.valid}</td>
			<td>{$u.User.created}</td>
			<td>{$u.User.last_login}</td>
			<td>
				<input type="button" name="modifyUser" value="{t}Modify{/t}" id="view_{$u.User.id}" onclick="javascript:viewUser({$u.User.id});"/>
				<input type="button" name="deleteUser" value="{t}Remove{/t}" id="user_{$u.User.id}" onclick="javascript:delUserDialog('{$u.User.userid}',{$u.User.id});"/>
			</td>
		</tr>
  		{/foreach}
  		</tbody>
		</table>
		</form>
	</div>
</div>