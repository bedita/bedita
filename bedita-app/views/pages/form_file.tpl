<h2 class="showHideBlockButton">{t}File{/t}</h2>
<div class="blockForm" style="display:block" id="multimediaitem">
<fieldset>
{if (isset($object))}

<div style="width: 210px; float: left;">
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

{elseif ($object.provider|default:false)}
	{assign_associative var="attributes" style="width:30px;heigth:30px;"}

	<a href="{$object.path}" target="_blank">
		{$mediaProvider->thumbnail($object, $attributes) }
	</a>
	
{else}
	<a href="{$conf->mediaUrl}{$object.path}" target="_blank">
		<img src="{$session->webroot}img/mime/{$object.type}.gif" />
	</a>
{/if}

</div>

{assign_concat var="fileUrl"  0=$conf->mediaUrl  1=$object.path}
{image_info var="imageInfo" file=$fileUrl}

<div style="line-height: 1.6em;">
	<span class="label">{t}Name{/t}:</span> {$object.name|default:""}<br />
	<span class="label">{t}Human readable type{/t}:</span> {$imageInfo.hrtype}<br />
	<span class="label">{t}Mime type{/t}:</span> {$object.type|default:""}<br />
	<span class="label">{t}Size{/t}:</span> {math equation="x/y" x=$object.size|default:0 y=1024 format="%d"|default:""} KB<br />
	<span class="label">{t}Width{/t}:</span> {$imageInfo.w}<br />
	<span class="label">{t}Height{/t}:</span> {$imageInfo.h}<br />
	<span class="label">{t}Bit depth{/t}:</span> {$imageInfo.bits}<br />
	<span class="label">{t}Channels{/t}:</span> {$imageInfo.channels}
</div>

<div style="line-height: 1.4em; clear: left;">
	<h3>Main EXIF data</h3>
	{foreach from=$imageInfo.exif.main item="value" key="key"}
		<span class="label">{$key}</span>: {$value}<br />
	{/foreach}
</div>
{/if}

</fieldset>
</div>