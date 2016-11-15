

<div class="tab"><h2>{t}Advanced Properties{/t}</h2></div>
<fieldset id="advancedproperties" class="multimediaiteminside">

<table class="bordered">
	{if !empty($object.id)}
	<tr>
		<th>{t}id{/t}:</th>
		<td>{$object.id}</td>
	</tr>
	{/if}
	{if !empty($object.nickname)}
	<tr>
		<th>{t}unique name{/t}:</th>
		<td>
			{*<input type="text" id="nicknameBEObject" name="data[nickname]" style="width:280px" value="{$object.nickname|escape:'html'|escape:'quotes'}"/>*}
			{$object.nickname}
		</td>
	</tr>
	{/if}
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
			<td>{$object.UserCreated.realname|default:''|escape} [ {$object.UserCreated.userid|default:''|escape} ]</td>
		</tr>	
		<tr>
			<th>{t}created on{/t}:</th>
			<td>{$object.created|date_format:$conf->dateTimePattern}</td>
		</tr>	 
		<tr>
			<th style="white-space:nowrap">{t}last modified on{/t}:</th>
			<td>{$object.modified|date_format:$conf->dateTimePattern}</td>
		</tr>
		<tr>
			<th style="white-space:nowrap">{t}last modified by{/t}:</th>
			<td>{$object.UserModified.realname|default:''|escape} [ {$object.UserModified.userid|default:''|escape} ]</td>
		</tr>
		<tr>
			<th>{t}object type{/t}:</th>
			<td>
				{$object.ObjectType.name}
			</td>
		</tr>
		
	{/if}
	{if empty($excludedParts) || !in_array('publisher',$excludedParts)}
	<tr>
		<th>{t}publisher{/t}:</th>
		<td>
			<input type="text" name="data[publisher]" value="{$object.publisher|default:''|escape}" />
			{if !empty($conf->editorialContents)}
				({t}An empty publisher means "not editorial content"{/t})
			{/if}	
		</td>
	</tr>
	{/if}
	{if empty($excludedParts) || !in_array('rights',$excludedParts)}
	<tr>
		<th>&copy; {t}rights{/t}:</th>
		<td><input type="text" name="data[rights]" value="{$object.rights|default:''|escape}" /></td>
	</tr>
	{/if}
	{if empty($excludedParts) || !in_array('license',$excludedParts)}
	<tr>
		<th>{t}license{/t}:</th>
		<td>
			<select style="width:300px;" name="data[license]">
				<option value="">--</option>
				{foreach from=$conf->defaultLicenses item=lic key=code}
					<option value="{$code}" {if $object.license==$code}selected="selected"{/if}>{$lic.title}</option>
				{/foreach}
				{foreach from=$conf->cfgLicenses item=lic key=code}
					<option value="{$code}" {if $object.license==$code}selected="selected"{/if}>{$lic.title}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	{/if}
</table>

</fieldset>
