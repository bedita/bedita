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

		
	{elseif strtolower(($object.ObjectType.name) == "video")}
	
		{assign_associative var="params" presentation="full"}
		{assign_associative var="htmlAttr" id="multimediaitemvideo"}
		
		{$beEmbedMedia->object($object,$params,$htmlAttr)}
		
	{elseif strtolower($object.ObjectType.name) == "audio"}
	
		{assign_associative var="htmlAttr" id="multimediaitemaudio"}
		{$beEmbedMedia->object($object, null, $htmlAttr)}
		
	{elseif strtolower($object.ObjectType.name) == "application"}
		
		{assign_associative var="htmlAttributes" id="appContainer"} 
		{assign_associative var="params" presentation="full"}
		{$beEmbedMedia->object($object,$params,$htmlAttributes)}
		
	{else}
			
		<a href="{$conf->mediaUrl}{$object.path}" target="_blank">
			{$beEmbedMedia->object($object)}
		</a>
	
	
	{/if}

{if $object.ObjectType.name == "video"}
<div style="clear:left; margin-top: 20px;">
{t}thumbnail{/t}<br/>
<input type="text" name="data[thumbnail]" value="{$object.thumbnail|default:''}" style="width: 350px;"/>
{if !empty($object.thumbnail)}
	<img src="{$object.thumbnail}" alt=""/>
{/if}
</div> 
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
{if strtolower($object.ObjectType.name) == "application"}
	
	<tr>
		<th>{t}Width{/t}:</th>
		<td><input type="text" size="6" name="data[width]" value="{$object.width}"/></td>
		<th>{t}Height{/t}:</th>
		<td><input type="text" size="6" name="data[height]" value="{$object.height}"/></td>
	</tr>
	<tr>
		<th>{t}Version{/t}:</th>
		<td colspan="3"><input type="text" name="data[application_version]" value="{$object.application_version}"/></td>
	</tr>
	<tr>
		<th>{t}Text direction{/t}:</th>
		<td colspan="3">
			<select name="data[text_dir]">
				<option value=""></option>
				<option value="ltr" {if $object.text_dir == 'ltr'}selected="selected"{/if}>{t}left to right{/t}</option>
				<option value="rtl" {if $object.text_dir == 'rtl'}selected="selected"{/if}>{t}right to left{/t}</option>
			</select>
		</td>
	</tr>
	<tr>
		<th>{t}Text lang{/t}:</th>
		<td colspan="3"><input type="text" name="data[text_lang]" value="{$object.text_lang}"/></td>
	</tr>

{/if}


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
