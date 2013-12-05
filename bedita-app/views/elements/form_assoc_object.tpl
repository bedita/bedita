{foreach from=$objsRelated item="objRelated" name="assocForeach"}
<tr class="obj {$objRelated.status|default:''}">
	<td style="padding:0px; width:20px;">
	<input type="hidden" class="rel_nickname" value="{$objRelated.nickname}">
		<input type="hidden" class="id" name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority" 
				style="margin:0px; width:20px; text-align:right; background-color:transparent"
				name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
	</td>

	<td style="width:10px;">
		<span title="{$objRelated.ObjectType.name}" class="listrecent {$objRelated.ObjectType.module_name|default:''}" style="margin:0px">&nbsp;</span>
	</td>
	
	<td>
		{$objRelated.title|default:'<i>[no title]</i>'|truncate:60:'~':true}
	</td>

{if $rel == "download"}

	<td>{$objRelated.mime_type|default:''|truncate:60:'~':true}</td>
	
	<td style="text-align:right">{$objRelated.file_size|default:0|filesize}</td>

{/if}

	<td>{$objRelated.status|default:''}</td>
	
	<td>{$objRelated.lang|default:''}</td>
	
	<td style="text-align:right; white-space:nowrap">
		<a class="BEbutton golink" 
		title="nickname:{$objRelated.nickname|default:''} id:{$objRelated.id}, {$objRelated.mime_type|default:''}" 
		href="{$html->url('/')}{$objRelated.ObjectType.module_name}/view/{$objRelated.id}">{t}details{/t}</a>
		<input class="BEbutton" name="remove" type="button" value="x">
	</td>

</tr>
{/foreach}