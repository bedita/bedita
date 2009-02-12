{foreach from=$objsRelated item="c"}
	<tr class="obj {$c.status}">
		<td>
			<input type="hidden" class="id" 	name="reorder[{$c.id}][id]" value="{$c.id}" />
			<input type="text" class="priority"	name="reorder[{$c.id}][priority]" value="{$c.priority|default:""}" size="3" maxlength="3"/>
		</td>
		<td>
			<span title="{$c.module}" class="listrecent {$c.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
		</td>
		<td style="width:100%">
			{$c.title|default:'<i>[no title]</i>'|truncate:"64":"…":true}
		</td>
		<td>
			{$c.lang}
		</td>
		<td class="commands" style="white-space:nowrap">
			<input type="button" class="BEbutton link" onClick="window.open($(this).attr('href'));" href="{$html->url('/')}{$c.module}/view/{$c.id}" name="details" value="››" />
			<input type="button" name="remove" value="x" />
		</td>
	</tr>
{/foreach}





