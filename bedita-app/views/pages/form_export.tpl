
<form action="{$html->url('/areas/export')}" method="post" name="exportForm" id="exportForm">
{$beForm->csrf()}

<input type="hidden" name="data[id]" value="{$objectId|default:''}"/>

<fieldset id="export" style="padding:20px">

	<label>{t}export{/t} object {$objectId|default:''}</label>
	
	&nbsp;&nbsp;&nbsp; <input type="checkbox" checked=1 /> recursive (include all children)
	&nbsp;&nbsp;&nbsp; <input type="checkbox" /> verbose (include all attributes)
	<hr />
	
	<div>
		{foreach $conf->filters.export as $filter => $val}
			<input name="data[type]" type="radio" value="{$filter}" />{$filter} &nbsp;
		{/foreach}
		<hr />
		<input type="checkbox" /> include media files
		&nbsp;&nbsp;&nbsp;
		<input type="checkbox" /> compress output
	</div>

	<hr />
		
		filename: <input type="text" name="data[filename]" value="bedita_export_{$objectId|default:''}">
	
	<hr />
	<input type="submit" value="{t}export{/t}" />

</fieldset>

</form>