{foreach from=$objsRelated item="objRelated" name="assocForeach"}
<tr>
	<td style="padding:0px; width:20px;">
		<input type="hidden" class="id" 	name="data[ObjectRelation][{$objRelated.id|default:""}][id]" value="{$objRelated.id|default:''}" />
		<input type="hidden" class="switch" name="data[ObjectRelation][{$objRelated.id|default:""}][switch]" value="{$rel|default:''}" />
		<input type="text" class="priority" 
				style="margin:0px; width:20px; text-align:right; background-color:transparent"
				name="data[ObjectRelation][{$objRelated.id|default:""}][priority]" 
				value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
	</td>
	
	<td style="width:10px;">
		<span class="listrecent {$objRelated.ObjectType.module|default:''}" style="margin:0px">&nbsp;</span>
	</td>
	
	<td>{$objRelated.title|default:''}</td>
	
	<td>{$objRelated.status|default:''}</td>
	
	<td>{$objRelated.lang|default:''}</td>
	
	<td>
		<input class="BEbutton link" rel="{$html->url('/')}{$objRelated.ObjectType.module}/view/{$objRelated.id}" name="details" type="button" value="details">
		<input class="BEbutton" name="remove" type="button" value="remove">
	</td>

</tr>
{/foreach}