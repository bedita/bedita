
<div class="tab"><h2>{t}Invoice{/t}</h2></div>

<fieldset id="invoice">			


<fieldset id="schedule">			
			
<table class="bordered" style="width:100%">

	<tr>
		<th>{t}start{/t}:</th>
		<th>{t}to recipients{/t}:</th>
		<th>{t}status{/t}:</th>
	</tr>
	<tr>
		<td>
	<input size=10 type="text" class="dateinput" name="data[DateItem][{$idx}][start]" id="eventStart" value="{if !empty($d.start)}{$d.start|date_format:$conf->datePattern}{/if}"/>
	<input size=5 type="text" id="timeStart" name="data[DateItem][{$idx}][timeStart]" value="" />

		</td>
		<td>
			<input type="checkbox"> lista dei gruppi
			<br />
			<input type="checkbox"> cioè delle categorie 
			<br />
			<input type="checkbox"> addressbook+newsletter
		</td>
		<td class="info" style="text-decoration: blink;">currently in job</td>
	</tr>


</table>
	<div class="modalcommands newsletter">
		<input type="button" value="  test newsletter  "> 
		&nbsp;&nbsp;
		<input type="button" value="  SEND newsletter  ">
	</div>
	
	<em>{t} Newsletter must be saved before sending {/t}</em>
</fieldset>
