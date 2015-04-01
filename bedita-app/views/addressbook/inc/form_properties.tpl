<div class="tab"><h2>{t}Properties{/t}</h2></div>

<fieldset id="properties">

<table class="bordered">

	<tr>
		<th>{t}Status{/t}:</th>
		<td colspan="4">
			{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
		</td>
	</tr>

	<tr>
		<th>{t}Username{/t}:</th>
		<td>
			<span id="user_name">
				{if !empty($object.User)}
					<a href="{$html->url('/users/viewUser/')}{$object.User.0.id}">{$object.User.0.userid|default:''|escape}</a>
				{else}
					{t}no user data{/t}
				{/if}
			</span>
			<input type="hidden" id="user_id" name="data[ObjectUser][card][0][user_id]" value="{$object.User.0.id|default:''}"/>
			<input type="hidden" name="data[ObjectUser][card][0][object_id]" value="{$object.id|default:''}"/>
			<input type="hidden" name="data[ObjectUser][card][0][switch]" value="card"/>
			&nbsp;&nbsp;&nbsp;
			{if empty($object.User)}
			<input id="promoteAsUser" type="button" class="modalbutton" name="edit" value="  {t}promote as user{/t}  "
				rel="{$html->url('/users/usersWithoutCard')}"
				title="USERS : select an item to associate" />
			{else}
			<input id="remove_user" type="button" value="  {t}remove from users{/t}  "/>
			{/if}
		</td>
	</tr>
	

	<tr>
		<th>{t}comments{/t}:</th>
		<td>
			<input type="radio" name="data[comments]" value="off"{if empty($object.comments) || $object.comments=='off'} checked{/if}/>{t}No{/t} 
			<input type="radio" name="data[comments]" value="on"{if !empty($object.comments) && $object.comments=='on'} checked{/if}/>{t}Yes{/t}
			<input type="radio" name="data[comments]" value="moderated"{if !empty($object.comments) && $object.comments=='moderated'} checked{/if}/>{t}Moderated{/t}
			&nbsp;&nbsp;
			{if isset($moduleList.comments) && $moduleList.comments.status == "on"}
				{if !empty($object.num_of_comment)}
					<a href="{$html->url('/')}comments/index/comment_object_id:{$object.id}"><img style="vertical-align:middle" src="{$html->webroot}img/iconComments.gif" alt="comments" /> ({$object.num_of_comment}) {t}view{/t}</a>
				{/if}
			{/if}
		</td>
	</tr>
	
	{if isset($comments)}
	<tr>
		<th>{t}Display details in frontend{/t}:</th>
		<td>
			<input type="radio" name="data[privacy_level]" value="0"{if empty($object.privacy_level) || $object.privacy_level=='0'} checked{/if}/>{t}No{/t} 
			<input type="radio" name="data[privacy_level]" value="1"{if !empty($object.privacy_level) && $object.privacy_level=='1'} checked{/if}/>{t}Yes{/t}
		</td>
	</tr>
	{/if}

</table>

</fieldset>
