<script type="text/javascript">
{literal}
function checkPassword(checkNull) {
	var val = $("#data[User][passwd]").val();
	alert(val);
	if(val == null) {
	 	if(checkNull)
			return false;
	 } else {
	   	if (!(val.match("[^a-zA-Z0-9]+")))
			return false;
		if (!(val.match("[0-9]+")))
			return false;
	}
	return true;
}

$(document).ready(function(){

	validateFrm = $("#userForm").validate({
		errorLabelContainer: $("#errorForm"),
		rules: {
			"data[User][userid]"		: "required",
			"data[User][realname]"	: "required",
			"data[User][email]"			: { email : true },
			"data[User][passwd]"	:  { equalTo : "#pwd"},					
			"pwd"			:  { 
{/literal}{if !isset($user)}
					required : true,
			{/if}
{literal}
					minLength : 6
			}
		},
		messages: {
{/literal}
			"data[User][userid]"		: "{t}user name required{/t}",
			"data[User][realname]"	: "{t}real name required{/t}",			
			"data[User][email]"			: "{t}use a valid email{/t}",
			"data[User][passwd]" : {ldelim} equalTo : "{t}passwords should be equal{/t}" {rdelim},
			"pwd"		:  {ldelim}
			{if !isset($user)}
				required : "{t}password is required{/t}",
			{/if}
				minLength : "{t 1='6'}Password should be %1 chars long{/t}"
{literal}	}
		}
	});
});
{/literal}
</script>

<div id="containerPage">
	<div class="FormPageHeader"><h1>{if isset($user)}{t}User settings{/t}{else}{t}New user{/t}{/if}</h1></div>
	<div class="blockForm" id="errorForm"></div>
	<div id="mainForm">
		<form action="{$html->url('/admin/saveUser')}" method="post" name="userForm" id="userForm">
		<table border="0" cellspacing="8" cellpadding="0">
		<tr>
		 	{if isset($user)}
			<input type="hidden" name="data[User][id]" value="{$user.User.id}"/>
			{/if}
			<td>{t}User name{/t}</td>
			<td><input type="text" id="username" name="data[User][userid]" 
				value="{$user.User.userid}" />&nbsp;</td>
		</tr>
		<tr>
			<td>{t}Real name{/t}</td>
			<td><input type="text" name="data[User][realname]" 
			value="{$user.User.realname}" />&nbsp;</td>
		</tr>
		<tr>
			<td>{t}Email{/t}</td>
			<td><input type="text" name="data[User][email]" value="{$user.User.email}"/>&nbsp;</td>
		</tr>
		<tr>
		 	{if isset($user)}
			<td>{t}New password{/t}</td>
			<td><input type="password" name="pwd" value="" id="pwd" />&nbsp;</td>
		 	{else}
			<td>{t}Password{/t}</td>
			<td><input type="password" name="pwd" value="" id="pwd" />&nbsp;</td>
			{/if}
		</tr>
		<tr>
			<td>{t}Confirm password{/t}</td>
			<td><input type="password" name="data[User][passwd]" value=""/>&nbsp;</td>
		</tr>
		<tr>
			<td>{t}Status{/t}</td>
				{if isset($user)}
					{assign var='valid' value=$user.User.valid}
				{else}
					{assign var='valid' value='1' }
				{/if}
			<td>
				<input type="radio" name="data[User][valid]"  id="userValid" 
					value="1" {if $valid}checked="checked"{/if}/ >
					<label for="userValid">{t}Valid{/t}</label>&nbsp;
				<input type="radio" name="data[User][valid]"  id="userNotValid" 
					value="0" {if !$valid}checked="checked"{/if}/ >
					<label for="userNotValid">{t}Blocked{/t}</label>&nbsp;
			</td>
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
		<tr></tr>
		{if isset($userModules)}
		<tr>
			<td>{t}Module access{/t}</td>
			<td><table>
				{foreach from=$userModules item=mod}
				<tr>
					<td><em>{$mod.label}</em></td>
					<td>{if ($mod.flag == $conf->BEDITA_PERMS_READ)}{t}Read only{/t}
						   {elseif ($mod.flag & $conf->BEDITA_PERMS_MODIFY)}{t}Read and modify{/t}
						   {/if}</td>
				</tr>
				{/foreach}
				</table>
			</td>
		</tr>
		{/if}
		<tr></tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="save" class="submit" value="{if isset($user)}{t}Modify{/t}{else}{t}Create{/t}{/if}" />
				<!--  onclick="setRulesNewUser(); if(!checkOnSubmit('userForm',rulesUser)) return false;" /> -->
			</td> 
		</tr>
  		</tbody>
		</table>
		</form>
	</div>
</div>