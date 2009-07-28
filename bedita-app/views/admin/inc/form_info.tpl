<div class="tab"><h2>{t}System info {/t}</h2></div>

<fieldset id="system_info">
<div>	
	
<table class="indexlist">
	<tr>
		<td></td>
		<td><label>{t}Software versions{/t}</label></td>
	</tr>
	<tr>
		<td><label>BEdita</label></td>
		<td>{$conf->Bedita.version}</td>
	</tr>
	<tr>
		<td><label>CakePHP</label></td>
		<td>{$conf->version()}</td>
	</tr>
	<tr>
		<td><label>PHP</label></td>
		<td>{$sys.phpVersion}</td>
	<tr>
		<td><label>PHP extensions</label></td>
		<td>{foreach from=$sys.phpExtensions item="ext"} {$ext}{/foreach}</td>
	</tr>
	<tr>
		<td><label>MySQL</label></td>
		<td>server {$sys.mysqlServer} - client {$sys.mysqlClient}</td>
	</tr>
	<tr>
		<td><label>Operating System</label></td>
		<td>{$sys.osVersion}</td>
	</tr>
	<tr>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td></td>
		<td><label>{t}URLs and paths{/t}</label></td>
	</tr>
	<tr>
		<td><label>Media files URL</label></td>
		<td><a href="{$conf->mediaUrl}" target="_blank">{$conf->mediaUrl}</a></td>
	</tr>
	<tr>
		<td><label>Media files root path</label></td>
		<td>{$conf->mediaRoot}</td>
	</tr>
	<tr>
		<td><label>BEdita URL</label></td>
		<td><a href="{$conf->beditaUrl}" target="_blank">{$conf->beditaUrl}</a></td>
	</tr>
	<tr>
		<td><label>BEdita app path</label></td>
		<td>{$sys.beditaPath}</td>
	</tr>
	<tr>
		<td><label>CakePHP path</label></td>
		<td>{$sys.cakePath}</td>
	</tr>
</table>

</div>
</fieldset>