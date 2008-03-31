<h2 class="showHideBlockButton">{t}File{/t}</h2>
<div class="blockForm" style="display:block" id="multimediaitem">
<fieldset>
{if (isset($object))}
	<table class="tableForm" border="0">
	<tr>
		<td rowspan="3">
{if ($object.ObjectType.name == "image")}
{assign var="thumbWidth" 		value = 200}
{assign var="thumbHeight" 		value = 200}
{assign var="filePath"			value = $object.path}
{assign var="fileName"			value = $object.filename|default:$obj.name}
{assign var="fileTitle"			value = $object.title}
{assign var="mediaPath"         value = $conf->mediaRoot}
{assign var="mediaUrl"          value = $conf->mediaUrl}
{assign_concat var="imageAltAttribute"	0="alt='"  1=$object.title 2="'"}
{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}

	{thumb 
		width			= $thumbWidth
		height			= $thumbHeight
		file			= $mediaPath$filePath
		linkurl			= $mediaUrl$filePath
		cache			= $mediaCacheBaseURL
		cachePATH		= $mediaCachePATH
		hint			= "false"
		html			= $imageAltAttribute
		frame			= ""
	}
{else}
	       <a href="{$conf->mediaUrl}{$object.path}" target="_blank">
           <img src="{$session->webroot}img/mime/{$object.type}.gif" />
           </a>
{/if}
		</td>
		<td class="label">{t}File name{/t}:</td><td>{$object.name|default:""}</td>
	</tr>
	<tr><td class="label">{t}File type{/t}:</td><td>{$object.type|default:""}</td></tr>
	<tr><td class="label">{t}File size{/t}:</td><td>{math equation="x/y" x=$object.size y=1024 format="%d"|default:""} KB</td></tr>
	</table>
{/if}
</fieldset>
</div>