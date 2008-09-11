

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
			<i>{t}no user data{/t} </i> 
			&nbsp;&nbsp;&nbsp;<input type="button" class="beditabutton" name="edit" value="  {t}promote as user{/t}  " />
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
