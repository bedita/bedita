	
	
<div class="tab"><h2>{t}Questions{/t}</h2></div>

<fieldset id="questions">
	<table class="indexlist">
		<tr>
			<th></th>
			<th>title</th>
			<th>type</th>
			<th>status</th>
			<th></th>
		</tr>
	{section name="o" loop=8}
		<tr>
			<td>{$smarty.section.o.iteration}</td>
			<td>Quanti conchiglie per 55 lische?</td>
			<td>scelta multipla</td>
			<td style="text-align:center">on</td>
			<td>
				<input type="button" title="{t}details{/t}" value="»" />
				<input type="button" title="{t}remove{/t}" value="x" />
			</td>
		</tr>
	{/section}
	<tr>
		<th colspan="5" style="padding:10px; text-align:center">
			<input class="modalbutton" type="button" value="{t}insert more questions{/t}" />
		</th>
	</tr>
	</table>
	
</fieldset>