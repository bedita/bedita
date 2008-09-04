
<input type="hidden" class="id" 	name="data[RelatedObject][link][{$objRelated.id}][id]" value="{$objRelated.id|default:''}" />
<input type="text" class="priority" name="data[RelatedObject][link][{$objRelated.id}][priority]" value="{$objRelated.priority|default:1}" size="3" maxlength="3"/>

<label>{t}Title{/t}:</label> <a href="{$objRelated.url}" target="{$objRelated.target}">{$objRelated.title|default:''}</a>
&nbsp;&nbsp;
<label>{t}Url{/t}:</label> {$objRelated.url|default:''}
&nbsp;&nbsp;
<label>{t}Target{/t}:</label> {$objRelated.target|default:''}
&nbsp;&nbsp;
<input type="button" value="{t}X{/t}" />

