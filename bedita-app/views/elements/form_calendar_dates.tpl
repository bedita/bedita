{$html->css('jquery.timepicker', null, ['inline' => false])}
{$html->script('libs/jquery/plugins/jquery.timepicker.min')}

<style scoped>
.daterow .dateadd, .dummydaterow {
	display:none;
}
.daterow:last-child .dateadd {
	display:inline-block;
}
.daterow:first-child .dateremove {
	display:none;
}
.daterow label {
    vertical-align: middle;
}
</style>

{assign var=numDates  value=count($object.DateItem)}
<script type="text/javascript">
$(document).ready(function(){
	
    var timePickerOptions = {
        minTime: '00:00',
        maxTime: '23:30',
        timeFormat: 'G:i'
    }

	$('.timeStart, .timeEnd', '.daterow').timepicker(timePickerOptions);

    var numDates = {$numDates};

    $(".dateremove").click(function (){
		var row = $(this).parent(".daterow");
		if ($(".daterow").size() > 1) {
	        $(row).remove();			
		} else {
			row.find(".eventStart").val("");
            row.find(".timeStart").val("");			
            row.find(".eventEnd").val("");
            row.find(".timeEnd").val("");
		}
	});

	$(".dateadd").click(function (){
        var row = $(this).parent(".daterow");
        if (row.length == 0) {
        	row = $(this).parent(".newdaterow");
        }
        var newRow = $(".dummydaterow").clone(true);
        newRow.insertAfter(row);
        newRow.removeClass("dummydaterow").addClass("newdaterow");
        var evtStart = newRow.find(".eventStart")
        evtStart.addClass("dateinput");
        // for newly created objs numDates may be 0
        if (numDates == 0) {
        	numDates = 1;
        }
        evtStart.prop("id", "eventStart_" + numDates);
        evtStart.prop("name", "data[DateItem][" + numDates + "][start_date]");
        var timeStart = newRow.find(".timeStart")
        timeStart.prop("id", "timeStart_" + numDates);
        timeStart.prop("name", "data[DateItem][" + numDates + "][timeStart]");
        var evtEnd = newRow.find(".eventEnd")
        evtEnd.addClass("dateinput");
        evtEnd.prop("id", "eventEnd_" + numDates);
        evtEnd.prop("name", "data[DateItem][" + numDates + "][end_date]");
        var timeEnd = newRow.find(".timeEnd")
        timeEnd.prop("id", "timeEnd_" + numDates);
        timeEnd.prop("name", "data[DateItem][" + numDates + "][timeEnd]");
        numDates++;
        newRow.find(".timeStart, .timeEnd").timepicker(timePickerOptions);
        newRow.find("input.dateinput").datepicker();
	});

    $(".radioAlways").click(function (){
    	var always = $(this).val();
        if (always == "true") {
        	$(this).closest(".daterow").find("input[type=checkbox]").prop('disabled', true);
            $(this).closest(".daterow").find("input[type=checkbox]").prop('checked', false);
            $(this).closest(".daterow").find(".date_exceptions").hide();

        } else {
            $(this).closest(".daterow").find("input[type=checkbox]").prop('disabled', false);
            $(this).closest(".daterow").find(".date_exceptions").show();
        }
    });

	
});

</script>

{$relcount = $object.DateItem|@count|default:0}
<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}Event calendar{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="eventDates">

<div class="dummydaterow">
    <label>{t}start{/t}:</label>
    <input size=10 type="text" id="" class="eventStart" name="" value=""/>
    <input size=5 type="text"  id=""  class="timeStart" name="" value="" />
    
    <label>{t}end{/t}:</label>
    <input size=10 type="text" id="" class="eventEnd" name="" value=""/>
    <input size=5 type="text"  id=""  class="timeEnd" name="" value="" />

    <a href="javascript:void(0)" class="BEbutton dateremove">X</a>
    <a href="javascript:void(0)" class="BEbutton dateadd">+</a>
    
</div>


{if !empty($object.DateItem)}
{foreach name=dd from=$object.DateItem|@sortby:'start_date' item=d key=key}

    {$dayStart = ""}
    {$dayEnd = ""}
    {if !empty($d.start_date)}
        {$dayStart = $d.start_date|date_format:$conf->datePattern}
    {/if}
    {if !empty($d.start_date)}
        {$dayEnd = $d.end_date|date_format:$conf->datePattern}
    {/if}

<div class="daterow">
	<label>{if $d@total > 1}<span class="rownumber">{$d@iteration}</span>{/if}{t}start{/t}:</label>
	<input size=10 type="text" id="eventStart_{$key}" class="dateinput eventStart" name="data[DateItem][{$key}][start_date]" 
	value="{$dayStart}"/>
	<input size=5 type="text"  id="timeStart_{$key}"  class="timeStart" name="data[DateItem][{$key}][timeStart]" 
	value="{if !empty($d.start_date)}{$d.start_date|date_format:'%H:%M'}{/if}" />
	
	<label>{t}end{/t}:</label>
	<input size=10 type="text" id="eventEnd_{$key}" class="dateinput eventEnd" name="data[DateItem][{$key}][end_date]" 
	value="{$dayEnd}"/>
	<input size=5 type="text"  id="timeEnd_{$key}"  class="timeEnd" name="data[DateItem][{$key}][timeEnd]" 
	value="{if !empty($d.end_date)}{$d.end_date|date_format:'%H:%M'}{/if}" />

    {if !empty($d.start_date) && !empty($d.end_date) && $dayStart != $dayEnd}
    <div>
        <label>{t}every day{/t}:</label>
        <input type="radio" id="everyY_{$key}" name="data[DateItem][{$key}][always]" 
           class="radioAlways" value="true" 
           {if empty($d.days)}checked="checked"{/if} />{t}yes{/t}
        <input type="radio" id="everyN_{$key}" name="data[DateItem][{$key}][always]" 
           class="radioAlways" value="false" 
           {if !empty($d.days)}checked="checked"{/if}/>{t}no{/t}
    </div>
    {/if}

	<a href="javascript:void(0)" class="BEbutton dateremove">x</a>
    <a href="javascript:void(0)" class="BEbutton dateadd">+</a>


    {if !empty($d.start_date) && !empty($d.end_date) && $dayStart != $dayEnd}
    <div class="date_exceptions"  {if empty($d.days)}style="display:none;"{/if}>
        <label>{t}days{/t}:</label>
        {if empty($d.days)}
            {$recordDays = []}
        {else}
            {$recordDays = $d.days}
        {/if}
        {$days=["sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"]}
        {foreach $days as $i => $day}
            <input type="checkbox" id="dayChk_{$key}_{$i}" 
                name="data[DateItem][{$key}][days][]" value="{$i}" 
                {if in_array($i, $recordDays)}checked="checked"{/if}
                {if empty($recordDays)}disabled="disabled"{/if}/> {t}{$day}{/t} &nbsp;&nbsp;
        {/foreach}
    </div>
	{/if}    

</div>

{/foreach}
{else}

<div class="daterow">
    <label>{t}start{/t}:</label>
    <input size=10 type="text" id="eventStart_0" class="dateinput eventStart" name="data[DateItem][0][start_date]" 
    value=""/>
    <input size=5 type="text"  id="timeStart_0"  class="timeStart" name="data[DateItem][0][timeStart]" 
    value="" />
    
    <label>{t}end{/t}:</label>
    <input size=10 type="text" id="eventEnd_0" class="dateinput eventEnd" name="data[DateItem][0][end_date]" 
    value=""/>
    <input size=5 type="text"  id="timeEnd_0"  class="timeEnd" name="data[DateItem][0][timeEnd]" 
    value="" />

    <a href="javascript:void(0)" class="BEbutton dateremove">x</a>
</div>
{/if}

</fieldset>