<script type="text/javascript">
var urlBan = "{$html->url('/comments/banIp')}";
var msgBan = "{t}Are you sure you want to ban this IP?{/t}";
var msgAccept = "{t}Are you sure you want to accept this IP?{/t}";
{literal}
$(document).ready(function(){
	$("#banIP").bind("click", function(){
		if(!confirm(msgBan)) return false ;
		$("#updateForm").attr("action", urlBan).submit();
		return false;
	});
	$("#sbanIP").bind("click", function(){
		if(!confirm(msgAccept)) return false ;
		$("#updateForm").attr("action", urlBan).submit();
		return false;
	});
});
{/literal}
</script>

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
		<textarea name="data[description]">{$object.description|default:''}</textarea>
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
		<td>{$object.created|date_format:$conf->dateTimePattern}</td>
		<th>{t}last modified on{/t}:</th>
		<td>{$object.modified|date_format:$conf->dateTimePattern}</td>
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

</form>