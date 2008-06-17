<form action="{$html->url('/comments/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
<input type="hidden" name="data[title]" value="{$object.title|default:''}" />
<input type="hidden" name="data[nickname]" value="{$object.nickname|default:''}" />

<table class="bordered">
		
	<tr>

		<th>{t}Status{/t}:</th>
		<td colspan="3">
			{html_radios name="data[status]" options=$conf->statusOptions 
			selected=$object.status|default:$conf->status separator="&nbsp;"}
		</td>

	</tr>
	
	<tr>
	
		<th>{t}Text{/t}:</th>
		<td colspan="3">
		<textarea name="data[abstract]">{$object.abstract|default:''}</textarea>
		</td>
	
	</tr>
	
	<tr>
	
		<th>{t}Author{/t}:</th>
		<td colspan="3">
		<input type="text" name="data[author]" value="{$object.author|default:''}"/>
		</td>
	
	</tr>
	
	<tr>
		<th>{t}created on{/t}:</th>
		<td>{$object.created|date_format:"%d-%m-%Y  | %H:%M:%S"}</td>
		<th>{t}last modified on{/t}:</th>
		<td>{$object.modified|date_format:"%d-%m-%Y | %H:%M:%S"}</td>
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
		<td colspan="3">
		{$object.ip_created}
		</td>
	
	</tr>
	{/if}
	
</table>

</form>