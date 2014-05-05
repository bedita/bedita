{$view->element('texteditor')}

<script type="text/javascript">
{literal}
$(document).ready(function(){
	var v = $().jquery;
	$("#jquery-version").text(v);
	if (typeof tinymce !== 'undefined') {
		v = tinymce.majorVersion + "." + tinymce.minorVersion;
		$("#tinymce-version").text(v);
	}
});
{/literal}
//-->
</script>	


<div class="tab"><h2>{t}System info{/t}</h2></div>

<fieldset id="system_info">
	
<table class="indexlist">
	<thead>
		<tr>
			<th colspan="3" style="text-transform:uppercase">{t}Software{/t}</th>
		</tr>
	</thead>
	<tr>
		<td><label>BEdita</label></td>
		<td>{$conf->Bedita.version}</td>
		<td></td>
	</tr>
	<tr>
		<td><label>CakePHP</label></td>
		<td>{$conf->version()}</td>
		<td></td>
	</tr>
	<tr>
		<td><label>PHP</label></td>
		<td>{$sys.phpVersion}</td>
	<tr>
		<td><label>PHP extensions</label></td>
		<td>{foreach from=$sys.phpExtensions item="ext"} {$ext}{/foreach}</td>
		<td></td>
	</tr>
	<tr>
		<td><label>{$sys.db}</label></td>
		<td>server: {$sys.dbServer|default:'?'} - client: {$sys.dbClient|default:'?'} - host: {$sys.dbHost} - db: {$sys.dbName}</td>
		<td></td>
	</tr>
	<tr>
		<td><label>Smarty</label></td>
		<td>{$smarty.version}</td>
		<td></td>
	<tr>
	<tr>
		<td><label>JQuery</label></td>
		<td><p id="jquery-version"></p></td>
		<td></td>
	</tr>
{if ($conf->richtexteditor|default:false)}
	<tr>
		<td><label>{$conf->richtexteditor.name|ucfirst}</label></td>
		<td><p id="richtexteditor-version"></p></td>
		<td></td>
	</tr>
{/if}	
	<tr>
		<td><label>Operating System</label></td>
		<td>{$sys.osVersion}</td>
		<td></td>
	</tr>
	<thead>
		<tr>
			<th colspan="3" style="text-transform:uppercase">{t}URLs and paths{/t}</th><td></td>
		</tr>
	</thead>
	<tr>
		<td><label>Media files URL</label></td>
		<td><a href="{$conf->mediaUrl}" target="_blank">{$conf->mediaUrl}</a></td><td></td>
	</tr>
	<tr>
		<td><label>Media files root path</label></td>
		<td>{$conf->mediaRoot}</td><td></td>
	</tr>
	<tr>
		<td><label>BEdita URL</label></td>
		<td><a href="{$conf->beditaUrl}" target="_blank">{$conf->beditaUrl}</a></td><td></td>
	</tr>
	<tr>
		<td><label>BEdita app path</label></td>
		<td>{$sys.beditaPath}</td><td></td>
	</tr>
	<tr>
		<td><label>CakePHP path</label></td>
		<td>{$sys.cakePath}</td><td></td>
	</tr>
</table>

</fieldset>