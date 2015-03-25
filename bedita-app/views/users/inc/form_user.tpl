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

        $('#userForm').on('submit', function (ev, proceed) {
            // #573 - Automatic Card creation.
            if (proceed || !$(this).find('#userCardToAssoc').length) {
                // Actual form submission.
                return true;
            }
            ev.preventDefault();

            var $that = $(this),
                $modal = $('#modalmain'),
                id = $('input[name="data[User][id]"]').val(),
                name = $('input[name="data[User][realname]"]').val(),
                email = $('input[name="data[User][email]"]').val();

            var reqData = {
                'id': id,
                'name': name,
                'email': email,
            };

            addCsrfToken(reqData, '#userForm');

            $that.BEmodal({
                title: '',
                destination: '{$html->url(['controller' => 'pages', 'action' => 'similarCards'])}',
                requestData: reqData
            });

            $modal
            .on('click', '#createCard', function (ev) {
                // Continues with user saving & new card creation.
                $that.trigger('submit', [/* proceed = */ true]);
            })
            .on('click', '#cardToUser', function (ev) {
                // Continues with user saving & existing card association.
                var val = $modal.find('input[name="cardtoassociate"]:checked').val();
                if (!val) {
                    window.alert('{t}Please choose an existing card, or create a new one.{/t}');
                    return false;
                }
                $that.find('#userCardToAssoc').val(val);
                $that.trigger('submit', [/* proceed = */ true]);
            });
        });
	});
</script>

<form action="{$html->url('/users/saveUser')}" method="post" name="userForm" id="userForm" class="cmxform">
{$beForm->csrf()}

<div class="tab"><h2>{t}User details{/t}</h2></div>

<fieldset id="details">

		<table class="bordered">
             {if $userDeleted|default:false}
            <tr>
                <td colspan="2">
                    This user was deleted, and modified on {$userdetail.modified|date_format:$conf->dateTimePattern}
                </td>
            </tr>
            {/if}
			<tr>
				<th><label>{t}Authentication service{/t}</label></th>
				<td>
					<select id="authselect" name="data[User][auth_type]">
						<option label="BEdita (default)" value="bedita"{if ($userdetail.auth_type|default:'bedita' == 'bedita') } selected{/if}>BEdita ( default )</option>
						{foreach from=$externalAuthServices item="service"}
						<option label="{$service.name}" value="{$service.name}"{if ($userdetail.auth_type|default:'bedita' == $service.name) } selected{/if}>{$service.name}</option>
						{/foreach}
					</select>
			</tr>

			<tr>
				<th>
					<label id="lusername" for="username">{t}User name{/t}</label>
					{if !empty($userdetail.id)}<input type="hidden" name="data[User][id]" value="{$userdetail.id}"/>{/if}
				</th>
				<td>
					<input type="text" id="username" name="data[User][userid]" value="{$userdetail.userid|escape}" onkeyup="cutBlank(this);" 
						class="{ required:true,lettersnumbersonly:true,minLength:6}" 
						title="{t 1='6'}User name is required (at least %1 chars, without white spaces and special chars){/t}"/>

					<input type="hidden" name="data[User][auth_params][userid]" value="{if !isset($userdetail.auth_params.userid)}{$userdetail.auth_params|default:''}{/if}" />
					&nbsp;</td>
			</tr>

			<tbody class="authTypeForm" id="authTypeBedita" {if ($userdetail.auth_type|mb_lower|default:'bedita' != 'bedita')}style="display:none"{/if}>
				<tr>
				 	<th>{t}New password{/t}</th>
					<td>
						<input type="{if !empty($userdetail.id)}password{else}text{/if}" name="pwd" value="{if empty($userdetail.id)}{$genpassword|default:''}{/if}" id="pwd"
							class="{if isset($userdetail)}{ password:true}{else}{ required:true,password:true}{/if}" 
					    	title="{$tr->t($conf->passwdRegexMsg)|default:''}" autocomplete="off"/>&nbsp;</td>
				</tr>
				<tr>
					<th>{t}Confirm password{/t}</th>
					<td>
						<input type="{if !empty($userdetail.id)}password{else}text{/if}" name="data[User][passwd]" id="pwdagain" value=""
					class="{ equalTo:'#pwd'}" title="{t}Passwords should be equal{/t}" autocomplete="off"/>&nbsp;</td>
				</tr>
			</tbody>

			{foreach from=$externalAuthServices item="service"}


			<tbody class="authTypeForm" id="authType{$service.name}" {if ($userdetail.auth_type|default:'bedita' != $service.name)}style="display:none"{/if}>
				<tr>
					<th>{t}Password{/t}</th>
					<td>
						{t}authentication provided by{/t} <span class="auth_name evidence">{$service.name}</span>
					</td>	
				</tr>
				<tr>
					<th><span class="auth_name">{$service.name}</span> userid</th>
					<td>
						<input type="text" placeholder="use user {$service.relatedBy|default:'e-mail'}" name="data[Service][{$service.name}][userid]" value="{if !empty($userdetail.auth_params)}{$userdetail.auth_params|default:''}{/if}" />
					&nbsp;
					{foreach from=$userdetail.auth_params item="val" key="k"}
						{if $k != 'userid'}
						 <input type="hidden" name="data[User][auth_params][{$k}]" value="{$val}"/>
						{/if}
					{/foreach}
					</td>
				</tr>
			</tbody>

			{/foreach}

			<tr>
				<th><label id="lrealname" for="realname">{t}Real name{/t}</label></th>
				<td>
					<input type="text" id="realname" name="data[User][realname]" value="{$userdetail.realname|escape}"
						class="{ required:true,minLength:6}" title="{t 1='6'}Real name is required (at least %1 alphanumerical chars){/t}"/>&nbsp;</td>
			</tr>
			<tr>
				<th><label id="lemail" for="email">{t}Email{/t}</label></th>
				<td>
					<input type="text" id="email" name="data[User][email]" value="{$userdetail.email|default:''}"
				class="{ email:true}" title="{t}Use a valid email{/t}"/>&nbsp;</td>

			</tr>
			

			<tr>
				<th>{t}User blocked{/t}</th>
					{if isset($userdetail)}
						{assign var='valid' value=$userdetail.valid}
					{else}
						{assign var='valid' value='1'}
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
				<th>{t}card details{/t}</th>
				<td>
                    {if !empty($objectUser.card)}
                        <a href="{$html->url('/view/')}{$objectUser.card.0.id}">{t}YES{/t}</a>
                    {else}
                        <input type="hidden" name="data[User][_cardToAssoc]" id="userCardToAssoc" value="" />
                        {t}NO{/t}
                    {/if}
				</td>
			</tr>

</table>

</fieldset>
	
	<div class="tab"><h2>{t}Groups{/t}</h2></div>

	<fieldset id="groups">	

	<input type="hidden" name="groups" id="groups" 
	class="{ required:true}" title="{t}Check at least one group{/t}"/>	
		
		<table class="bordered">	
			{if !empty($formGroups)}
					{foreach from=$formGroups key=gname item=u}
					<tr>
						<td>
							{$gname = $gname|escape}
							<input type="checkbox" id="group_{$gname}" name="data[groups][{$gname}]" {if $u == 1}checked="checked"{/if}	onclick="javascript:localUpdateGroupsChecked(this);" />
							&nbsp;
							<label id="lgroup{$gname}" for="group{$gname}">{$gname}</label>
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
						class="{$mod.url}">
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

    {$relcount = $html->params.paging.Objects.count|default:0}
    <div class="tab"><h2 {if empty($objectsCreated)}class="empty"{/if}>{t}Objects created{/t} &nbsp; 
        {if $relcount > 0}
            <span class="relnumb">
                {$relcount}</span>{/if}
    </h2></div>
    
    <fieldset id="objects_created"> 

        <table class="indexlist bordered">    
        {if !empty($objectsCreated)}
        {foreach from=$objectsCreated item=item key=key}
            {$object_type=$conf->objectTypes[$item.Objects.object_type_id].module_name}
            <tr class="obj {$item.Objects.status|default:''}" data-beid="{$item.Objects.id}" data-benick="{$item.Objects.nickname}">
                <td style="width:20px">
                    <span class="listrecent {$object_type}">&nbsp;&nbsp;</span>
                </td>
                <td><a target="obj" href="{$html->url('/')}view/{$item.Objects.id}">{$item.Objects.title|default:$item.Objects.nickname}</a></td>
                <td>
                    {$item.Objects.id}
                </td>
            </tr>
        {/foreach}
        {else}
            <tr><td><i>{t}No objects{/t}</i></td></tr>
        {/if}
        </table>

        <div class="toolbar sans" style="text-align:right; padding:10px">
            {$pager = $html->params.paging.Objects|default:''}
            {t}page{/t} {$pager.page} &nbsp;{t}of{/t}&nbsp; {$pager.pageCount} 
        </div>

    </fieldset>
      
    {*dump var=$html->params.paging.Objects|default:''*}

	{include file="inc/form_user_custom_properties.tpl"}

</form>
