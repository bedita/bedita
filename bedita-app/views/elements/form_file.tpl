{*
** detail of media item
*}


{if (isset($object)) and (!empty($object.uri))}

	<div class="tab"><h2>{t}File{/t}</h2></div>

	<fieldset id="multimediaitem" style="margin-left:-10px;">

		<div class="multimediaiteminside">

			{if ($object.ObjectType.name == "image")}

				{if strpos($object.uri,'/') === 0}
					{assign_concat var="fileUrl"  1=$conf->mediaRoot  2=$object.uri}
				{else}
					{assign var="fileUrl"  value=$object.uri}
				{/if}
				{$imgInfo = $imageInfo->get($fileUrl)}

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

			{bedev}
            {if !empty($object.uri)}
                <button>{t}delete this file or reference{/t}</button>
            {/if}
            {/bedev}

		</div>

	</fieldset>

	{$view->element('file_tech_details')}

{/if}


<div class="tab"><h2>
	{if (!isset($object)) or (empty($object.uri))}
		{t}Upload new file{/t}
	{else}
		{t}Change this file with another{/t}
	{/if}
	</h2></div>

<fieldset id="add">

{$view->element('upload_choices')}

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
