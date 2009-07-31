<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
var urlChangeStatus = "{$html->url('changeStatusObjects/')}";
{literal}
$(document).ready(function(){
	
	$("#deleteSelected").click(function() {
		if(!confirm(message)) 
			return false ;
		$("#formObject").attr("action", urlDelete) ;
		$("#formObject").submit() ;
	});
	
	$("#changestatusSelected").click( function() {
		$("#formObject").attr("action", urlChangeStatus) ;
		$("#formObject").submit() ;
	});
});

{/literal}
//-->
</script>	


<form method="post" action="" id="formObject">

	<div id="viewthumb">
	<table class="indexlist">
	{capture name="theader"}
		<tr>
			<th colspan="2" nowrap>
				<img class="multimediaitemToolbar viewlist" src="{$html->webroot}img/iconML-list.png" />
				<img class="multimediaitemToolbar viewsmall" src="{$html->webroot}img/iconML-small.png" />
				<img class="multimediaitemToolbar viewthumb" src="{$html->webroot}img/iconML-thumb.png" />
				order by:
			</th>
			<th>{$beToolbar->order('id', 'id')}</th>
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('name', 'Name')}</th>
			<th>{$beToolbar->order('mediatype', 'Type')}</th>
			<th>size</th>
			
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('modified', 'Modified')}</th>		
		</tr>
	{/capture}
		
		{$smarty.capture.theader}

	</table>

	<br style="clear:both" />
	
		
		{foreach from=$objects item="item"}
			<div class="multimediaitem itemBox{if $item.status != "on"} off{/if}">
				
				{include file="../common_inc/file_item.tpl"}
				<table border=0 padding="0" spacing="0" style="width:100%">
					<td style="text-align:left;">{if (empty($item.fixed))}<input  type="checkbox" name="objects_selected[]" class="objectCheck" title="{$item.id}" value="{$item.id}" />{/if}</td>
					<td style="text-align:right;"><a href="" class="BEbutton">+</a></td>
				</table>
			</div>
		{/foreach}
	</div>
		
	<br style="margin:0px; line-height:0px; clear:both" />
	
	<table class="indexlist" id="viewlist" style="display:none;">
		
		{$smarty.capture.theader}
		
		{section name="i" loop=$objects}
	<tr>

{strip}

		{assign var="thumbWidth" 		value = 45}
		{assign var="thumbHeight" 		value = 34}
		{assign var="filePath"			value = $objects[i].path}
		{assign var="mediaPath"         value = $conf->mediaRoot}
		{assign var="mediaUrl"         value = $conf->mediaUrl}

		<td style="width:{$thumbWidth}px">
	
		<div style="width:{$thumbWidth}px; border:4px solid white;">		
		
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
		<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].title}</a></td>
		<td>{$objects[i].name}</td>
		<td>{$objects[i].mediatype}</td>
		<td>{math equation="x/y" x=$objects[i].size|default:0 y=1024 format="%d"|default:""} KB</td>
		<td>{$objects[i].status}</td>
		<td>{$objects[i].created|date_format:'%b %e, %Y'}</td>
	
	</tr>				
	
		{sectionelse}
		
			<td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td>
		
		{/section}

		{if ($smarty.section.i.total) >= 10}
				
					{$smarty.capture.theader}
					
		{/if}

	</table>


{if !empty($objects)}

<div style="border-top: 1px solid gray; padding-top:10px; margin-top:10px; white-space:nowrap">
	
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')} 
	&nbsp;&nbsp;&nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
	&nbsp;&nbsp;&nbsp
	<label for="selectAll"><input type="checkbox" class="selectAll" id="selectAll"/> {t}(un)select all{/t}</label>

	
</div>

<br />

<div class="tab"><h2>{t}Bulk actions on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div class="htabcontent" style="width:620px">

{t}change status to{/t}: 	<select style="width:75px" id="newStatus" name="newStatus">
								<option value=""> -- </option>
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " />
	<hr />

	
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	
</div>

{/if}

</form>


