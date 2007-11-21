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

function localUpdateGroupsChecked(chkElem) {
	if(chkElem.checked) {
		document.getElementById("groups").value="true";
	} else {
		var nchecked = 0;
		var formElem = document.getElementById("userForm");
		for(i=0;i<formElem.lenght;i++) {
			if(i<3) alert('test');
			name = formElem.elements[i].name;
			if(name.indexOf('group_') != -1) {
				if(formElem.elements[i].checked) {
					nchecked = 1;
					break;
				}
			}
		}
		document.getElementById("groups").value = (nchecked>0) ? "checked" : "";
	}
}

$.validator.setDefaults({ 
	submitHandler: function() { alert("submitted!"); },
	success: function(label) {
		// set &nbsp; as text for IE
		label.html("&nbsp;").addClass("checked");
	}
});
$().ready(function() { 
	$("#userForm").validate(); 
});

{/literal}
</script>

<div id="containerPage">
	<div class="FormPageHeader"><h1>{if isset($user)}{t}User settings{/t}{else}{t}New user{/t}{/if}</h1></div>
	<div class="blockForm" id="errorForm"></div>
	<div id="mainForm">
		<form action="{$html->url('/admin/saveUser')}" method="post" name="userForm" id="userForm" class="cmxform">
		<fieldset>
		<table border="0" cellspacing="8" cellpadding="0">
		<tr>
			<td class="label">
				<label id="lusername" for="username">{t}User name{/t}</label>
				{if isset($user)}<input type="hidden" name="data[User][id]" value="{$user.User.id}"/>{/if}
			</td>
			<td class="field"><input type="text" id="username" name="data[User][userid]" class="{literal}{required:true}{/literal}" 
				value="{$user.User.userid}" title="{t}User name is required{/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label"><label id="lrealname" for="realname">{t}Real name{/t}</label></td>
			<td class="field">
				<input type="text" id="realname" name="data[User][realname]" value="{$user.User.realname}"
				class="{literal}{required:true}{/literal}" title="{t}Real name is required{/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label"><label id="lemail" for="email">{t}Email{/t}</label></td>
			<td class="field"><input type="text" id="email" name="data[User][email]" value="{$user.User.email}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		
		<tr>
		 	<td class="label">{if isset($user)}{t}New password{/t}{else}{t}Password{/t}{/if}</td>
			<td class="field"><input type="password" name="pwd" value="" id="pwd" 
			class="{if isset($user)}{literal}{required:true,minLength:6}{/literal}{else}{literal}{minLength:6}{/literal}{/if}" title="{t 1='6'}Password is required (at least %1 chars){/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label">{t}Confirm password{/t}</td>
			<td class="field"><input type="password" name="data[User][passwd]" value=""
			class="{literal}{equalTo:'#pwd'}{/literal}" title="{t}Passwords should be equal{/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label">{t}Status{/t}</td>
				{if isset($user)}
					{assign var='valid' value=$user.User.valid}
				{else}
					{assign var='valid' value='1' }
				{/if}
			<td class="field">
				<input type="radio" name="data[User][valid]"  id="userValid" 
					value="1" {if $valid}checked="checked"{/if} />
					<label for="userValid">{t}Valid{/t}</label>&nbsp;
				<input type="radio" name="data[User][valid]"  id="userNotValid" 
					value="0" {if !$valid}checked="checked"{/if} />
					<label for="userNotValid">{t}Blocked{/t}</label>&nbsp;
			</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label"><label id="lgroups" for="groups">{t}Groups{/t}</label></td>
			<td class="field"><input type="hidden" name="groups" id="groups"
				class="{literal}{required:true}{/literal}" title="{t}Check at least one group{/t}"/></td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label">&#160;</td>
			<td class="field">
				<table>
				{foreach from=$formGroups key=gname item=u}
				<tr>
					<td class="field"><input type="checkbox" id="group_{$gname}" name="data[$gname]" {if $u == 1}checked="checked"{/if}
					onclick="javascript:localUpdateGroupsChecked(this);"/></td>
					<td class="label"><label id="lgroup{$gname}" for="group{$gname}">{$gname}</label></td>
					<td class="status">&#160;</td>
				</tr>
				{/foreach}
				</table>
			</td>
			<td class="status">&#160;</td>
		</tr>
		{if isset($userModules)}
		<tr>
			<td class="label">{t}Module access{/t}</td>
			<td class="field">
				<table>
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
			<td class="status">&#160;</td>
		</tr>
		{/if}
		<tr>
			<td class="label">&#160;</td>
			<td class="field" colspan="2">
				<input type="submit" name="save" class="submit" value="{if isset($user)}{t}Modify{/t}{else}{t}Create{/t}{/if}" />
			</td> 
		</tr>
		</table>
		</fieldset>
		</form>
	</div>
</div>