{$html->css("jquery.timepicker.css")}
{$html->script("jquery/jquery.placement.below")}
{$html->script("jquery/jquery.timepicker-list")}

<style scoped>
.daterow .dateadd, .dummydaterow {
	display:none;
}
.daterow:last-child .dateadd {
	display:inline;
}
.daterow:first-child .dateremove {
	display:none;
}
</style>

{assign var=numDates  value=count($object.DateItem)}
<script type="text/javascript">
$(document).ready(function(){
	
	$(".timeStart, .timeEnd", ".daterow").timePicker({ startTime: "00:00", endTime: "23:30"});

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
        evtStart.attr("id","eventStart_" + numDates);
        evtStart.attr("name","data[DateItem][" + numDates + "][start_date]");
        var timeStart = newRow.find(".timeStart")
        timeStart.attr("id","timeStart_" + numDates);
        timeStart.attr("name","data[DateItem][" + numDates + "][timeStart]");
        var evtEnd = newRow.find(".eventEnd")
        evtEnd.addClass("dateinput");
        evtEnd.attr("id","eventEnd_" + numDates);
        evtEnd.attr("name","data[DateItem][" + numDates + "][end_date]");
        var timeEnd = newRow.find(".timeEnd")
        timeEnd.attr("id","timeEnd_" + numDates);
        timeEnd.attr("name","data[DateItem][" + numDates + "][timeEnd]");
        numDates++;
        newRow.find(".timeStart, .timeEnd").timePicker({ startTime: "00:00", endTime: "23:30"});
        newRow.find("input.dateinput").datepicker();
	});

});

</script>

<div class="tab"><h2>{t}Event calendar{/t}</h2></div>
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
<div class="daterow">
	<label>{t}start{/t}:</label>
	<input size=10 type="text" id="eventStart_{$key}" class="dateinput eventStart" name="data[DateItem][{$key}][start_date]" 
	value="{if !empty($d.start_date)}{$d.start_date|date_format:$conf->datePattern}{/if}"/>
	<input size=5 type="text"  id="timeStart_{$key}"  class="timeStart" name="data[DateItem][{$key}][timeStart]" 
	value="{if !empty($d.start_date)}{$d.start_date|date_format:'%H:%M'}{/if}" />
	
	<label>{t}end{/t}:</label>
	<input size=10 type="text" id="eventEnd_{$key}" class="dateinput eventEnd" name="data[DateItem][{$key}][end_date]" 
	value="{if !empty($d.end_date)}{$d.end_date|date_format:$conf->datePattern}{/if}"/>
	<input size=5 type="text"  id="timeEnd_{$key}"  class="timeEnd" name="data[DateItem][{$key}][timeEnd]" 
	value="{if !empty($d.end_date)}{$d.end_date|date_format:'%H:%M'}{/if}" />

	<a href="javascript:void(0)" class="BEbutton dateremove">X</a>
	<a href="javascript:void(0)" class="BEbutton dateadd">+</a>
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

    <a href="javascript:void(0)" class="BEbutton dateremove">X</a>
    <a href="javascript:void(0)" class="BEbutton dateadd">+</a>
</div>
{/if}

</fieldset>