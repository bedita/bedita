<div class="tab"><h2>List details</h2></div>
<fieldset id="details">
	<table class="bordered">
		<tr>
			<td>
				<label for="groupname">{t}list name{/t}:</label>
			</td>
			<td>
				<input type="hidden" name="data[MailGroup][id]" value="{$item.id|default:''}" />
				<input type="text" style="width:360px;" id="groupname" name="data[MailGroup][group_name]" value="{$item.group_name|escape|default:''}" />
			</td>
		</tr>
		<tr>
			<td>
				<label for="publishing">{t}publication{/t}:</label>
			</td>
			<td>{assign var='mailgroup_area_id' value=$item.area_id|default:''}
				<select style="width:220px" name="data[MailGroup][area_id]">
					{foreach from=$areasList key="area_id" item="public_name"}
					<option value="{$area_id}"{if $area_id == $mailgroup_area_id} selected{/if}>{$public_name|escape}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>{assign var='mailgroup_visible' value=$item.visible|default:'1'}
				<input type="radio" name="data[MailGroup][visible]" value="1" {if $mailgroup_visible=='1'}checked="true"{/if}/>
				<label for="visible">{t}public list	{/t}</label> (people can subscribe)
			&nbsp;
				<input type="radio" name="data[MailGroup][visible]" value="0" {if $mailgroup_visible=='0'}checked="true"{/if}/>
				<label for="visible">{t}private list {/t}</label> (back-end insertions only)
			</td>
		</tr>
	</table>
</fieldset>