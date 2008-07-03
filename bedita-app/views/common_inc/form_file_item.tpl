{if empty($item)} {assign var="item" value=$object} {/if}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 85}
{assign var="filePath"			value = $item.path}
{assign var="fileName"			value = $item.filename|default:$item.name}
{assign var="fileTitle"			value = $item.title}
{assign var="newPriority"		value = $item.priority+1|default:$priority}
{assign var="mediaPath"         value = $conf->mediaRoot}
{assign var="mediaUrl"          value = $conf->mediaUrl}
{assign_concat var="linkUrl"            0=$html->url('/multimedia/view/') 1=$item.id}
{assign_concat var="imageAltAttribute"	0="alt='"  1=$item.title 2="'"}
{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}



	<input type="hidden" name="data[ObjectRelation][{$item.id}][id]" value="{$item.id}" />
	<input type="hidden" name="data[ObjectRelation][{$item.id}][switch]" value="{$relation}" />
	<input type="hidden" name="data[ObjectRelation][{$item.id}][modified]" value="0" />

	{if strtolower($item.ObjectType.name) == "image"}
	
		{if !empty($fileName) }
			{thumb 
				width			= $thumbWidth
				height			= $thumbHeight
				file			= $mediaPath$filePath
				link = "false"
				linkurl			= $linkUrl
				cache			= $mediaCacheBaseURL
				cachePATH		= $mediaCachePATH
				hint			= "false"
				html			= $imageAltAttribute
				frame			= ""
				window			= "false"
			}
		{else}
		
			{if strtolower($item.ObjectType.name) == "image"}<img src="{$session->webroot}img/image-missing.jpg" width="{$thumbWidth}" />{/if}
			
		{/if}
		
	{elseif ($item.provider|default:false)}
	
		{assign_concat var="myStyle" 0="width:" 1=$conf->videoThumbWidth 2="px; " 3="height:" 4=$conf->videoThumbHeight 5="px;"}
		{assign_associative var="attributes" style=$myStyle}
	
		<div><a href="{$linkUrl}" target="_blank">{$mediaProvider->thumbnail($item, $attributes) }</a></div>
	
	{elseif strtolower($item.ObjectType.name) == "audio"}
	
		<div><a href="{$linkUrl}"><img src="{$session->webroot}img/mime/{$item.type}.gif" /></a></div>	
	
	{else}
	
		<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$item.type}.gif" /></a></div>
	
	{/if}
	
	<label class="evidence">
		<input type="text" class="priority" name="data[ObjectRelation][{$item.id}][priority]" value="{$item.priority|default:$priority}" size="3" maxlength="3"/>
	</label>
	
	<ul class="info_file_item">
		{*
		<li>{t}title{/t}:
			<input type="text" class="info_file_item" value="{$fileTitle|escape:'htmlall'}" name="data[ObjectRelation][{$item.id}][title]" />
		</li>
		{t}Description{/t}:		*}
		<li>
			<textarea class="info_file_item" style="border-left:0px; border-right:0px" name="data[ObjectRelation][{$item.id}][description]">{$item.description|default:""|escape:'htmlall'}</textarea>
			<br />
			<a href="{$linkUrl}">details</a>
		</li>
	</ul>
	

