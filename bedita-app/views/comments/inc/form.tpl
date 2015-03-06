<form action="{$html->url('/comments/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
{$beForm->csrf()}
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
<input type="hidden" name="data[title]" value="{$object.title|default:''}" />
<input type="hidden" name="data[nickname]" value="{$object.nickname|default:''}" />
<input type="hidden" name="data[object_id]" value="{$object.ReferenceObject.id}" />

<div class="tab"><h2>comment#{$object.id} on {$object.ReferenceObject.title|escape}</h2></div>

<fieldset id="details">
<table class="bordered">
		
	<tr>

		<th>{t}Status{/t}:</th>
		<td colspan="3">
			{html_radios name="data[status]" options=$conf->statusOptions 
			selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
		</td>

	</tr>
	
	<tr>
	
		<th>{t}Text{/t}:</th>
		<td colspan="3">
		<textarea style="height:120px; width:380px;" name="data[description]">{$object.description|default:''|escape}</textarea>
		</td>
	
	</tr>
	
	<tr>
	
		<th>{t}Author{/t}:</th>
		<td colspan="3">
		<input type="text" name="data[author]" value="{$object.author|default:''|escape}"/>
		</td>
	
	</tr>

	<tr>
	
		<th>{t}Commented item{/t}:</th>
		<td colspan="3">
			{assign var="o" value=$object.ReferenceObject}
			<a href="{$html->url('/')}view/{$o.id}"><span title="{$conf->objectTypes[$o.object_type_id].name}" 
				class="listrecent {$conf->objectTypes[$o.object_type_id].module_name}">&nbsp;</span>
				{$o.title|default:'<i>[no title]</i>'|escape}
			</a>
		</td>
	
	</tr>

	
	<tr>
		<th>{t}created on{/t}:</th>
		<td>{$object.created|date_format:$conf->dateTimePattern}</td>
		<th>{if isset($object.UserCreated.id)}{t}from{/t}:{/if}</th>
		<td>{if isset($object.UserCreated.id)}{$object.UserCreated.userid|escape}{/if}</td>
	</tr>

	<tr>
		<th>{t}modified on{/t}:</th>
		<td>{$object.modified|date_format:$conf->dateTimePattern}</td>
		<th>{if isset($object.UserModified.id)}{t}from{/t}:{/if}</th>
		<td>{if isset($object.UserModified.id)}{$object.UserModified.userid|escape}{/if}</td>
	</tr>
	
	<tr>
	
		<th>{t}email{/t}:</th>
		<td colspan="3">
		<input type="text" name="data[email]" value="{$object.email|default:''}"/>
		</td>
	
	</tr>
	
	<tr>
	
		<th>{t}web site{/t}:</th>
		<td colspan="3">
		<input type="text" name="data[url]" value="{$object.url|default:''}"/>
		</td>
	
	</tr>

	{if !empty($object.ip_created)}
	<tr>
	
		<th>{t}IP{/t}:</th>
		<td>
		{$object.ip_created}
		<input type="hidden" name="data[ip_created]" value="{$object.ip_created}"/>
		<input type="hidden" name="data[ip_to_ban]" value="{$object.ip_created}"/>
		</td>
		<td colspan="2">
			{if !isset($banned)}
				<input type="hidden" name="data[ban_status]" value="ban"/>
				<input type="button" class="delete" id="banIP" title="banIP" value="{t}ban this IP!{/t}"/>
			{else}
				<input type="hidden" name="data[ban_status]" value="accept"/>
				IP banned - <input type="button" class="delete" id="sbanIP" title="banIP" value="{t}accept this IP!{/t}"/>
			{/if}
		</td>		
	
	</tr>
	{/if}
	
</table>
</fieldset>

{$view->element('form_geotag')}

{assign_associative var="params" object_type_id=$conf->objectTypes.document.id}
{$view->element('form_assoc_objects', $params)}
	
</form>


	{$view->element('form_print')}