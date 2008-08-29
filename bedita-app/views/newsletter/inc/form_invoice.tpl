

<div class="tab"><h2>{t}Invoices{/t}</h2></div>

<fieldset id="invoices">			
			
<table class="bordered" style="width:100%">
		
	<tr>
		<th>Newsletter sent on:</th>
		<th>to recipient</th>
		<th>with template</th>
	</tr>
	<tr>
		<td class="info" style="text-decoration: blink;">currently in job</td>
		<td>gruppo uno</td>
		<td>pubblicazione uno</td>
	</tr>
	<tr>
		<td>01 sep 2008</td>
		<td>gruppo due</td>
		<td>pubblicazione uno</td>
	</tr>
	<tr>
		<td>21 aug 2008</td>
		<td>gruppo uno</td>
		<td>pubblicazione uno</td>
	</tr>
</table>

</fieldset>



<div class="tab"><h2>{t}Schedule new Invoice{/t}</h2></div>

<fieldset id="schedule">			
			
<table class="bordered" style="width:100%">

	<tr>
		<th>Start on:</th>
		<th>to recipient:</th>
		<th>with template:</th>
	</tr>
	<tr>
		<td>
			<input size="10" type="text" class="dateinput" name="data[start]" id="start" 
			value="{$object.start|default:$smarty.now|date_format:$conf->datePattern}" />
		</td>
		<td>
			<select>
				<option value="">--</option>
				<option>list of all recipents</option>
			</select>
		</td>
		<td>
			<select>
				<option value="">--</option>
				<option>list of all templates</option>
				<option>grouped by publishing</option>
			</select>
		</td>
	</tr>


</table>
	<div class="modalcommands newsletter">
		<input type="button" value="  test newsletter  "> 
		&nbsp;&nbsp;
		<input type="button" value="  SEND newsletter  ">
	</div>
	
	<em>{t} Newsletter must be saved before sending {/t}</em>
</fieldset>
