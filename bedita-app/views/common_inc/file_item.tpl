{*
** media item on media modules index
*}

{if empty($item)} {assign var="item" value=$object} {/if}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 98}
{assign var="fileName"			value = $item.filename|default:$item.name}
{assign_concat var="linkUrl" 0=$html->url('/multimedia/view/') 1=$item.id}

{strip}

	
	<div style="overflow:hidden; height:{$thumbHeight}px" class="imagebox">

	<a href="{$linkUrl}">
	{if strtolower($item.ObjectType.name) == "image"}
		{assign_associative var="params" width=$thumbWidth height=$thumbHeight mode="fill" upscale=false}
		{assign_associative var="htmlAttr" width=$thumbWidth height=$thumbHeight alt=$item.title title=$item.name}
		
		{if !empty($fileName) }
			
			{$beEmbedMedia->object($item,$params,$htmlAttr)}
			
			
		{else}
		
			<img  alt="{$item.mediatype|default:'notype'}" title="{$item.mediatype|default:'notype'} | {$item.title}" src="/img/iconset/88px/{$item.mediatype|default:'notype'}.png" />
			
		{/if}

	{elseif ($item.provider|default:false)}
	
		{assign_associative var="htmlAttr" width=$conf->videoThumbWidth height=$conf->videoThumbHeight alt=$item.title title=$item.name}
		{$beEmbedMedia->object($item,null,$htmlAttr)}
	
	
	{else}
	
		{*$beEmbedMedia->object($item,null)*}
		
		<img alt="{$item.mediatype|default:'notype'}" 
		title="{$item.mediatype|default:'notype'} | {$item.title}" 
		src="{$html->webroot}img/iconset/88px/{$item.mediatype|default:'notype'}.png" />
			
{/if}
	</a>
	</div>
	
	
	<ul class="info_file_item bordered">

		<li style="line-height:1.2em; height:1.2em; overflow:hidden">
			{$item.title}
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