
<form>
<fieldset id="export" style="padding:20px">

	<label>{t}export{/t} object {$objectId|default:''}</label>
	
	&nbsp;&nbsp;&nbsp; <input type="checkbox" checked=1 /> recursive (include all children)
	&nbsp;&nbsp;&nbsp; <input type="checkbox" /> verbose (include all attributes)
	<hr />
	
	<div>
		<input name="exportype" type="radio" />HTML5 &nbsp;
		<input name="exportype" type="radio" />ePUB3 &nbsp;
		<input name="exportype" type="radio" />XML &nbsp;
		<input name="exportype" type="radio" />PDF &nbsp;
		<input name="exportype" type="radio" />RTF &nbsp;
		<hr />
		<input type="checkbox" /> include media files
		&nbsp;&nbsp;&nbsp;
		<input type="checkbox" /> compress output
	</div>

	<hr />
		
		filename: <input type="text" value="bedita_export_{$objectId|default:''}">
	
	<hr />
	<input type="button" value="{t}export{/t}" />



</fieldset>

</form>