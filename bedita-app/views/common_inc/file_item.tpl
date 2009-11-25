{*
** media item on media modules index
*}

{if empty($item)} {assign var="item" value=$object} {/if}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 98}
{assign var="fileName"			value = $item.filename|default:$item.name|default:''}
{assign_concat var="linkUrl" 0=$html->url('/multimedia/view/') 1=$item.id}

{strip}

	<div style="overflow:hidden; height:{$thumbHeight}px" class="imagebox">

	<a href="{$linkUrl}">
		
	{if strtolower($item.ObjectType.name|default:'') == "image"}

		{assign_associative var="params" width=$thumbWidth height=$thumbHeight longside=false mode="fill" modeparam="000000" type=null upscale=false}
		{assign_associative var="htmlAttr" alt=$item.title title=$item.name|default:''}
		
		{if !empty($fileName) }
			
			{$beEmbedMedia->object($item,$params,$htmlAttr)}
			
			
		{else}
		
			<img  alt="{$item.mediatype|default:'notype'}" title="{$item.mediatype|default:'notype'} | {$item.title}" src="/img/iconset/88px/{$item.mediatype|default:'notype'}.png" />
			
		{/if}

	{elseif strtolower($item.ObjectType.name|default:'') == "video"}
	
		{assign_associative var="params" presentation="thumb"}
	
		{if !empty($item.provider)}
			{assign_associative var="htmlAttr" width=$conf->media.video.thumbWidth height=$conf->media.video.thumbHeight alt=$item.title title=$item.name}
		{else}
			{assign var="htmlAttr" value=null}
		{/if}
	
		{$beEmbedMedia->object($item,$params,$htmlAttr)}
	
	
	{else}
	
		{assign_associative var="params" presentation="thumb"}
		{$beEmbedMedia->object($item,$params)}
			
	{/if}
	</a>
	</div>
	
	
	<ul class="info_file_item bordered" style="line-height:1em;">

		<li style="line-height:1.2em; height:1.2em; overflow:hidden">
			{$item.title|default:'<i>[no title]</i>'}
		</li>
{if strtolower($item.ObjectType.name|default:'') == "image"}
		<li style="line-height:1.2em; height:1.2em; overflow:hidden">
			{$item.width}x{$item.height}px, {$item.size|default:0|filesize}
		</li>
{else}
		<li style="line-height:1.2em; height:1.2em; white-space:nowrap; overflow:hidden">
			{$item.mime_type} {$item.size|default:0|filesize}
		</li>
{/if}
		<li style="line-height:1.2em; height:1.2em; overflow:hidden">
			{$item.created|date_format:'%b %e, %Y'}
		</li>
	</ul>


{/strip}