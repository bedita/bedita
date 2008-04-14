
{if empty($obj)} {assign var="obj" value=$object} {/if}
{assign var="thumbWidth" 		value = 110}
{assign var="thumbHeight" 		value = 100}
{assign var="filePath"			value = $obj.path}
{assign var="fileName"			value = $obj.filename|default:$obj.name}
{assign var="fileTitle"			value = $obj.title}
{assign var="newPriority"		value = $obj.priority+1|default:$priority}
{assign var="mediaPath"         value = $conf->mediaRoot}
{assign var="mediaUrl"          value = $conf->mediaUrl}
{assign_concat var="linkUrl"            0=$html->url('/multimedia/view/') 1=$obj.id}    {* vecchio lunkurl $mediaUrl$filePath - forse c'è un modo migliore x astrarre il link al modulo in multimedia *}
{assign_concat var="imageAltAttribute"	0="alt='"  1=$obj.title 2="'"}
{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}

{* da eliminare, solo x test *}
{*assign var="description"		value="Lorem ipsum dolor sit amet, consectetur adipisicing elit."*}

<div id="m_{$obj.id}" class="itemBox">
	<input type="hidden" class="index" 	name="index" value="{$objIndex}" />
	<input type="hidden" class="id" 	name="data[ObjectRelation][{$objIndex}][id]" value="{$obj.id}" />
	<input type="hidden" class="switch" name="data[ObjectRelation][{$objIndex}][switch]" value="{$relation}" />
	<input type="hidden" class="modified" name="data[ObjectRelation][{$objIndex}][modified]" value="0" />

	<div class="itemHeader">
		<input type="text" class="priority" name="data[ObjectRelation][{$objIndex}][priority]" value="{$obj.priority|default:$priority}" size="3" maxlength="3"/>
	</div>

	{if strtolower($obj.ObjectType.name) == "image"}
	<div id="imageBox">
		{if !empty($fileName) }
		{thumb 
			width			= $thumbWidth
			height			= $thumbHeight
			file			= $mediaPath$filePath
			linkurl			= $linkUrl
			cache			= $mediaCacheBaseURL
			cachePATH		= $mediaCachePATH
			hint			= "false"
			html			= $imageAltAttribute
			frame			= ""
			window			= "false"
		}
		{else}
		{if strtolower($obj.ObjectType.name) == "image"}<img src="{$session->webroot}img/image-missing.jpg" width="160"/>{/if}
		{/if}
	</div>
	{elseif ($obj.provider|default:false)}
		{assign_concat var="myStyle" 0="width:" 1=$conf->videoThumbWidth 2="; " 3="height:" 4=$conf->videoThumbHeight}
		{assign_associative var="attributes" style=$myStyle}
		<div><a href="{$linkUrl}" target="_blank">{$mediaProvider->thumbnail($obj, $attributes) }</a></div>
	{elseif strtolower($obj.ObjectType.name) == "audio"}
		<div><a href="{$linkUrl}"><img src="{$session->webroot}img/mime/{$obj.type}.gif" /></a></div>	
	{else}
		<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$obj.type}.gif" /></a></div>
	{/if}

	<div class="itemInfo">
		<div><span class="title">{t}Title{/t}:</span><br /><input type="text" class="info_file_item" value="{$fileTitle|escape:'htmlall'}" name="data[ObjectRelation][{$objIndex}][title]" /></div>
		<div><span class="title">{t}Description{/t}:</span><br /><textarea class="autogrow info_file_item" name="data[ObjectRelation][{$objIndex}][description]">{$obj.description|default:""|escape:'htmlall'}</textarea></div>
		<div style="border-bottom: 1px solid #999;"></div>
		<div><span class="title">{t}File{/t}:</span> {$fileName|escape:'htmlall'}</div>
		<div><span class="title">{t}Type{/t}:</span> {t}{$obj.type}{/t}</div>
		<div><span class="title">{t}Size{/t}</span>: {math equation="x/y" x=$obj.size|default:0 y=1024 format="%d"} KB</div>
		{if $obj.width|default:false && $obj.height}<div>{$obj.width}px X {$obj.height}px</div>{/if}
	</div>

	<div class="itemInfoSmall" style="display: none;">{$fileTitle|escape:'htmlall'|wordwrap:13:"\n":true}</div>
	
	<div class="itemFooter">
		<input type="button" onclick="removeItem('m_{$obj.id}')" value="{t}X{/t}" />
	</div>
</div>