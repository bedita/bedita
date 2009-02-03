{foreach from=$objsRelated item="objRelated" name="assocForeach"}
<tr class="obj {$objRelated.status|default:''}">
	<td style="padding:0px; width:20px;">
		<input type="hidden" class="id" 	name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="text" class="priority" 
				style="margin:0px; width:20px; text-align:right; background-color:transparent"
				name="data[RelatedObject][{$rel}][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
	</td>
	
	<td style="width:10px;">
		<span title="{$objRelated.ObjectType.name}" class="listrecent {$objRelated.ObjectType.module|default:''}" style="margin:0px">&nbsp;</span>
	</td>
	
	<td>{$objRelated.title|default:'<i>[no title]</i>'}</td>
	
	<td>{$objRelated.status|default:''}</td>
	
	<td>{$objRelated.lang|default:''}</td>
	
	<td style="text-align:right; white-space:nowrap">
		<input class="BEbutton link" rel="{$html->url('/')}{$objRelated.ObjectType.module}/view/{$objRelated.id}" name="details" type="button" value="details">
		<input class="BEbutton" name="remove" type="button" value="x">
	</td>

</tr>
{/foreach}