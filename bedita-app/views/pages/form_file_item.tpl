{if empty($obj)}{assign var="obj" value=$object}{/if}
{assign var="controller" 		value=$controller|default:multimedia}
{assign var="index" 			value=$index|default:1}
{assign var="thumbWidth" 		value=100}
{assign var="thumbHeight" 		value=100}
{assign var="thumbPath"         value=$conf->mediaRoot}
{assign var="thumbBaseUrl"      value=$conf->mediaUrl}
{assign var="thumbLside"		value=""}
{assign var="thumbSside"		value=""}
{assign var="thumbHtml"			value=""}
{assign var="thumbDev"			value=""}
{assign var="filePath"			value=$obj.path}
{assign var="fileName"			value=$obj.filename|default:$obj.name}
{assign var="fileTitle"			value=$obj.title}
{assign var="newPriority"		value=$obj.priority+1|default:$priority}
<div id="m_{$obj.id}" class="itemBox">
	<input type="hidden" class="index" 	name="index" value="{$index}" />
	<input type="hidden" class="id" 	name="data[{$controller}][{$index}][id]" value="{$obj.id}" />
	<input type="text" class="priority" name="data[{$controller}][{$index}][priority]" value="{$obj.priority|default:$priority}" size="3" maxlength="3"/>
	<span class="label"><b>{$fileName}</b></span>
	<br/>

	{if strtolower($obj.ObjectType.name) == "image"}
	<div style="width:{$thumbWidth}px; height:{$thumbHeight}px; overflow:hidden;">
		{if !empty($fileName) }
		{thumb 
			width			= "$thumbWidth" 
			height			= "$thumbHeight"
			file			= $thumbPath$filePath
			cache			= $conf->imgCache
			MAT_SERVER_PATH	= $conf->mediaRoot 
			MAT_SERVER_NAME	= $conf->mediaUrl
			linkurl			= "$thumbBaseUrl$filePath"
			longside		= "$thumbLside"
			shortside		= "$thumbSside"
			html			= "$thumbHtml"
			dev				= "$thumbDev"} 
		{else}
		{if strtolower($obj.ObjectType.name) == "image"}<img src="{$session->webroot}img/image-missing.jpg" width="160"/>{/if}
		{/if}
	</div>
	{else}
	<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$obj.type}.gif" /></a> </div>
	{/if}
	<br/>
	{t}Title{/t}: {$fileTitle|escape:'htmlall'}<br/>
	{t}Object type{/t}: {$obj.ObjectType.name}<br/>
	{if $obj.description}
	{t}Description{/t}:<br/>{$obj.description|escape:'htmlall'}<br/>
	{/if}
	{t}Size{/t}: {math equation="x/y" x=$obj.size y=1024 format="%d"} KB<br/>
	{if !empty($fileName) && $obj.name == "Image"}x: {$obj.width} y: {$obj.height}{/if}
	<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
	<input type="button" onclick="removeItem('m_{$obj.id}')" value="{t}Delete{/t}" />
	</div>
</div>