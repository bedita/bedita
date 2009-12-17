{$html->css("jquery.timepicker.css")}
{$javascript->link("jquery/jquery.placement.below")}
{$javascript->link("jquery/jquery.timepicker-list")}

{literal}
<script type="text/javascript">
$(document).ready(function(){
	$(".timeStart, .timeEnd").timePicker({startTime: "00:00", endTime: "23:30"});
		
		$(".dateadd").click(function (){
			var row = $(this).parent(".daterow");
			$(row).clone(true).insertAfter(row).addClass("newdaterow");
			$(".newdaterow INPUT").val("");
		});
		$(".dateremove").click(function (){
			var row = $(this).parent(".daterow");
			$(row).remove();
		});
});

</script>
{/literal}
<div class="tab"><h2>{t}Event calendar{/t}</h2></div>
<fieldset id="eventDates">

{foreach name=dd from=$object.DateItem item=d}
<div class="daterow">
	{assign var=idx value=$smarty.foreach.dd.index}

	<label>{t}start{/t}:</label>
	<input size=10 type="text" id="eventStart{$idx}" class="dateinput" name="data[DateItem][{$idx}][start]" value="{if !empty($d.start)}{$d.start|date_format:$conf->datePattern}{/if}"/>
	<input size=5 type="text"  id="timeStart{$idx}"  class="timeStart" name="data[DateItem][{$idx}][timeStart]" value="{if !empty($d.start)}{$d.start|date_format:'%H:%M'}{/if}" />
	
	<label>{t}end{/t}:</label>
	{strip}
	<input size=10 type="text" id="eventEnd{$idx}" class="dateinput" name="data[DateItem][{$idx}][end]" value="{if !empty($d.end)}{$d.end|date_format:$conf->datePattern}{/if}"/>
	{/strip}
	<input size=5 type="text"  id="timeEnd{$idx}"  class="timeEnd" name="data[DateItem][{$idx}][timeEnd]" value="{if !empty($d.end)}{$d.end|date_format:'%H:%M'}{/if}" />
	
	{bedev}
	<a href="javascript:void(0)" class="BEbutton dateremove">X</a>
		{if $smarty.foreach.dd.last} <a href="javascript:void(0)" class="BEbutton dateadd">+</a> {/if}
	<hr />
	{/bedev}
</div>
{foreachelse}

<div class="newdaterow">
	{assign var=idx value=$smarty.foreach.dd.index+1}
	<label>{t}start{/t}:</label>
	<input size=10 type="text" id="eventStart{$idx}" class="dateinput" name="data[DateItem][{$idx}][start]" value=""/>
	<input size=5 type="text"  id="timeStart{$idx}"  class="timeStart" name="data[DateItem][{$idx}][timeStart]" value="" />
	
	<label>{t}end{/t}:</label>
	{strip}
	<input size=10 type="text" id="eventEnd{$idx}" class="dateinput" name="data[DateItem][{$idx}][end]" value=""/>
	{/strip}
	<input size=5 type="text"  id="timeEnd{$idx}"  class="timeEnd" name="data[DateItem][{$idx}][timeEnd]" value="" />
</div>
{/foreach}


</fieldset>