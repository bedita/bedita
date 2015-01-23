<td style="padding:0px !important">
{*literal}
<script type="text/javascript">
$(document).ready(function(){
	$(".linkcontent").change(function() {
		$(this).parents("TR").find(".linkmod").val(1).css("background-color","gold");
	})
});
</script>
{/literal*}
	<input type="hidden" class="id"  name="data[RelatedObject][link][{$objRelated.id}][id]" value="{$objRelated.id|default:''}" />
	<input type="text" name="data[RelatedObject][link][{$objRelated.id}][priority]" value="{$objRelated.priority|default:1}" size="3" maxlength="3" class="priority" style="width:20px; padding:0px; margin:0px !important;" />
</td>
<td>
	<input type="text" class="linkcontent" style="width:140px" name="data[RelatedObject][link][{$objRelated.id}][title]" value="{$objRelated.title|escape|default:''}" />
</td>
<td>
	<input type="text" class="linkcontent" style="width:230px" value="{$objRelated.url}" name="data[RelatedObject][link][{$objRelated.id}][url]" />
</td>

{*
<label>{t}target{/t}:</label> 
<select style="width:70px" name="data[RelatedObject][link][{$objRelated.id}][target]"> 
	<option value="_self">_self</option>
	<option value="_blank">_blank</option>
</select>
*}
<td style="white-space:nowrap">
	<input type="button" class="BEbutton golink" onClick="window.open($(this).attr('href'));" href="{$html->url('/')}webmarks/view/{$objRelated.id}" name="details" value="››" />&nbsp;
	<input type="button" class="remove" title="remove" value="{t}X{/t}" />
	&nbsp; <a href="{$objRelated.url}" title="{t}open in new window{/t}" target="_blank">open</a>
	
		
	<input type="hidden" class="linkmod" name="data[RelatedObject][link][{$objRelated.id}][modified]" value="1" />
	
</td>
