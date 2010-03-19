

<div class="tab"><h2>{t}Advanced Properties{/t}</h2></div>
<fieldset id="advancedproperties">

<table class="bordered">

	<tr>
		<th>{t}nickname{/t}:</th>
		<td>
			<input type="text" id="nicknameBEObject" name="data[nickname]" style="width:280px" value="{$object.nickname|escape:'html'|escape:'quotes'}"/>
			&nbsp;&nbsp;
			<label>id:</label> {$object.id}
		</td>
	</tr>
	
	{if ($object)}
		
		{if !empty($object.Alias)}
		<tr>
			<th>{t}Alias{/t}:</th>
			<td>
				<ul>
				{foreach from=$object.Alias item=alias}
					{$alias.nickname_alias}
				{/foreach}
				</ul>
			</td>
		</tr>
		{/if}
		<tr>
			<th>{t}created by{/t}:</th>
			<td>{if !empty($object.UserCreated.userid)}{$object.UserCreated.userid}{else}-{/if}</td>
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
			<td>{if !empty($object.UserModified.userid)}{$object.UserModified.userid}{else}-{/if}</td>
		</tr>
		
	{/if}

	<tr>
		<th>{t}publisher{/t}:</th>
		<td><input type="text" name="data[publisher]" value="{$object.publisher|default:''}" /></td>
	</tr>
	<tr>
		<th>&copy; {t}rights{/t}:</th>
		<td><input type="text" name="data[rights]" value="{$object.rights|default:''}" /></td>
	</tr>
	<tr>
		<th>{t}license{/t}:</th>
		<td>
			<select style="width:300px;" name="data[license]">
				<option value="">--</option>
				<option value="1" {if $object.license=='1'}selected="selected"{/if}>Creative Commons Attribuzione 2.5 Italia</option>
				<option value="2" {if $object.license=='2'}selected="selected"{/if}>Creative Commons Attribuzione-Non commerciale 2.5 Italia</option>
				<option value="3" {if $object.license=='3'}selected="selected"{/if}>Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia</option>
				<option value="4" {if $object.license=='4'}selected="selected"{/if}>Creative Commons Attribuzione-Non opere derivate 2.5 Italia</option>
				<option value="5" {if $object.license=='5'}selected="selected"{/if}>Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia</option>
				<option value="6" {if $object.license=='6'}selected="selected"{/if}>Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia</option>
				<option value="7" {if $object.license=='7'}selected="selected"{/if}>Tutti i diritti riservati</option>
			</select>
		</td>
	</tr>
</table>

</fieldset>
