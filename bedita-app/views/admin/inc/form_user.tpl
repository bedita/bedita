<script type="text/javascript">
{literal}

function localGroupChecked() {
	var formElem = document.getElementById("userform");
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
		"lettersnumbersonly",
		function(value, element) { return /^[a-z0-9]+$/i.test(value); },
		"{/literal}{t}Letters or numbers only please{/t}{literal}");
	jQuery.validator.addMethod(
		"password", 
		function( value, element, param ) {return this.optional(element) || {/literal}{$conf->passwdRegex}{literal}.test(value);}, 
    	"{/literal}{$tr->t($conf->passwdRegexMsg)}{literal}");
    $("#userform").validate();
    $("#submit").click(function(){localSetGroupChecked()});
});

/*
  Password should contain at least one integer.
  Password should contain at least one alphabet(either in downcase or upcase).
  Password can have special characters from 20 to 7E ascii values.
  Password should be minimum of 6 and maximum of 40 cahracters long.

  regexp: /^(?=.*\d)(?=.*([a-z]|[A-Z]))([\x20-\x7E]){6,40}$/
*/

{/literal}
</script>


<div class="tab"><h2>{t}User details{/t}</h2></div>

<fieldset id="customProperties">	

		<table class="bordered">
		<tr>
			<th>
				<label id="lusername" for="username">{t}User name{/t}</label>
				{if isset($user)}<input type="hidden" name="data[User][id]" value="{$user.User.id}"/>{/if}
			</th>
			<td>
				<input type="text" id="username" name="data[User][userid]" value="{$user.User.userid}" onkeyup="cutBlank(this);" 
					class="{literal}{required:true,lettersnumbersonly:true,minLength:6}{/literal}" title="{t 1='6'}User name is required (at least %1 chars, without white spaces and special chars){/t}"/>&nbsp;</td>
		</tr>
		<tr>
			<th><label id="lrealname" for="realname">{t}Real name{/t}</label></th>
			<td>
				<input type="text" id="realname" name="data[User][realname]" value="{$user.User.realname}"
					class="{literal}{required:true,minLength:6}{/literal}" title="{t 1='6'}Real name is required (at least %1 alphanumerical chars){/t}"/>&nbsp;</td>
		</tr>
		<tr>
			<th><label id="lemail" for="email">{t}Email{/t}</label></th>
			<td>
				<input type="text" id="email" name="data[User][email]" value="{$user.User.email}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>&nbsp;</td>

		</tr>
		

		<tr>
		 	<th>{if isset($user)}{t}New password{/t}{else}{t}Password{/t}{/if}</th>
			<td>
				<input type="password" name="pwd" value="" id="pwd"
					class="{if isset($user)}{literal}{password:true}{/literal}{else}{literal}{required:true,password:true}{/literal}{/if}" 
			    	title="{$tr->t($conf->passwdRegexMsg)}"/>&nbsp;</td>
			
		</tr>
		<tr>
			<th>{t}Confirm password{/t}</th>
			<td>
				<input type="password" name="data[User][passwd]" value=""
			class="{literal}{equalTo:'#pwd'}{/literal}" title="{t}Passwords should be equal{/t}"/>&nbsp;</td>
			
		</tr>
		
		<tr>
			<th>{t}Strength{/t}</th>
			<td>
				<div id="strength" style="height:15px;width:160px; background-color:tan">
				</div>
				<span id="result"></span>
			
			</td>
		</tr>
		
		<tr>
			<th>{t}User blocked{/t}</th>
				{if isset($user)}
					{assign var='valid' value=$user.User.valid}
				{else}
					{assign var='valid' value='1' }
				{/if}
			<td>
				<input type="radio" name="data[User][valid]"  id="userValid" 
					value="1" {if $valid}checked="checked"{/if} />
					<label for="userValid">{t}No{/t}</label>&nbsp;
				<input type="radio" name="data[User][valid]"  id="userNotValid" 
					value="0" {if !$valid}checked="checked"{/if} />
					<label for="userNotValid">{t}Yes{/t}</label>&nbsp;
			</td>
			
		</tr>
		<tr>
			<th>{t}last login{/t}</th>
			<td>{$user.User.last_login|date_format:"%d %B %Y   %H:%M"}</td>
		</tr>
		<tr>
			<th>{t}created{/t}</th>
			<td>{$user.User.created|date_format:"%d %B %Y   %H:%M"}</td>
		</tr
		<tr>
			<th>{t}modified{/t}</th>
			<td>{$user.User.modified|date_format:"%d %B %Y   %H:%M"}</td>
		</tr>

</table>






</fieldset>

	
<div class="tab"><h2>{t}Groups{/t}</h2></div>

<fieldset id="groups">	

<input type="hidden" name="groups" id="groups" 
class="{literal}{required:true}{/literal}" title="{t}Check at least one group{/t}"/>	
	
	<table class="bordered">	
		{if !empty($formGroups)}

				{foreach from=$formGroups key=gname item=u}
				<tr>
					<td>
						<input type="checkbox" id="group_{$gname}" name="data[groups][{$gname}]" 
						{if $u == 1}checked="checked"{/if}
						onclick="javascript:localUpdateGroupsChecked(this);"/>
					&nbsp;<label id="lgroup{$gname}" for="group{$gname}">{$gname}</label>
					</td>
					<th>{if in_array($gname,$conf->authorizedGroups)} <span class="evidence">*</span> {/if}</th>
				</tr>
				{/foreach}

		{/if}
				<tr>
					<td></td>
					<td><span class="evidence">*</span> {t}Group authorized to Backend{/t}</td>
					
				</tr>	
	</table>

</fieldset>


{if !empty($userModules)}
<div class="tab"><h2>{t}Module access{/t}</h2></div>

<fieldset id="userModules">	
		
	<table class="bordered">	

				{foreach from=$userModules item=mod}
				<tr>
					<th>
						<div style="padding-left:10px; border-left:20px solid {$mod.color}">
						{$mod.label}
						</div>
					</th>
					<td>
						{if ($mod.flag == $conf->BEDITA_PERMS_READ)}{t}Read only{/t}
						{elseif ($mod.flag & $conf->BEDITA_PERMS_MODIFY)}{t}Read and modify{/t}
						{/if}
					</td>
				</tr>
				{/foreach}

	</table>

</fieldset>
{/if}


