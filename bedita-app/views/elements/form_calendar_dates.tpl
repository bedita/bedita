{$html->css('jquery.timepicker', null, ['inline' => false])}
{$html->script('libs/jquery/plugins/jquery.timepicker.min')}

<style scoped>
.newdaterow .dateadd,
.daterow .dateadd, .dummydaterow {
	display:none;
}
.newdaterow:last-child .dateadd,
.daterow:last-child .dateadd {
	display:inline-block;
}
.newdaterow:first-child .dateremove,
.daterow:first-child .dateremove {
	display:none;
}
.daterow label {
    vertical-align: middle;
}
.ui-timepicker-wrapper {
    width: 5em;
}
</style>

{assign var=numDates  value=count($object.DateItem)}
<script type="text/javascript">


</script>

{$relcount = $object.DateItem|@count|default:0}
<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}Event calendar{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="eventDates">

<div class="dummydaterow">
    <label>{t}start{/t}:</label>

    <input maxlength="10" type="text" id="" class="eventStart" name="" value=""/>
    <input maxlength="5" type="text"  id=""  class="timeinput timeStart" name="" value="" />

    <label>{t}end{/t}:</label>
    <input maxlength="10" type="text" id="" class="eventEnd" name="" value=""/>
    <input maxlength="5" type="text"  id=""  class="timeinput timeEnd" name="" value="" />

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
	<input maxlength="10" type="text" id="eventStart_{$key}" class="dateinput eventStart" name="data[DateItem][{$key}][start_date]" 
	value="{$dayStart}"/>
	<input maxlength="5" type="text"  id="timeStart_{$key}"  class="timeinput timeStart" name="data[DateItem][{$key}][timeStart]" 
	value="{if !empty($d.start_date)}{$d.start_date|date_format:'%H:%M'}{/if}" />
	
	<label>{t}end{/t}:</label>
	<input maxlength="10" type="text" id="eventEnd_{$key}" class="dateinput eventEnd" name="data[DateItem][{$key}][end_date]" 
	value="{$dayEnd}"/>
	<input maxlength="5" type="text"  id="timeEnd_{$key}"  class="timeinput timeEnd" name="data[DateItem][{$key}][timeEnd]" 
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
    <input maxlength="10" type="text" id="eventStart_0" class="dateinput eventStart" name="data[DateItem][0][start_date]" 
    value=""/>
    <input maxlength="5" type="text"  id="timeStart_0"  class="timeinput timeStart" name="data[DateItem][0][timeStart]" 
    value="" />
    
    <label>{t}end{/t}:</label>
    <input maxlength="10" type="text" id="eventEnd_0" class="dateinput eventEnd" name="data[DateItem][0][end_date]" 
    value=""/>
    <input maxlength="5" type="text"  id="timeEnd_0"  class="timeinput timeEnd" name="data[DateItem][0][timeEnd]" 
    value="" />

    <a href="javascript:void(0)" class="BEbutton dateremove">x</a>
</div>
{/if}

</fieldset>