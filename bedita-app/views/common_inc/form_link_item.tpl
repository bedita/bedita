
<td style="padding:0px !important">
	<input type="hidden" class="id"  name="data[RelatedObject][link][{$objRelated.id}][id]" value="{$objRelated.id|default:''}" />
	<input type="hidden" name="data[RelatedObject][link][{$objRelated.id}][modified]" value="1" size="3" maxlength="3"/>
	<input type="text" class="priority" 
	style="width:20px; padding:0px; margin:0px !important;" 
	name="data[RelatedObject][link][{$objRelated.id}][priority]" value="{$objRelated.priority|default:1}" size="3" maxlength="3"/>
</td>
<td>
	<input type="text" style="width:140px" name="data[RelatedObject][link][{$objRelated.id}][title]" 
	value="{$objRelated.title|default:''}" />
</td>
<td>
	<input type="text" style="width:230px" value="{$objRelated.url}" name="data[RelatedObject][link][{$objRelated.id}][url]" />
</td>

{*
<label>{t}target{/t}:</label> 
<select style="width:70px" name="data[RelatedObject][link][{$objRelated.id}][target]"> 
	<option value="_self">_self</option>
	<option value="_blank">_blank</option>
</select>
*}
<td>
	<a style="font-weight:bold;" href="{$objRelated.url}" target="_blank"> GO </a>&nbsp;
	<input type="button" class="BEbutton link" onClick="window.open($(this).attr('href'));" href="{$html->url('/')}webmarks/view/{$objRelated.id}" name="details" value="››" />&nbsp;
	<input type="button" class="remove" title="remove" value="{t}X{/t}" />
</td>
