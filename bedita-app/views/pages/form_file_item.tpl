{assign var="thumbWidth" 		value=100}
{assign var="thumbHeight" 		value=100}
{assign var="thumbCache" 		value=$CACHE}
{assign var="thumbPath"         value=$MEDIA_ROOT}
{assign var="thumbBaseUrl"      value=$MEDIA_URL}
{assign var="thumbLside"		value=""}
{assign var="thumbSside"		value=""}
{assign var="thumbHtml"			value=""}
{assign var="thumbDev"			value=""}
{assign var="imagePath" 		value=$object.path}
{assign var="imageFile" 		value=$object.filename|default:$object.name}
{assign var="imageTitle" 		value=$object.title}
{assign var="newPriority" 		value=$object.priority+1|default:$priority}
<div id="m_{$object.id}" class="itemBox">
	<input type="hidden" class="index" 	name="index" value="{$index}" />
	<input type="hidden" class="id" 	name="data[{$controller}][{$index}][id]" value="{$object.id}" />
	<input type="text" class="priority" name="data[{$controller}][{$index}][priority]" value="{$object.priority|default:$priority}" size="3" maxlength="3"/>
	<span class="label">{$imageFile}</span>
	<div style="width:{$thumbWidth}px; height:{$thumbHeight}px; overflow:hidden;">
	{$imageFile} : {$object.ObjectType.name}
	{if !empty($imageFile) && strtolower($object.ObjectType.name) == "image"}
		{thumb 
			width="$thumbWidth" 
			height="$thumbHeight" 
			file=$thumbPath$imagePath
			cache="$thumbCache" 
			MAT_SERVER_PATH=$thumbPath 
			MAT_SERVER_NAME=$thumbBaseUrl
			linkurl="$thumbBaseUrl/$imageFile"
			longside="$thumbLside"
			shortside="$thumbSside"
			html="$thumbHtml"
			dev="$thumbDev"} 
	{else}
		{if strtolower($object.ObjectType.name) == "image"}
		<img src="{$session->webroot}/img/image-missing.jpg" width="160"/>
		{else}
			type: {$object.ObjectType.name}
		{/if}
	{/if}
	</div>
	<br/>
	{t}Title{/t}:<br/>{$imageTitle|escape:'htmlall'}<br/>
	{t}Description{/t}:<br/>{$object.short_desc|escape:'htmlall'}<br/>
	{t}Size{/t}:<br/>{$object.size/1000} Kb<br/>
	{if !empty($imageFile) && $object.name == "Image"}x: {$object.width} y: {$object.height}{/if}
	<div align="right" style="padding-top:4px; margin-top:4px; border-top:1px solid silver">
	<input type="button" onclick="removeItem('m_{$object.id}')" value="{t}Delete{/t}" />
	</div>
</div>