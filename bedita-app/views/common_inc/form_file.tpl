{*
** detail of media item
*}

{if (isset($object)) and (!empty($object.path))}

<div class="tab"><h2>{t}File{/t}</h2></div>


<fieldset id="multimediaitem" style="margin-left:-10px;">

		
<div id="multimediaiteminside">


	{if ($object.ObjectType.name == "image")}

		{if strpos($object.path,'/') === 0}
			{assign_concat var="fileUrl"  0=$conf->mediaUrl  1=$object.path}
		{else}
			{assign var="fileUrl"  value=$object.path}
		{/if}
		{image_info var="imageInfo" file=$fileUrl}
		
		{assign_associative var="params" width=500}
		
		{$beEmbedMedia->object($object,$params)}
		

		
	{elseif ($object.provider|default:false)}
		
		{*
		{assign_concat var="myStyle" 0="width:" 1=$conf->videoThumbWidth 2="px; " 3="height:" 4=$conf->videoThumbHeight 5="px;"}
		{assign_associative var="attributes" style=$myStyle}

		<a href="{$object.path}" target="_blank">
			{$beEmbedMedia->object($object,null,$attributes)}
		</a>
		<embed 
			src	= "{$html->webroot}swf/mediaplayer.swf" 
			width	= "{$conf->videoWidth}"
			height	= "{$conf->videoHeight}"
			allowscriptaccess = "always"
			allowfullscreen = "true"
			flashvars = "file={$mediaProvider->sourceEmbed($object) }&backcolor=0x000000&frontcolor=0xFFFFFF&lightcolor=0x000000&overstretch=true&searchbar=false&autostart=false"
		/>
		*}
	
		{assign_associative var="params" presentation="full"}
		{assign_associative var="htmlAttr" width=$conf->media.video.width height=$conf->media.video.height}
		
		{$beEmbedMedia->object($object,$params,$htmlAttr)}
		
		
	{elseif strtolower($object.ObjectType.name) == "audio"}
	
	
		<a href="{$conf->mediaUrl}{$object.path}" target="_blank">
			<img src="{$session->webroot}img/mime/{$object.mime_type}.gif" />
		</a>
	
		<embed 
			src		= "{$html->webroot}swf/mediaplayer.swf" 
			width	= "{$conf->audioWidth}"
			height	= "{$conf->audioHeight}"
			allowscriptaccess = "always"
			allowfullscreen = "true"
			flashvars = "file={$conf->mediaUrl}{$object.path}&backcolor=0x000000&frontcolor=0xFFFFFF&lightcolor=0x000000&overstretch=true&searchbar=false&autostart=false"
		/>
		
		
	{else}
	
	
		<a href="{$conf->mediaUrl}{$object.path}" target="_blank">
			{$beEmbedMedia->object($object)}
		</a>
	
	
	{/if}




<table class="bordered" style="margin-top:10px; border:1px solid #999; width:100%; clear:both">

	<tr>
		<th>{t}filename{/t}:</th>
		<td colspan="3">{$object.name|default:""}</td>
	</tr>
	<tr>
		<th>{t}mime type{/t}:</th>
		<td>{$object.mime_type|default:""}</td>
		<th>{t}filesize{/t}:</th>
		<td>{$object.size|filesize}</td>
	</tr>


{if ($object.ObjectType.name == "image")}
	
	<tr>
		<th>{t}Human readable type{/t}:</th>
		<td>{$imageInfo.hrtype}</td>
		<th>{t}Orientation{/t}:</th>
		<td>{$imageInfo.orientation}</td>
	</tr>
	<tr>
		<th>{t}Width{/t}:</th>
		<td>{$imageInfo.w}</td>
		<th>{t}Height{/t}:</th>
		<td>{$imageInfo.h}</td>
	</tr>
	<tr>
		<th>{t}Bit depth{/t}:</th><td>{$imageInfo.bits}</td>
		<th>{t}Channels{/t}:</th><td>{$imageInfo.channels}</td>
	</tr>
	
{/if}
	<tr>
		<th></th>
		<td>
			
			{if (substr($object.path,0,7) == 'http://') or (substr($object.path,0,8) == 'https://')}
			<a target="_blank" href="{$object.path}">
			{else}
			<a target="_blank" href="{$conf->mediaUrl}{$object.path}">
			{/if}
				› {t}open file in a separate window{/t}
			</a>
		</td>
	</tr>
</table>

</div>

</fieldset>


{/if}


<div class="tab"><h2>
	{if (!isset($object)) or (empty($object.path))}
		{t}Upload new file{/t}
	{else}
		{t}Change this file with another{/t}
	{/if}
	</h2></div>

<fieldset id="add">
	

<table class="htab">
	<td rel="uploadItems">{t}browse your disk{/t}</td>
	<td rel="urlItems">{t}add by url{/t}</td>
</table>


<div class="htabcontainer" id="addmultimediacontents">

	<div class="htabcontent" id="uploadItems">
		<input style="margin:20px; width:270px;" type="file" name="Filedata" />
	</div>
	
	
	<div class="htabcontent" id="urlItems">
		
		<table style="margin:20px;">
		<tr>
			<td>{t}Url{/t}:</td>
			<td><input type="text" style="width:270px;" name="data[url]" /></td>
		</tr>
		
		</table>
	</div>

</div>

</fieldset>
