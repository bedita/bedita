


<form action="{$html->url('/admin/users')}" method="post" name="userForm" id="userForm">

		<table class="indexlist">
		<tr>
			
			<th>{t}User{/t}</th>
			<th>{t}Name{/t}</th>
			
			<th>{t}Valid{/t}</th>
			<th>{t}Created{/t}</th>
			<th>{t}Last login{/t}</th>
			<th>{t}Action{/t}</th>

		</tr>
		

		{foreach from=$users item=u}
		
		<tr class="rowList">
			<td><a href="{$html->url('/admin/viewUser/')}{$u.User.id}">{$u.User.userid}</a></td>
			<td>{$u.User.realname}</td>
			
			<td>{$u.User.valid}</td>
			<td>{$u.User.created|date_format:$conf->dateTimePattern}</td>
			<td>{$u.User.last_login|date_format:$conf->dateTimePattern}</td>
			
			{if $module_modify eq '1' && $BEAuthUser.userid ne $u.User.userid}
			<td>
				<input type="button" name="removeUser" value="{t}Remove{/t}" id="user_{$u.User.id}" onclick="javascript:delUserDialog('{$u.User.userid}',{$u.User.id});"/>
			</td>
			{/if}						
			
		
  		{/foreach}

		</table>
		
		
<div class="tab"><h2>Operazioni sui 3 records selezionati</h2></div>
<div>
	<input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label>
	<hr />
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
</div>	
		
		
		</form>

