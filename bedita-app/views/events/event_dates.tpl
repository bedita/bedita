<h2 class="showHideBlockButton">{t}Event dates{/t}</h2>
<div class="blockForm" id="eventDates">
<fieldset>
{assign var=idx value=0}
{*
{foreach key="name" item="d" from=$object.EventDateItem}
	<span style="font-weight:bold;">{t}Event start{/t}:</span>
	<input type="text" class="{literal}{checkDate:true}{/literal}" title="{t}start has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" 
		name="data[EventDateItem][{$name}][start]" id="eventStart{$name}" value="{if !empty($d.start)}{$d.start|date_format:$conf->date_format}{/if}"/>
	<span style="font-weight:bold;">{t}end{/t}:</span>
	<input type="text" class="{literal}{checkDate:true}{/literal}" title="{t}end has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" 
		name="data[EventDateItem][{$name}][end]" id="eventEnd{$name}" value="{if !empty($d.end)}{$d.end|date_format:$conf->date_format}{/if}"/>
	<hr/>
{/foreach}
*}
	<span style="font-weight:bold;">{t}Add event date{/t}</span>
	<br/>
	<span style="font-weight:bold;">{t}start{/t}</span>
	<input type="text" class="{literal}{checkDate:true}{/literal}" title="{t}start has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" 
		name="data[EventDateItem][{$idx}][start]" id="eventStart" value=""/>
	<span style="font-weight:bold;">{t}end{/t}:</span>
	<input type="text" class="{literal}{checkDate:true}{/literal}" title="{t}end has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" 
		name="data[EventDateItem][{$idx}][end]" id="eventEnd" value=""/>
	<hr/>
</fieldset>
</div>