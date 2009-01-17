

<div class="tab"><h2>{t}Advanced Properties{/t}</h2></div>
<fieldset id="advancedproperties">

<table class="bordered">

	<tr>

		<th>{t}nickname{/t}:</th>
		<td colspan="5">
			<input type="text" id="nicknameBEObject" name="data[nickname]" value="{$object.nickname|escape:'html'|escape:'quotes'}"/>
		</td>

	</tr>

	{if (isset($doctype) && !empty($doctype))}
	<tr>
		<th>{t}Choose document type{/t}:</th>
		<td>
			{html_radios name="data[object_type_id]" options=$conf->docTypeOptions selected=$object.object_type_id|default:'22' separator="&nbsp;"}
		</td>
		<td>&nbsp;</td>
	</tr>
	{/if}

	{if ($object)}
		<tr>
			<th>{t}created by{/t}:</th>
			<td>{$object.UserCreated.userid}</td>
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
			<td>{$object.UserModified.userid}</td>
		</tr>
		
	{/if}

</table>

</fieldset>
