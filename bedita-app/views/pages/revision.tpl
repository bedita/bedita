<div>
	
	<table class="bordered">
	<thead>
		<tr>
			<td colspan=5>
				Versione <b>2</b> di <b>5</b>, del {$smarty.now|date_format:'%d %B %Y %H:%M:%S'}, di <b>Nome Redattore</b>
				<input type="button" class="BEbutton" style="margin-left:10px" value="ripristina" /> 
				<input type="button" class="BEbutton" style="margin-left:10px" value="elimina" /> 
			</td>
		</tr>
	</thead>
	<tbody>
	{foreach from=$diff item=xdiff key=key}
		<tr>
			<td><b>{$key}</b></td>
			<td>{$revision[$key]|default:'<i>empty</i>'}</td>
			{*<td>{$diff}</td>*}
		</tr>
	{/foreach}
	</tbody>
	</table>



</div>