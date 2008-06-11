
{if empty($obj)} {assign var="obj" value=$object} {/if}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 85}
{assign var="filePath"			value = $obj.path}
{assign var="fileName"			value = $obj.filename|default:$obj.name}
{assign var="fileTitle"			value = $obj.title}
{assign var="newPriority"		value = $obj.priority+1|default:$priority}
{assign var="mediaPath"         value = $conf->mediaRoot}
{assign var="mediaUrl"          value = $conf->mediaUrl}
{assign_concat var="linkUrl"            0=$html->url('/multimedia/view/') 1=$obj.id}
{assign_concat var="imageAltAttribute"	0="alt='"  1=$obj.title 2="'"}
{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}



	<input type="hidden" name="data[ObjectRelation][{$obj.id}][id]" value="{$obj.id}" />
	<input type="hidden" name="data[ObjectRelation][{$obj.id}][switch]" value="{$relation}" />
	<input type="hidden" name="data[ObjectRelation][{$obj.id}][modified]" value="0" />

	{if strtolower($obj.ObjectType.name) == "image"}
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
			{if strtolower($obj.ObjectType.name) == "image"}<img src="{$session->webroot}img/image-missing.jpg" width="130"/>{/if}
		{/if}
	{elseif ($obj.provider|default:false)}
		{assign_concat var="myStyle" 0="width:" 1=$conf->videoThumbWidth 2="px; " 3="height:" 4=$conf->videoThumbHeight 5="px;"}
		{assign_associative var="attributes" style=$myStyle}
		<div><a href="{$linkUrl}" target="_blank">{$mediaProvider->thumbnail($obj, $attributes) }</a></div>
	{elseif strtolower($obj.ObjectType.name) == "audio"}
		<div><a href="{$linkUrl}"><img src="{$session->webroot}img/mime/{$obj.type}.gif" /></a></div>	
	{else}
		<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$obj.type}.gif" /></a></div>
	{/if}
	
	<label class="evidence">
		<input type="text" name="data[ObjectRelation][{$obj.id}][priority]" value="{$obj.priority|default:$priority}" size="3" maxlength="3"/>
	</label>
	<ul>
		<li>{t}title{/t}:
			<input type="text" class="info_file_item" value="{$fileTitle|escape:'htmlall'}" name="data[ObjectRelation][{$obj.id}][title]" /></li>
		<li>{t}Description{/t}:
			<textarea class="autogrow info_file_item" name="data[ObjectRelation][{$obj.id}][description]">{$obj.description|default:""|escape:'htmlall'}</textarea></li>
	</ul>
	

