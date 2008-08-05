

<div class="tab"><h2>{t}advanced properties{/t}</h2></div>
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


           <tr>
				<td><label>{t}publisher{/t}</label></td>
				<td><input type="text" name="publisher" value="" /></td>
           </tr>
           <tr>
           		<td><strong>&copy; {t}rights{/t}</strong></td>
				<td><input type="text" name="rights" value="" /></td>
           </tr>
			<tr>
				<td> <label>{t}license{/t}</label></td>                
				<td>
					<select style="width:300px;" name="license">
						<option value="">--</option>
						<option  value="1">Creative Commons Attribuzione 2.5 Italia</option>
						<option  value="2">Creative Commons Attribuzione-Non commerciale 2.5 Italia</option>
						<option  value="3">Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="4">Creative Commons Attribuzione-Non opere derivate 2.5 Italia</option>
						<option  value="5">Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="6">Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia</option>
						<option  value="7">Tutti i diritti riservati</option>
					</select>
                </td>
           </tr>
</table>


    

