
{if empty($obj)} {assign var="obj" value=$object} {/if}
{assign var="thumbWidth" 		value = 100}
{assign var="thumbHeight" 		value = 100}
{assign var="filePath"			value = $obj.path}
{assign var="fileName"			value = $obj.filename|default:$obj.name}
{assign var="fileTitle"			value = $obj.title}
{assign var="newPriority"		value = $obj.priority+1|default:$priority}
{assign var="mediaPath"         value = $conf->mediaRoot}
{assign var="mediaUrl"          value = $conf->mediaUrl}
{assign_concat var="imageAltAttribute"	0="alt='"  1=$obj.title 2="'"}
{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}

{* da eliminare, solo x test *}
{*assign var="description"		value="Lorem ipsum dolor sit amet, consectetur adipisicing elit."*}

<div id="m_{$obj.id}" class="itemBox">
	<input type="hidden" class="index" 	name="index" value="{$objIndex}" />
	<input type="hidden" class="id" 	name="data[ObjectRelation][{$objIndex}][id]" value="{$obj.id}" />
	<input type="hidden" class="switch" name="data[ObjectRelation][{$objIndex}][switch]" value="{$relation}" />

	<div class="itemHeader">
		<input type="text" class="priority" name="data[ObjectRelation][{$objIndex}][priority]" value="{$obj.priority|default:$priority}" size="3" maxlength="3"/>
	</div>

	{if strtolower($obj.ObjectType.name) == "image"}
	<div style="width: {$thumbWidth+2}px; height: {$thumbHeight+2}px;" id="imageBox">
		{if !empty($fileName) }
		{thumb 
			width			= $thumbWidth
			height			= $thumbHeight
			file			= $mediaPath$filePath
			linkurl			= $mediaUrl$filePath
			cache			= $mediaCacheBaseURL
			cachePATH		= $mediaCachePATH
			hint			= "false"
			frame			= ""
			html			= $imageAltAttribute
		}
		{else}
		{if strtolower($obj.ObjectType.name) == "image"}<img src="{$session->webroot}img/image-missing.jpg" width="160"/>{/if}
		{/if}
	</div>
	{else}
	<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$obj.type}.gif" /></a></div>
	{/if}

	<div class="itemInfo">
		<div><span class="title">{t}Title{/t}:</span><input type="text" value="{$fileTitle|escape:'htmlall'}" /></div>
		<div><span class="title">{t}Description{/t}:</span><textarea class="autogrow">{$obj.description|default:""|escape:'htmlall'}</textarea></div>
		<div style="border-bottom: 1px solid #999;"></div>
		<div><span class="title">{t}File{/t}:</span> {$fileName|escape:'htmlall'}</div>
		<div><span class="title">{t}Type{/t}:</span> {t}{$obj.type}{/t}</div>
		<div><span class="title">{t}Size{/t}</span>: {math equation="x/y" x=$obj.size y=1024 format="%d"} KB</div>
		{if $obj.width && $obj.height}<div>{$obj.width}px X {$obj.height}px</div>{/if}
	</div>

	<div class="itemInfoSmall" style="display: none;">{$fileTitle|escape:'htmlall'}</div>
	
	<div class="itemFooter">
		<input type="button" onclick="removeItem('m_{$obj.id}')" value="{t}X{/t}" />
	</div>
</div>