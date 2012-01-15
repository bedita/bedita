
<form>
<fieldset id="export" style="padding:20px">

	<label>{t}import{/t} data on object {$objectId|default:''}</label>

	<hr />
		
		<label>file:</label> <input type="file" />
		&nbsp;&nbsp;&nbsp;&nbsp; or &nbsp;&nbsp;&nbsp;
		<label>url:</label>  <input style="width:300px" type="text" />
		
		
	<hr />
	
	<div>
		<b>select source type:</b> &nbsp;&nbsp;&nbsp;
		<input name="exportype" type="radio" />XML &nbsp;
		<input name="exportype" type="radio" />ePUB3 &nbsp;
		<input name="exportype" type="radio" />XJson &nbsp;
		<input name="exportype" type="radio" />autodetect
		&nbsp;&nbsp;&nbsp;
		<input type="checkbox" /> create media files if included
	</div>


	<hr />
	<input type="button" value="   {t}import{/t}   " />



</fieldset>

</form>