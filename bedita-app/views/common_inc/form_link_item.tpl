<div style="white-space:nowrap">
	
<input type="hidden" class="id" 	name="data[RelatedObject][link][{$objRelated.id}][id]" value="{$objRelated.id|default:''}" />
<input type="text" class="priority" name="data[RelatedObject][link][{$objRelated.id}][priority]" value="{$objRelated.priority|default:1}" size="3" maxlength="3"/>

{*
<label>{t}title{/t}:</label> <a href="{$objRelated.url}" target="{$objRelated.target}">{$objRelated.title|default:''}</a>
&nbsp;&nbsp;
<label>{t}url{/t}:</label> {$objRelated.url|default:''}
&nbsp;&nbsp;
<label>{t}target{/t}:</label> {$objRelated.target|default:''}
&nbsp;&nbsp;
<input type="button" value="{t}X{/t}" />
*}

<label>{t}title{/t}:</label> <input type="text" style="width:100px" name="data[RelatedObject][link][{$objRelated.id}][title]" value="{$objRelated.title|default:''}" />
<label>{t}url{/t}:</label> 	<input type="text" value="{$objRelated.url}" name="data[RelatedObject][link][{$objRelated.id}][url]" />
<label>{t}target{/t}:</label> 
<select style="width:70px" name="data[RelatedObject][link][{$objRelated.id}][target]"> 
	<option value="_self">_self</option>
	<option value="_blank">_blank</option>
</select>
<input type="button" title="remove" value="{t}X{/t}" />

</div>
