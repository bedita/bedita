{$html->css("jquery.timepicker.css")}
{$javascript->link("jquery/jquery.placement.below")}
{$javascript->link("jquery/jquery.timepicker-list")}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	$("#timeStart, #timeEnd").timePicker({startTime: "00:00", endTime: "23:30"});
});
</script>
{/literal}
<div class="tab"><h2>{t}Event calendar{/t}</h2></div>
<fieldset id="eventDates">

{assign var=idx value=0}

{if isset($object.DateItem.0)}    
{assign var=d value=$object.DateItem.0}
{/if}

	<label>{t}start{/t}:</label>
	<input size=10 type="text" class="dateinput" name="data[DateItem][{$idx}][start]" id="eventStart" value="{if !empty($d.start)}{$d.start|date_format:$conf->datePattern}{/if}"/>
	<input size=5 type="text" id="timeStart" name="data[DateItem][{$idx}][timeStart]" value="{if !empty($d.start)}{$d.start|date_format:'%H:%M'}{/if}" />
	
	<label>{t}end{/t}:</label>
	{strip}
	<input size=10 type="text" class="dateinput" name="data[DateItem][{$idx}][end]" id="eventEnd" value="{if !empty($d.end)}{$d.end|date_format:$conf->datePattern}{/if}"/>
	{/strip}
	<input size=5 type="text" id="timeEnd" name="data[DateItem][{$idx}][timeEnd]" value="{if !empty($d.end)}{$d.end|date_format:'%H:%M'}{/if}" />

</fieldset>
