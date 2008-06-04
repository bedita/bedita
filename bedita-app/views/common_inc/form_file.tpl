{*
** pages form-file template
** @author ChannelWeb srl
*}

<h2 class="showHideBlockButton">{t}File{/t}</h2>
<div class="blockForm" id="multimediaitem">
<fieldset>
{if (isset($object))}

<div style="margin: 0 5px 5px 0; float: left;">
	{if ($object.ObjectType.name == "image")}
		{assign var="filePath"			value = $object.path}
		{assign var="fileName"			value = $object.filename|default:$obj.name}
		{assign var="fileTitle"			value = $object.title}
		{assign var="mediaPath"         value = $conf->mediaRoot}
		{assign var="mediaUrl"          value = $conf->mediaUrl}
		{assign_concat var="imageAltAttribute"	0="alt='"  1=$object.title 2="'"}
		{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
		{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}

		{assign_concat var="fileUrl"  0=$conf->mediaUrl  1=$object.path}
		{image_info var="imageInfo" file=$fileUrl}
		{if $imageInfo.landscape}
			{assign var="thumbWidth" 		value = 300}
			{assign var="thumbHeight" 		value = 200}
		{else}
			{assign var="thumbWidth" 		value = 200}
			{assign var="thumbHeight" 		value = 300}
		{/if}

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
		{assign_concat var="myStyle" 0="width:" 1=$conf->videoThumbWidth 2="px; " 3="height:" 4=$conf->videoThumbHeight 5="px;"}
		{assign_associative var="attributes" style=$myStyle}

	<a href="{$object.path}" target="_blank">
		{$mediaProvider->thumbnail($object, $attributes) }
	</a>
	
		{$mediaProvider->embed($object, $attributes) }
	<embed 
		src	= "{$html->webroot}swf/mediaplayer.swf" 
		width	= "{$conf->videoWidth}"
		height	= "{$conf->videoHeight}"
		allowscriptaccess = "always"
		allowfullscreen = "true"
		flashvars = "file={$mediaProvider->sourceEmbed($object) }&backcolor=0x000000&frontcolor=0xFFFFFF&lightcolor=0x000000&overstretch=true&searchbar=false&autostart=false"
	/>
	
	{elseif strtolower($object.ObjectType.name) == "audio"}
	<a href="{$conf->mediaUrl}{$object.path}" target="_blank">
		<img src="{$session->webroot}img/mime/{$object.type}.gif" />
	</a>

	<embed 
		src		= "{$html->webroot}swf/mediaplayer.swf" 
		width	= "{$conf->audioWidth}"
		height	= "{$conf->audioHeight}"
		allowscriptaccess = "always"
		allowfullscreen = "true"
		flashvars = "file={$conf->mediaUrl}{$object.path}&backcolor=0x000000&frontcolor=0xFFFFFF&lightcolor=0x000000&overstretch=true&searchbar=false&autostart=false"
	/>
	{else}
	<a href="{$conf->mediaUrl}{$object.path}" target="_blank">
		<img src="{$session->webroot}img/mime/{$object.type}.gif" />
	</a>
	{/if}

</div>


<div style="line-height: 1.6em;">
	<span class="label">{t}Name{/t}:</span> {$object.name|default:""}<br />
	<span class="label">{t}Mime type{/t}:</span> {$object.type|default:""}<br />
	{if ($object.ObjectType.name == "image")}
	<span class="label">{t}Size{/t}:</span> {math equation="x/y" x=$object.size|default:0 y=1024 format="%d"|default:""} KB<br />
	<span class="label">{t}Human readable type{/t}:</span> {$imageInfo.hrtype}<br />
	<span class="label">{t}Width{/t}:</span> {$imageInfo.w}<br />
	<span class="label">{t}Height{/t}:</span> {$imageInfo.h}<br />
	<span class="label">{t}Bit depth{/t}:</span> {$imageInfo.bits}<br />
	<span class="label">{t}Channels{/t}:</span> {$imageInfo.channels}<br />
	<span class="label">{t}Orientation{/t}:</span> {$imageInfo.orientation}
	{/if}
</div>

</fieldset>
</div>


{* EXIF *}
{if $object.ObjectType.name == "image" && $imageInfo.hrtype eq "JPG"}
<h2 class="showHideBlockButton">{t}Exif - Main Data{/t}</h2>
<div class="blockForm" id="exifdata">
<fieldset>

	<div style="line-height: 1.4em;">
	{if $imageInfo.exif.main}
		{foreach from=$imageInfo.exif.main item="value" key="key"}
		<span class="label">{$key}</span>: {$value}<br />
		{/foreach}
	{/if}

	{if $imageInfo.exif.XMP}
		<h2 style="margin-top: 10px;">XMP (Adobe) data</h2>
		{section name=XMP loop=$imageInfo.exif.XMP}
		{if $imageInfo.exif.XMP[XMP].value}
		<span class="label">{$imageInfo.exif.XMP[XMP].item}</span>: {$imageInfo.exif.XMP[XMP].value}<br />
		{/if}
		{/section}
	{/if}

	{if !$imageInfo.exif.main && !$imageInfo.exif.XMP}
	EXIF records are empty.
	{/if}
	</div>

</fieldset>
</div>
{/if}

{/if} {* if $obj, line 4 *}
