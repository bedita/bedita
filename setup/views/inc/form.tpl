{if $page==2}
<form>
<fieldset>
	<table>
	<tr><td><label>Database name</label>:</td><td><input type="text" value="bedita"/></td></tr>
	<tr><td><label>User</label>:</td><td><input type="text" value="bedita"/></td></tr>
	<tr><td><label>Password</label>:</td><td><input type="text"/></td></tr>
	</table>
	<a href="javascript:;" onclick="javascript:document.getElementById('dbAdvancedSettings').style.display='';">Advanced settings</a>
	<table id="dbAdvancedSettings" style="display:none">
	<tr><td><label>Host</label>:</td><td><input type="text" value="localhost"/></td></tr>
	<tr><td><label>Driver</label>:</td><td><select><option>mysql</option></select></td></tr>
	<tr><td><label>Persistent</label>:</td><td><input type="text"/></td></tr>
	<tr><td><label>Schema</label>:</td><td><input type="text"/></td></tr>
	<tr><td><label>Prefix</label>:</td><td><input type="text"/></td></tr>
	<tr><td><label>Encoding</label>:</td><td><input type="text"/></td></tr>
	</table>
	<table>
	<tr>
		<td colspan="2">
			<input type="button" value="Cancel" />
			<input type="button" value="< Back" />
			<input type="button" value="Next >" />
			<input type="button" value="Finish" disabled="disabled" />
		</td>
	</tr>
	</table>
</fieldset>
</form>
{/if}