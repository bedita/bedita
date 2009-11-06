{literal}
	<script type="text/javascript">
		$(document).ready(function() {
			$("input[type=password]").val("");
			
			$("#authselect").change(function() {
				var au = $(this).val();
				if (au != "") {
					$("#authType").show();
					$("#defaultAuth").hide();
					$("#authType .auth_name").text(au);
				} else {
					$("#authType").hide();
					$("#defaultAuth").show();
				};
			});
			
		});
	</script>
{/literal}

<form action="{$html->url('/admin/saveUser')}" method="post" name="userForm" id="userForm" class="cmxform">
			
<div class="tab"><h2>{t}User details{/t}</h2></div>

<fieldset id="details">	

		<table class="bordered">
			
		<tr>
			<th><label>{t}Authentication service{/t}</label></th>
			<td>
				<select id="authselect" name="data[User][auth_type]">
					<option label="BEdita" value="" selected>BEdita ( default )  </option>
					{html_options values=$conf->extAuthTypes output=$conf->extAuthTypes selected=$userdetail.auth_type}
				</select>
		</tr>
	
		<tr>
			<th>
				<label id="lusername" for="username">{t}User name{/t}</label>
				{if !empty($userdetail.id)}<input type="hidden" name="data[User][id]" value="{$userdetail.id}"/>{/if}
			</th>
			<td>
				<input type="text" id="username" name="data[User][userid]" value="{$userdetail.userid}" onkeyup="cutBlank(this);" 
					class="{literal}{required:true,lettersnumbersonly:true,minLength:6}{/literal}" title="{t 1='6'}User name is required (at least %1 chars, without white spaces and special chars){/t}"/>&nbsp;</td>
		</tr>
		
		<tbody id="defaultAuth" {if ($userdetail.auth_type|default:'')}style="display:none"{/if}>
			<tr>
			 	<th>{t}New password{/t}</th>
				<td>
					<input type="password" name="pwd" value="" id="pwd"
						class="{if isset($userdetail)}{literal}{password:true}{/literal}{else}{literal}{required:true,password:true}{/literal}{/if}" 
				    	title="{$tr->t($conf->passwdRegexMsg)}"/>&nbsp;</td>
			</tr>
			<tr>
				<th>{t}Confirm password{/t}</th>
				<td>
					<input type="password" name="data[User][passwd]" value=""
				class="{literal}{equalTo:'#pwd'}{/literal}" title="{t}Passwords should be equal{/t}"/>&nbsp;</td>
			</tr>
		</tbody>


		<tbody id="authType" {if (!$userdetail.auth_type|default:'')}style="display:none"{/if}>
			<tr>
				<th>{t}Password{/t}</th>
				<td>
					{t}authentication provided by{/t} <span class="auth_name evidence">{$userdetail.auth_type|default:'external service'}</span>
				</td>	
			</tr>
			<tr>
				<th><span class="auth_name">{$userdetail.auth_type|default:'external service'}</span> userid</th>
				<td><input type="text" name="data[User][auth_params][userid]" value="{$userdetail.auth_params.userid|default:''}" />&nbsp;
				{foreach from=$userdetail.auth_params item="val" key="k"}
					{if $k != 'userid'}
					 <input type="hidden" name="data[User][auth_params][{$k}]" value="{$val}"/>
					{/if}
				{/foreach}
				</td>
			</tr>
		</tbody>
		
				
		
		
		
		
		<tr>
			<th><label id="lrealname" for="realname">{t}Real name{/t}</label></th>
			<td>
				<input type="text" id="realname" name="data[User][realname]" value="{$userdetail.realname}"
					class="{literal}{required:true,minLength:6}{/literal}" title="{t 1='6'}Real name is required (at least %1 alphanumerical chars){/t}"/>&nbsp;</td>
		</tr>
		<tr>
			<th><label id="lemail" for="email">{t}Email{/t}</label></th>
			<td>
				<input type="text" id="email" name="data[User][email]" value="{$userdetail.email|default:' '}"
			class="{literal}{email:true}{/literal}" title="{t}Use a valid email{/t}"/>&nbsp;</td>

		</tr>
		

		<tr>
			<th>{t}User blocked{/t}</th>
				{if isset($userdetail)}
					{assign var='valid' value=$userdetail.valid}
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
			<td>{$userdetail.last_login|date_format:$conf->dateTimePattern}</td>
		</tr>
		<tr>
			<th>{t}created{/t}</th>
			<td>{$userdetail.created|date_format:$conf->dateTimePattern}</td>
		</tr>
		<tr>
			<th>{t}modified{/t}</th>
			<td>{$userdetail.modified|date_format:$conf->dateTimePattern}</td>
		</tr>
		
		<tr>
			<th>{t}addressbook details{/t}</th>
			<td>
				{if !empty($objectUser.card)}
					<a href="{$html->url('/')}addressbook/view/{$objectUser.card.0.id}">YES</a>
				{else}
					NO
				{/if}	
			</td>
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
					<th>{if in_array($gname,$authGroups)} <span class="evidence">*</span> {/if}</th>
				</tr>
				{/foreach}

		{/if}
				<tr>
					<td></td>
					<td><span class="evidence">*</span> {t}Group authorized to Backend{/t}</td>
					
				</tr>	
	</table>

</fieldset>


{if !empty($userdetailModules)}
<div class="tab"><h2>{t}Module access{/t}</h2></div>

<fieldset id="userModules">	
		
	<table class="bordered">	

				{foreach from=$userdetailModules item=mod}
				<tr>
					<th style="white-space:nowrap">
					<div style="float:left; vertical-align:middle; margin:0px 10px 0px -10px; width:20px;" 
					class="{$mod.path}">
					&nbsp;</div>
					
					{$mod.label}
						
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

</form>
