	
<form action="{$html->url('/pages/printme')}" target="print" method="post">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>

<div class="tab"><h2>{t}Print{/t}</h2></div>
<fieldset class="whitebox" id="print">

	<table border=0>
		<tr>
			<th><label>{t}Print layout{/t}:</label></th>
			<td>{$html->image('/img/print_tab_1fold.png')}</td>
			<td><input type="radio" name="data[printlayout]" value="1fold"> <label>A4</label></td>
			<td>{$html->image('/img/print_tab_2fold.png')}</td>
			<td><input type="radio" name="data[printlayout]" value="2fold"> <label>A4 2</label></td>
			<td>{$html->image('/img/print_tab_3fold.png')}</td>
			<td><input type="radio" name="data[printlayout]" value="3fold"> <label>A4 3</label></td>
		</tr>
	</table>
	<hr />
	<label>{t}print context{/t}:&nbsp;</label>
	<select name="printcontext">
		<option>BEdita standard report</option>
	{foreach from=$tree item=item}
		<option value="{$item.id}">{$item.title}</option>
	{/foreach}	
	</select>
	
	&nbsp;&nbsp;&nbsp;<input type="submit" value="{t}print{/t}">

</fieldset>

</form>