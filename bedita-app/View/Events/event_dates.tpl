{$this->Html->css("jquery.timepicker.css")}
{$this->Html->script("jquery/jquery.placement.below")}
{$this->Html->script("jquery/jquery.timepicker-list")}


<script type="text/javascript">
$(document).ready(function(){
	$('#eventStart').attachDatepicker();
	$('#eventEnd').attachDatepicker();
	$("#timeStart, #timeEnd").timePicker({startTime: "00:00", endTime: "23:30"});
});
</script>

<h2 class="showHideBlockButton">{t}Event calendar{/t}</h2>
<div class="blockForm" id="eventDates">
<fieldset>
{assign var=idx value=0}
{*
{foreach key="name" item="d" from=$object.DateItem}
	<span style="font-weight:bold;">{t}Event start{/t}:</span>
	<input type="text" class="{ checkDate:true}" title="{t}start has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" 
		name="data[DateItem][{$name}][start_date]" id="eventStart{$name}" value="{if !empty($d.start_date)}{$d.start_date|date_format:$conf->datePattern}{/if}"/>
	<span style="font-weight:bold;">{t}end{/t}:</span>
	<input type="text" class="{ checkDate:true}" title="{t}end has to be a valid date in the following format:{/t} {$conf->dateFormatValidation}" 
		name="data[DateItem][{$name}][end_date]" id="eventEnd{$name}" value="{if !empty($d.end_date)}{$d.end_date|date_format:$conf->datePattern}{/if}"/>
	<hr/>
{/foreach}
*}
{assign var=d value=$object.DateItem.0}
	<span style="font-weight:bold;">{t}Add event date{/t}</span>
	<br/>
	<span style="font-weight:bold;">{t}start{/t}</span>
	<input type="text" class="{ checkDate:'{$conf->dateFormatValidation}'}" title="{t 1=$conf->dateFormatValidation}Please enter a valid date in the %1 format{/t}" 
		name="data[DateItem][{$idx}][start_date]" id="eventStart" value="{if !empty($d.start_date)}{$d.start_date|date_format:$conf->datePattern}{/if}"/>
	<input type="text" id="timeStart" name="data[DateItem][{$idx}][timeStart]" class="{checkTime: true}"  
		title="{t 1='HH:mm'}Please enter a valid time in the %1 format{/t}" value="{if !empty($d.start_date)}{$d.start_date|date_format:"%H:%M"}{/if}" size="10"/>
	<span style="font-weight:bold;">{t}end{/t}:</span>
	{strip}
	<input type="text" class="{
									checkDate: '{$conf->dateFormatValidation}',
									dateGreaterThen: new Array('{$conf->dateFormatValidation}','eventStart')
							  }" 
						title="{t 1=$conf->dateFormatValidation}Please enter a valid date in the %1 format and greater than the previous date{/t}" 
		name="data[DateItem][{$idx}][end_date]" id="eventEnd" value="{if !empty($d.end_date)}{$d.end_date|date_format:$conf->datePattern}{/if}"/>
	{/strip}
	<input id="timeEnd" name="data[DateItem][{$idx}][timeEnd]" type="text"  class="{ checkTime: true}"
		title="{t 1='HH:mm'}Please enter a valid time in the %1 format{/t}" value="{if !empty($d.end_date)}{$d.end_date|date_format:"%H:%M"}{/if}" size="10"/>
	<hr/>
</fieldset>
</div>