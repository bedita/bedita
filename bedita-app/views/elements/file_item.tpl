{*
** media item on media modules index
*}

{if empty($item)} {assign var="item" value=$object} {/if}

{assign var="thumbWidth" 		value = 130}
{assign var="thumbHeight" 		value = 98}
{assign var="fileName"			value = $item.filename|default:$item.name|default:''}
{assign_concat var="linkUrl" 1=$html->url('/multimedia/view/') 2=$item.id}


{assign var="objectType_name" value=strtolower($item.ObjectType.name|default:$conf->objectTypes[$item.object_type_id].name|default:'')}


{strip}
	
	<div style="overflow:hidden; height:{$thumbHeight}px" class="imagebox">

	<a href="{$linkUrl}" title="BEobject: {$objectType_name|default:''} / Category: {$item.mediatype|default:'notype'}">
		
	{if $objectType_name == "image"}

		{assign_associative var="params" width=$thumbWidth height=$thumbHeight longside=false mode="fill" modeparam="000000" type=null upscale=false}
		{assign_associative var="htmlAttr"}
	
		{$beEmbedMedia->object($item,$params,$htmlAttr)}


	{elseif $objectType_name == "video"}
	
		{assign_associative var="params" presentation="thumb"}
	
		{if !empty($item.provider)}
			{assign_associative var="htmlAttr" width=$conf->media.video.thumbWidth height=$conf->media.video.thumbHeight}
		{else}
			{assign var="htmlAttr" value=null}
		{/if}
	
		{$beEmbedMedia->object($item,$params,$htmlAttr)}
	
	
	{else}
	
		{assign_associative var="params" mode="fill" presentation="thumb"}
		
		{$beEmbedMedia->object($item,$params)}
			
	{/if}
	</a>
	</div>
	
	<ul class="info_file_item bordered">
		<li>
			{$item.title|escape|default:'<i>[no title]</i>'}
		</li>
{if !empty($item.width)}
		<li>
			{$item.width|default:0}x{$item.height|default:0}px, {$item.file_size|default:0|filesize}
		</li>
{else}
		<li>
			{$item.mime_type} {$item.file_size|default:0|filesize}
		</li>
{/if}
		<li title="{$fileName|default:''} | {$objectType_name|default:''}">
			{$fileName|default:''} | {$objectType_name|default:''}
		</li>
		<li>
			{$item.created|date_format:'%b %e, %Y'}
		</li>
	</ul>


{/strip}