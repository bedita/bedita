<script type="text/javascript">
{literal}
$(document).ready(function(){

	$(".indexlist TD").not(".checklist").not(".go").css("cursor","pointer").click(function(i) {
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
});

{/literal}
//-->
</script>	


<form action="{$html->url('/admin/users')}" method="post" name="userForm" id="userForm">

<table class="indexlist">
	<tr>
		<th>{$paginator->sort('id', 'id')}</th>
		<th>{$paginator->sort('User', 'userid')}</th>
		<th>{$paginator->sort('Name', 'realname')}</th>
		<th>{$paginator->sort('Blocked', 'valid')}</th>
		<th>{$paginator->sort('Created', 'created')}</th>
		<th>{$paginator->sort('Last login', 'last_login')}</th>
		<th>{t}Action{/t}</th>
	</tr>
	{foreach from=$users item=u}
	<tr>
		<td><a href="{$html->url('/admin/viewUser/')}{$u.User.id}">{$u.User.id}</a></td>
		<td>{$u.User.userid}</td>
		<td>{$u.User.realname}</td>
		<td>{if $u.User.valid=='1'}{t}No{/t}{else}{t}Yes{/t}{/if}</td>
		<td>{$u.User.created|date_format:$conf->dateTimePattern}</td>
		<td>{$u.User.last_login|date_format:$conf->dateTimePattern}</td>
		<td class="go">
			{if $module_modify eq '1' && $BEAuthUser.userid ne $u.User.userid}
			<input type="button" name="removeUser" value="{t}Remove{/t}" id="user_{$u.User.id}" onclick="javascript:delUserDialog('{$u.User.userid}',{$u.User.id});"/>
			{/if}
		</td>
	{/foreach}
</table>

</form>