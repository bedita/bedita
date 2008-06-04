


<form action="{$html->url('/admin/users')}" method="post" name="userForm" id="userForm">

		<table class="indexlist">
		<tr>
			
			<th>{t}User{/t}</th>
			<th>{t}Name{/t}</th>
			
			<th>{t}Valid{/t}</th>
			<th>{t}Created{/t}</th>
			<th>{t}Last login{/t}</th>

		</tr>
		

		{foreach from=$users item=u}
		
		<tr class="rowList" rel="{$html->url('/admin/viewUser/')}{$u.User.id}">
			<td>{$u.User.userid}</td>
			<td>{$u.User.realname}</td>
			
			<td>{$u.User.valid}</td>
			<td>{$u.User.created}</td>
			<td>{$u.User.last_login}</td>
			
			{*if $module_modify eq '1'}
			<td>
				<input type="button" name="modifyUser" value="{t}Modify{/t}" id="view_{$u.User.id}" onclick="javascript:viewUser({$u.User.id});"/>
				<input type="button" name="deleteUser" value="{t}Remove{/t}" id="user_{$u.User.id}" onclick="javascript:delUserDialog('{$u.User.userid}',{$u.User.id});"/>
			</td>
			{/if*}						
			
		
  		{/foreach}

		</table>
		
		
<div class="tab"><h2>Operazioni sui 3 records selezionati</h2></div>
<div>
	<input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label>
	<hr />
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
</div>	
		
		
		</form>

