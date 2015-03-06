

<div class="tab"><h2>{t}Advanced Properties{/t}</h2></div>
<fieldset id="advancedproperties">

<table class="bordered">

	<tr>

		<th>{t}nickname{/t}:</th>
		<td colspan="5">
			<input type="text" style="width:100%" name="data[nickname]" value="{$object.nickname|escape:'html'|escape:'quotes'}"/>
		</td>

	</tr>

	{if ($object)}
		<tr>
			<th>{t}created by{/t}:</th>
			<td>{$object.UserCreated.userid|escape}</td>
		</tr>	
		<tr>
			<th>{t}created on{/t}:</th>
			<td>{$object.created|date_format:$conf->dateTimePattern}</td>
		</tr>	 
		<tr>
			<th>{t}last modified on{/t}:</th>
			<td>{$object.modified|date_format:$conf->dateTimePattern}</td>
		</tr>
		<tr>
			<th>{t}last modified by{/t}:</th>
			<td>{$object.UserModified.userid|escape}</td>
		</tr>
		
	{/if}

</table>

</fieldset>
