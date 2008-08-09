<tr>
	<input type="hidden" name="index" value="{$objIndex|default:""}" />
	<input type="hidden" class="id" 	name="data[ObjectRelation][{$objIndex|default:""}][id]" value="{$objRelated.id|default:''}" />
	<input type="hidden" class="switch" name="data[ObjectRelation][{$objIndex|default:""}][switch]" value="{$rel|default:''}" />
	
	<td>
		<input type="text" class="priority" name="data[ObjectRelation][{$objIndex|default:""}][priority]" value="{$objRelated.priority|default:''}" size="3" maxlength="3"/>
		<span class="listrecent {$objRelated.module|default:''}" style="margin-left:0px">&nbsp;&nbsp;</span>
	</td>
	
	<td>
		{$objRelated.title|default:''}
	</td>
	
	<td>
		{$objRelated.status|default:''}
	</td>
	
	<td>
		{$objRelated.lang|default:''}
	</td>
	
	<td>
		<a href="{$html->url('/')}{$objRelated.module|default:''}/view/{$objRelated.id}">dettagli</a>
	</td>
	<td>
		elimina 
	</td>

</tr>
