<script type="text/javascript">
<!--
{literal}
function addUserToCard(id,username) {
	$("#user_id").attr("value",id);
	$("#user_name").text(username);
}
{/literal}
//-->
</script>

<div class="tab"><h2>{t}Properties{/t}</h2></div>

<fieldset id="properties">

<table class="bordered">

	<tr>
		<th>{t}Status{/t}:</th>
		<td colspan="4">
			{if ($object.status == 'fixed')}
			{t}This object is fixed - some data is readonly{/t}
			<input type="hidden" name="data[status]" value="fixed"/>
			{else}
			{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
			{/if}
		</td>
	</tr>

	<tr>
		<th>{t}Username{/t}:</th>
		<td>
			<span id="user_name">
				{if !empty($object.User)}
					<a href="{$html->url('/admin/viewUser/')}{$object.User.0.id}">{$object.User.0.userid|default:''}</a>
				{else}
					{t}no user data{/t}
				{/if}
			</span>
			<input type="hidden" id="user_id" name="data[User][0]" value="{$object.User.0.id|default:''}"/>
			&nbsp;&nbsp;&nbsp;
			{if empty($object.User)}
			<input type="button" class="modalbutton" name="edit" value="  {t}promote as user{/t}  "
				rel="{$html->url('/admin/showUsers')}"
				title="USERS : select an item to associate" />
			{else}
			<input type="button" value="  {t}remove from users{/t}  " />
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
