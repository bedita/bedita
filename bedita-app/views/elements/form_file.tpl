{*
** detail of media item
*}


{if (isset($object)) and (!empty($object.uri))}

<div class="tab"><h2>{t}File{/t}</h2></div>

<fieldset id="multimediaitem" style="margin-left:-10px;">

<div id="multimediaiteminside">

{if ($object.ObjectType.name == "image")}

	{if strpos($object.uri,'/') === 0}
		{assign_concat var="fileUrl"  1=$conf->mediaUrl  2=$object.uri}
	{else}
		{assign var="fileUrl"  value=$object.uri}
	{/if}
	{image_info var="imageInfo" file=$fileUrl}

	{assign_associative var="params" width=500 longside=false mode="fill" modeparam="000000" type=null upscale=false}

	{$beEmbedMedia->object($object,$params)}

	
{elseif strtolower(($object.ObjectType.name) == "video")}

	{assign_associative var="params" presentation="full"}
	{assign_associative var="htmlAttr" width=500 height=345}
	{$beEmbedMedia->object($object,$params,$htmlAttr)}
	
{elseif strtolower($object.ObjectType.name) == "audio"}

	{assign_associative var="htmlAttr" id="multimediaitemaudio"}
	{$beEmbedMedia->object($object, null, $htmlAttr)}
	
{elseif strtolower($object.ObjectType.name) == "application"}
	
	{assign_associative var="htmlAttributes" id="appContainer"} 
	{assign_associative var="params" presentation="full"}
	{$beEmbedMedia->object($object,$params,$htmlAttributes)}
	
{else}
		
	<a href="{$conf->mediaUrl}{$object.uri}" target="_blank">
		{$beEmbedMedia->object($object)}
	</a>


{/if}




<table class="bordered" style="margin:10px auto; width:95%; border:1px solid #999; clear:both">

	<tr>
		<th>{t}filename{/t}:</th>
		<td colspan="3">{$object.name|default:""}</td>
	</tr>
	<tr>
		<th>{t}mime type{/t}:</th>
		<td>{$object.mime_type|default:""}</td>
		<th>{t}filesize{/t}:</th>
		<td>{$object.file_size|filesize}</td>
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
		<th nowrap>{t}Human readable type{/t}:</th>
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
		<th>{t}Url{/t}: <!-- <input type="button" onclick="$('#mediaurl').copy();" value="{t}copy{/t}" /> --> </th>
		<td colspan="3">

		{if (substr($object.uri,0,7) == 'http://') or (substr($object.uri,0,8) == 'https://')}
			{assign var="uri" value=$object.uri}
		{else}
			{assign_concat var="uri" 1=$conf->mediaUrl 2=$object.uri}
		{/if}
			<a target="_blank" id="mediaurl" href="{$uri}">
				{$uri}
			</a>
		</td>
	</tr>
	{if !empty($html->params.isAjax)}
	<tr>
		<th>{t}id{/t}:</th>
		<td>
			{$object.id}
		</td>
		<th>{t}Unique name{/t}:</th>
		<td>
			{$object.nickname}
		</td>
	</tr>
	{/if}

	{if ($object.ObjectType.name != "image")}
	<tr>
		<th>{t}thumbnail{/t}</th>
		<td colspan="3">
		<input type="text" name="data[thumbnail]" value="{$object.thumbnail|default:''}" style="width: 350px;"/>
		{*if !empty($object.thumbnail)}
			<img src="{$object.thumbnail}" alt=""/>
		{/if*}
		</td>
	</tr>
	{/if}

	</table>
		
</div>

</fieldset>


{/if}


<div class="tab"><h2>
	{if (!isset($object)) or (empty($object.uri))}
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
