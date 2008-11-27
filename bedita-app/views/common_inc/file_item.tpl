{*
** media item on media modules index
*}

{if empty($item)} {assign var="item" value=$object} {/if}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 98}
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

{strip}

	
	<div style="overflow:hidden; height:{$thumbHeight}px" class="imagebox">

	<a href="{$linkUrl}">
	{if strtolower($item.ObjectType.name) == "image"}
	
		{if !empty($fileName) }
			
			{$beEmbedMedia->object($item,$thumbWidth,$thumbHeight,false,"fill",null,null,false)}
			
			
		{else}
		
			<img  alt="{$item.mediatype|default:'notype'}" title="{$item.mediatype|default:'notype'} | {$item.title}" src="/img/iconset/88px/{$item.mediatype|default:'notype'}.png" />
			
		{/if}

	{elseif ($item.provider|default:false)}
	
		{assign_concat var="myStyle" 0="width:" 1=$conf->videoThumbWidth 2="px; " 3="height:" 4=$conf->videoThumbHeight 5="px;"}
		{assign_associative var="attributes" style=$myStyle}
	
		{$mediaProvider->thumbnail($item, $attributes) }

	
	{else}
		
		<img alt="{$item.mediatype|default:'notype'}" title="{$item.mediatype|default:'notype'} | {$item.title}" src="/img/iconset/88px/{$item.mediatype|default:'notype'}.png" />
	
	{/if}
	</a>
	</div>
	
	
	<ul class="info_file_item bordered">

		<li style="line-height:1.2em; height:1.2em; overflow:hidden">
			{$fileTitle}
		</li>
{if strtolower($item.ObjectType.name) == "image"}
		<li>
			{$item.width}x{$item.height}px, {$item.size|default:0|filesize}
		</li>
{else}
		<li>
			{$item.mime_type} {$item.size|default:0|filesize}
		</li>
{/if}
		<li>
			{$item.created|date_format:'%b %e, %Y'}
		</li>
	</ul>







{/strip}