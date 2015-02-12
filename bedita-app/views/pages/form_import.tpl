
<form action="{$html->url('/areas/import')}" method="post" name="importForm" id="importForm" enctype="multipart/form-data">
{$beForm->csrf()}

<input type="hidden" name="data[sectionId]" value="{$objectId|default:''}"/>

<fieldset id="import" style="padding:20px">

	<label>{t}import{/t} data on object {$objectId|default:''}</label>

	<hr />
		
		<label>file:</label> <input type="file" name="Filedata" />
		&nbsp;&nbsp;&nbsp;&nbsp; or &nbsp;&nbsp;&nbsp;
		<label>url:</label>  <input style="width:300px" type="text" name="data[url]" />
		
		
	<hr />
	
	<div>
		<b>select source type:</b> &nbsp;&nbsp;&nbsp;
		{foreach $conf->filters.import as $filter => $val}
			<input name="data[type]" type="radio" value="{$filter}" />{$filter} &nbsp;
		{/foreach}
		<input name="data[type]" type="radio" value="auto" checked="checked"/>autodetect
		&nbsp;&nbsp;&nbsp;
		<input type="checkbox" /> create media files if included
	</div>

	<hr />
	<input type="submit" value="{t}import{/t}" />

</fieldset>

</form>