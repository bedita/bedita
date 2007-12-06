<script type="text/javascript">
{literal}

function cutBlank(elem) {
	if (elem.value.length > 0) {
		var i = elem.value.length - 1;
		var c = elem.value.charAt(i);
		if (c == ' ') {
			elem.value = elem.value.substring(0,i);
		}
	}
}

function localGroupChecked() {
	var formElem = document.getElementById("userForm");
	for(i=0;i<formElem.length;i++) {
		id = formElem.elements[i].id;
		if(id.indexOf('group_') != -1) {
			if(formElem.elements[i].checked) {
				return true;
			}
		}
	}
	return false;
}

function localSetGroupChecked() {
	document.getElementById("groups").value = localGroupChecked() ? "checked" : "";
}

function localUpdateGroupsChecked(chkElem) {
	document.getElementById("groups").value = (chkElem.checked || localGroupChecked()) ? "checked" : "";
}

$.validator.setDefaults({ 
	success: function(label) {
		// set &nbsp; as text for IE
		label.html("&nbsp;").addClass("checked");
	}
});
$(document).ready(function() {
	$('#pwd').keyup(function(){
		$('#result').html(passwordStrength($('#pwd').val(),$('#username').val()));
		$('#strength').html(pwdStrenFeedback($('#pwd').val(),$('#username').val()));
	});
/*
jQuery.validator.addMethod("letterswithbasicpunc", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^[a-z-.,()'\"s]+$/i.test(value);

}, "Letters or punctuation only please");  

jQuery.validator.addMethod("alphanumeric", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^w+$/i.test(value);
}, "Letters, numbers, spaces or underscores only please");  

jQuery.validator.addMethod("lettersonly", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^[a-z]+$/i.test(value);
}, "Letters only please"); 

jQuery.validator.addMethod("nowhitespace", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^S+$/i.test(value);
}, "No white space please"); 

jQuery.validator.addMethod("anything", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^.+$/i.test(value);
}, "May contain any characters."); 

jQuery.validator.addMethod("integer", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^d+$/i.test(value);
}, "Numbers only please");

jQuery.validator.addMethod("phone", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^d{3}-d{3}-d{4}$/.test(value);
}, "Must be XXX-XXX-XXXX");
*/
	jQuery.validator.addMethod(
		"lettersonly",
		function(value, element) { return /^[a-z]+$/i.test(value); },
		"{/literal}{t}Letters only please{/t}{literal}");
	jQuery.validator.addMethod(
		"password", 
		function( value, element, param ) {return this.optional(element) || {/literal}{$conf->passwdRegex}{literal}.test(value);}, 
    	"{/literal}{$tr->t($conf->passwdRegexMsg)}{literal}");
    $("#userForm").validate();
    $("#submit").click(function(){localSetGroupChecked()});
});

/**
  Password should contain at least one integer.
  Password should contain at least one alphabet(either in downcase or upcase).
  Password can have special characters from 20 to 7E ascii values.
  Password should be minimum of 6 and maximum of 40 cahracters long.

  regexp: /^(?=.*\d)(?=.*([a-z]|[A-Z]))([\x20-\x7E]){6,40}$/
*/
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
			<td class="field">
				<input type="text" id="username" name="data[User][userid]" value="{$user.User.userid}" onkeyup="cutBlank(this);" 
					class="{literal}{required:true,lettersonly:true,minLength:6}{/literal}" title="{t 1='6'}User name is required (at least %1 chars, without white spaces and special chars){/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label"><label id="lrealname" for="realname">{t}Real name{/t}</label></td>
			<td class="field">
				<input type="text" id="realname" name="data[User][realname]" value="{$user.User.realname}"
					class="{literal}{required:true,minLength:6}{/literal}" title="{t 1='6'}Real name is required (at least %1 alphanumerical chars){/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label"><label id="lemail" for="email">{t}Email{/t}</label></td>
			<td class="field">
				<input type="text" id="email" name="data[User][email]" value="{$user.User.email}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		
		<tr>
		 	<td class="label">{if isset($user)}{t}New password{/t}{else}{t}Password{/t}{/if}</td>
			<td class="field">
				<input type="password" name="pwd" value="" id="pwd"
					class="{if isset($user)}{literal}{password:true}{/literal}{else}{literal}{required:true,password:true}{/literal}{/if}" 
			    	title="{$tr->t($conf->passwdRegexMsg)}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label">{t}Confirm password{/t}</td>
			<td class="field">
				<input type="password" name="data[User][passwd]" value=""
			class="{literal}{equalTo:'#pwd'}{/literal}" title="{t}Passwords should be equal{/t}"/>&nbsp;</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label">{t}Strength{/t}</td>
			<td class="field" colspan="2">
				<div id="strength">
					<table><tr><td><table><tr><td style="height:4px;width:160px;background-color:tan"></td></tr></table></td><td></td></tr></table>
				</div>
				<span id="result"></span>
			</td>
		</tr>
		<tr>
			<td class="label">{t}User blocked{/t}</td>
				{if isset($user)}
					{assign var='valid' value=$user.User.valid}
				{else}
					{assign var='valid' value='1' }
				{/if}
			<td class="field">
				<input type="radio" name="data[User][valid]"  id="userValid" 
					value="1" {if $valid}checked="checked"{/if} />
					<label for="userValid">{t}No{/t}</label>&nbsp;
				<input type="radio" name="data[User][valid]"  id="userNotValid" 
					value="0" {if !$valid}checked="checked"{/if} />
					<label for="userNotValid">{t}Yes{/t}</label>&nbsp;
			</td>
			<td class="status">&#160;</td>
		</tr>
		<tr>
			<td class="label"><label id="lgroups" for="groups">{t}Groups{/t}</label></td>
			<td class="field">
				<input type="hidden" name="groups" id="groups"
				class="{literal}{required:true}{/literal}" title="{t}Check at least one group{/t}"/></td>
			<td class="status">&#160;</td>
		</tr>
		{if !empty($formGroups)}
		<tr>
			<td class="label">&#160;</td>
			<td class="field">
				<table>
				{foreach from=$formGroups key=gname item=u}
				<tr>
					<td class="field">
						<input type="checkbox" id="group_{$gname}" name="data[$gname]" {if $u == 1}checked="checked"{/if}
					onclick="javascript:localUpdateGroupsChecked(this);"/></td>
					<td class="label"><label id="lgroup{$gname}" for="group{$gname}">{$gname}</label></td>
					<td class="status">&#160;</td>
				</tr>
				{/foreach}
				</table>
			</td>
			<td class="status">&#160;</td>
		</tr>
		{/if}
		{if !empty($userModules)}
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
				<input type="submit" id="submit" name="save" class="submit" value="{if isset($user)}{t}Modify{/t}{else}{t}Create{/t}{/if}" />
			</td> 
		</tr>
		</table>
		</fieldset>
		</form>
	</div>
</div>