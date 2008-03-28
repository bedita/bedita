{if empty($obj)} {assign var="obj" value=$object} {/if}
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
{assign var="description"		value="descriz descriz asd descrizs ds descriz s descrizs dd descriz asds descriz descriz descriz."}

<div id="m_{$obj.id}" class="itemBox">
	<input type="hidden" class="index" 	name="index" value="{$index}" />
	<input type="hidden" class="id" 	name="data[{$controller}][{$index}][id]" value="{$obj.id}" />

	<input type="text" class="priority" name="data[{$controller}][{$index}][priority]" value="{$obj.priority|default:$priority}" maxlength="3"/>
	<div class="label">{$fileTitle}</div>

	{if strtolower($obj.ObjectType.name) == "image"}
	<div style="width: {$thumbWidth}px; height: {$thumbHeight}px; overflow: hidden; position: relative; top: 40px; margin-bottom: 40px;">
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
			dev				= "$thumbDev"
			addgreytohint	= "false"}
		{else}
		{if strtolower($obj.ObjectType.name) == "image"}<img src="{$session->webroot}img/image-missing.jpg" width="160"/>{/if}
		{/if}
	</div>
	{else}
	<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$obj.type}.gif" /></a></div>
	{/if}
	<div class="itemInfo">
		<span class="title">{t}File name{/t}:</span> {$fileName|escape:'htmlall'}<br/>
		<span class="title">{t}Object type{/t}:</span> {t}{$obj.ObjectType.name}{/t}<br/>
		<span class="title">{t}Description{/t}:</span> {$description}<br/>
		{if $obj.description}
		{t}Description{/t}:<br/>{$obj.description|escape:'htmlall'}<br/>
		{/if}
		{t}Size{/t}: {math equation="x/y" x=$obj.size y=1024 format="%d"} KB<br/>
		{if !empty($fileName) && $obj.name == "Image"}x: {$obj.width} y: {$obj.height}{/if}
	</div>
	<div class="itemButtons">
		<input type="button" onclick="removeItem('m_{$obj.id}')" value="{t}Delete{/t}" />
	</div>
</div>