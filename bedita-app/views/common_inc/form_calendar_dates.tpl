{$html->css("jquery.timepicker.css")}
{$javascript->link("jquery/jquery.placement.below")}
{$javascript->link("jquery/jquery.timepicker-list")}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	$('#eventStart').attachDatepicker();
	$('#eventEnd').attachDatepicker();
	$("#timeStart, #timeEnd").timePicker({startTime: "00:00", endTime: "23:30"});
});
</script>
{/literal}
<div class="tab"><h2>{t}Event calendar{/t}</h2></div>
<fieldset id="eventDates">

{assign var=idx value=0}


{assign var=d value=$object.EventDateItem.0}

	<label>{t}start{/t}</label>
	<input size=10 type="text" class="{literal}{{/literal}checkDate:'{$conf->dateFormatValidation}'{literal}}{/literal}" title="{t 1=$conf->dateFormatValidation}Please enter a valid date in the %1 format{/t}" 
		name="data[EventDateItem][{$idx}][start]" id="eventStart" value="{if !empty($d.start)}{$d.start|date_format:$conf->date_format}{/if}"/>
	<input size=5 type="text" id="timeStart" name="data[EventDateItem][{$idx}][timeStart]" class="{literal}{checkTime: true}{/literal}"  
		title="{t 1='HH:mm'}Please enter a valid time in the %1 format{/t}" value="{if !empty($d.start)}{$d.start|date_format:"%H:%M"}{/if}" size="10"/>
	
	<label>{t}end{/t}:</label>
	{strip}
	<input size=10 type="text" class="{literal}{{/literal}
									checkDate: '{$conf->dateFormatValidation}',
									dateGreaterThen: new Array('{$conf->dateFormatValidation}','eventStart')
							  {literal}}{/literal}" 
						title="{t 1=$conf->dateFormatValidation}Please enter a valid date in the %1 format and greater than the previous date{/t}" 
		name="data[EventDateItem][{$idx}][end]" id="eventEnd" value="{if !empty($d.end)}{$d.end|date_format:$conf->date_format}{/if}"/>
	{/strip}
	<input size=5 id="timeEnd" name="data[EventDateItem][{$idx}][timeEnd]" type="text"  class="{literal}{checkTime: true}{/literal}"
		title="{t 1='HH:mm'}Please enter a valid time in the %1 format{/t}" value="{if !empty($d.end)}{$d.end|date_format:"%H:%M"}{/if}" size="10"/>


</fieldset>
