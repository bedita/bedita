<table class="indexlist" id="viewlist" style="display:block;">
		
	{capture name="theader"}
	<thead>
		<tr>
			<th colspan="2" nowrap>
				{* 
				<img class="multimediaitemToolbar viewlist" src="{$html->webroot}img/iconML-list.png" />
				<img class="multimediaitemToolbar viewsmall" src="{$html->webroot}img/iconML-small.png" />
				<img class="multimediaitemToolbar viewthumb" src="{$html->webroot}img/iconML-thumb.png" />
				*}
				 order by:
			</th>
			<th>{$beToolbar->order('id', 'id')}</th>
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('name', 'FileName')}</th>
			<th>{$beToolbar->order('mediatype', 'Type')}</th>
			<th>size</th>
			
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('modified', 'Modified')}</th>		
		</tr>
	</thead>
	{/capture}
		
		{$smarty.capture.theader}

		{section name="i" loop=$objects}
	<tr>

{strip}

		{assign var="thumbWidth" 		value = 45}
		{assign var="thumbHeight" 		value = 34}
		{assign var="filePath"			value = $objects[i].uri}
		{assign var="mediaPath"         value = $conf->mediaRoot}
		{assign var="mediaUrl"         value = $conf->mediaUrl}

		<td style="width:{$thumbWidth}px">
	
		<div style="width:{$thumbWidth}px; height:35px; overflow:hidden; border:4px solid white;">		
		
			{if strtolower($objects[i].ObjectType.name) == "image"}	
			<a href="{$html->url('view/')}{$objects[i].id}">
				{assign_associative var="params" width=$thumbWidth height=$thumbHeight mode="crop"}
				{assign_associative var="htmlAttr" width=$thumbWidth height=$thumbHeight}	
				{$beEmbedMedia->object($objects[i],$params,$htmlAttr)}
			</a>
						
			{elseif ($objects[i].provider|default:false)}
			
				{assign_associative var="htmlAttr" width="30" heigth="30"}
				<a href="{$filePath}" target="_blank">{$beEmbedMedia->object($objects[i],null,$htmlAttr)}</a>
			
			{else}
			
				<a href="{$conf->mediaUrl}{$filePath}" target="_blank">
					<img src="{$session->webroot}img/mime/{$objects[i].mime_type}.gif" />
				</a>

			{/if}
		
		</div>
			
		</td>
{/strip}
	
		<td style="width:15px;">
		{if (empty($objects[i].fixed))}
			<input  type="checkbox" name="objects_selected[]" class="objectCheck" title="{$objects[i].id}" value="{$objects[i].id}" />
		{/if}
		</td>
		
		<td>{$objects[i].id}</td>
		<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title|escape}</a></td>
		<td>{$objects[i].name}</td>
		<td>{$objects[i].mediatype}</td>
		<td>{math equation="x/y" x=$objects[i].file_size|default:0 y=1024 format="%d"|default:""} KB</td>
		<td>{$objects[i].status}</td>
		<td>{$objects[i].created|date_format:'%b %e, %Y'}</td>
	
	</tr>				
	
		{sectionelse}
		
			<td colspan="100" style="padding:30px">{t}No items found{/t}</td>
		
		{/section}

		{if ($smarty.section.i.total) >= 10}
				
					{$smarty.capture.theader}
					
		{/if}

	</table>