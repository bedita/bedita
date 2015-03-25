{$view->element('texteditor')}

<script type="text/javascript">
{literal}
$(document).ready(function(){
	var v = $().jquery;
	$('#jquery-version').text(v);
	if (typeof tinymce !== 'undefined') {
		v = tinymce.majorVersion + "." + tinymce.minorVersion;
		$('#richtexteditor-version').text(v);
	}
	if (typeof CKEDITOR !== 'undefined') {
		$('#richtexteditor-version').text(CKEDITOR.version);
	}
});
{/literal}
//-->
</script>

<div class="tab"><h2>{t}System info{/t}</h2></div>

<fieldset id="system_info">

<table class="indexlist">
{if !empty($warnings)}
    <thead class="warn">
        <tr><th colspan="2" style="text-transform:uppercase">{t}Warnings{/t}</th></tr>
    </thead>
    <tbody class="warn">
        {if in_array('phpVersion', $warnings)}<tr><td colspan="2">{t 1='PHP' 2=$conf->requirements.phpVersion}Currently installed %1 version is too old, and is no longer supported by BEdita. Please consider upgrading to %2 at least.{/t}</td></tr>{/if}

{foreach $conf->requirements.phpExtensions as $ext}
        {if in_array($ext, $warnings)}<tr><td colspan="2">{t 1=$ext}PHP extension %1 doesn't seem to be loaded, but it's required for BEdita full functionality. Please, ensure it is correctly installed.{/t}</td></tr>{/if}
{/foreach}

        {if in_array('db', $warnings)}<tr><td colspan="2">{t 1=$sys.db 2=$conf->requirements.dbVersion|array_keys|join:', '}Your database engine (%1) is not supported by BEdita. Supported SQL servers: %2.{/t}</td></tr>{/if}
        {if in_array('MySQL', $warnings)}<tr><td colspan="2">{t 1='MySQL server' 2=$conf->requirements.dbVersion.MySQL}Currently installed %1 version is too old, and is no longer supported by BEdita. Please consider upgrading to %2 at least.{/t}</td></tr>{/if}
        {if in_array('PostgreSQL', $warnings)}<tr><td colspan="2">{t 1='PostgreSQL server' 2=$conf->requirements.dbVersion.PostgreSQL}Currently installed %1 version is too old, and is no longer supported by BEdita. Please consider upgrading to %2 at least.{/t}</td></tr>{/if}
    </tbody>
{/if}

	<thead>
		<tr><th colspan="2" style="text-transform:uppercase">{t}Software{/t}</th></tr>
	</thead>
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
		<td>{$sys.phpExtensions|join:' '}</td>
	</tr>
	<tr>
		<td><label>{$sys.db}</label></td>
		<td>server: {$sys.dbServer|default:'?'} - client: {$sys.dbClient|default:'?'} - host: {$sys.dbHost} - db: {$sys.dbName}</td>
	</tr>
	<tr>
		<td><label>Smarty</label></td>
		<td>{$smarty.version}</td>
	<tr>
	<tr>
		<td><label>JQuery</label></td>
		<td id="jquery-version"></td>
	</tr>
{if ($conf->richtexteditor|default:false)}
	<tr>
		<td><label>{$conf->richtexteditor.name|ucfirst}</label></td>
		<td id="richtexteditor-version"></td>
	</tr>
{/if}
	<tr>
		<td><label>Operating System</label></td>
		<td>{$sys.osVersion}</td>
	</tr>

	<thead>
		<tr><th colspan="2" style="text-transform:uppercase">{t}URLs and paths{/t}</th></tr>
	</thead>
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

</fieldset>