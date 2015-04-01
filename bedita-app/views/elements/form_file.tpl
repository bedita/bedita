{*
** detail of media item
*}


{if (isset($object)) and (!empty($object.uri))}

<div class="tab"><h2>{t}File{/t}</h2></div>

<fieldset id="multimediaitem">

	<div class="multimediaiteminside">

		{if ($object.ObjectType.name == "image")}

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

		{if isset($object) && !empty($object.uri) && $object.ObjectType.name == "image"}

		<button style="margin: 10px 0;" data-start="{if empty($object.relations) || empty($object.relations.mediamap)}hidden{else}visible{/if}" id="toggleMediamap" data-show="{t}show mediamaps{/t}" data-hide="{t}hide mediamaps{/t}"></button>

		{$html->script('flatlander', false)}
	    {$html->css('flatlander', false)}

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

{if !empty($elsewhere_hash)}
<div class="tab"><h2>{t}Other media ({$elsewhere_hash|@count}) with the same image file hash{/t}</h2></div>
<fieldset id="others">
	<table class="bordered indexlist">
		<thead>
			<tr>
				<th colspan=2>{t}Media title{/t}</th>
				<th>{t}File uri{/t}</th>
				<th>{t}status{/t}</th>
				<th>{t}modified{/t}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$elsewhere_hash item=item}
			<tr style="cursor:pointer" onClick="window.open('{$html->url('/')}view/{$item.streams.id}','_newtab');">
				<td><span class="listrecent image" style="margin:5px 5px 0 5px">&nbsp;</span></td>
				<td>{if !empty($item.objects.title)}{$item.objects.title}{else}<i>[ {$item.objects.nickname} ]</i>{/if}</td>
				<td nowrap>{$item.streams.uri}</td>
				<td style="text-align:center">{$item.objects.status}</td>
				<td nowrap style="text-align:center">{$item.objects.modified|date_format:$conf->dateTimePattern}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</fieldset>
{/if}

<div class="tab"><h2>
	{if (!isset($object)) or (empty($object.uri))}
		{t}Upload new file{/t}
	{else}
		{t}Change this file{/t}
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
