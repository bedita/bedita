<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
{literal}
$(document).ready(function(){


	$("TABLE.indexList TD.cellList").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	/* select/unselect each item's checkbox */
	$(".selectAll").bind("click", function(e) {
		var status = this.checked;
		$(".objectCheck").each(function() { this.checked = status; });
	}) ;
	/* select/unselect main checkbox if all item's checkboxes are checked */
	$(".objectCheck").bind("click", function(e) {
		var status = true;
		$(".objectCheck").each(function() { if (!this.checked) return status = false;});
		$(".selectAll").each(function() { this.checked = status;});
	}) ;
	
	$("#deleteSelected").bind("click", delObjects);
	$("a.delete").bind("click", function() {
		delObject($(this).attr("title"));
	});
	$("#changestatusSelected").bind("click",changeStatusObjects);
});
function delObject(id) {
	if(!confirm(message)) return false ;
	$("#objects_selected").attr("value",id);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
function delObjects() {
	if(!confirm(messageSelected)) return false ;
	var oToDel = "";
	var checkElems = document.getElementsByName('object_chk');
	for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
	oToDel = (oToDel=="") ? "" : oToDel.substring(1);
	$("#objects_selected").attr("value",oToDel);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
function changeStatusObjects() {
	var status = $("#newStatus").val();
	if(status != "") {
		var oToDel = "";
		var checkElems = document.getElementsByName('object_chk');
		for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
		oToDel = (oToDel=="") ? "" : oToDel.substring(1);
		$("#objects_selected").attr("value",oToDel);
		$("#formObject").attr("action", '{/literal}{$html->url('changeStatusObjects/')}{literal}' + status) ;
		$("#formObject").get(0).submit() ;
		return false ;
	}
}
{/literal}
//-->
</script>	


<form method="post" action="" id="formObject">

<input type="hidden" name="data[id]"/>
<input type="hidden" name="objects_selected" id="objects_selected"/>

	
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
			<th>type</th>
			<th>size</th>
			
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('modified', 'Modified')}</th>		
		</tr>
	{/capture}
		
		{$smarty.capture.theader}

	</table>

	<br style="clear:both" />
	
		<div id="viewthumb">
		{foreach from=$objects item="item"}
			<div class="multimediaitem itemBox{if $item.status == "off"} off{/if}">
				
				{include file="../common_inc/file_item.tpl"}
					
			</div>
		{/foreach}
		</div>
		
	<br style="margin:0px; line-height:0px; clear:both" />
	
	<table class="indexlist" id="viewlist" style="display:none;">
	{section name="i" loop=$objects}
	<tr>

{strip}
		<td style="width:50px">
			
			{assign var="thumbWidth" 		value = 50}
			{assign var="thumbHeight" 		value = 25}
			{assign var="filePath"			value = $objects[i].path}
			{assign var="mediaPath"         value = $conf->mediaRoot}
			{assign var="mediaUrl"         value = $conf->mediaUrl}
			{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
			{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}

				
			<div>		
			
			{if strtolower($objects[i].ObjectType.name) == "image"}	
			<a href="{$html->url('view/')}{$objects[i].id}">
				{thumb 
					width			= $thumbWidth
					height			= $thumbHeight
					sharpen			= "false"
					file			= $mediaPath$filePath
					link			= "false"
					linkurl			= $mediaUrl$filePath
					window 			= "false"
					cache			= $mediaCacheBaseURL
					cachePATH		= $mediaCachePATH
					hint			= "false"
					html			= "style='border:4px solid white'"
					frame			= ""
				}	
			</a>
						
			{elseif ($objects[i].provider|default:false)}
			
				{assign_associative var="attributes" style="width:30px;heigth:30px;"}
				<a href="{$filePath}" target="_blank">{$mediaProvider->thumbnail($objects[i], $attributes) }</a>
			
			{else}
			
				<a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$objects[i].type}.gif" /></a>
			
			{/if}
		
		</div>
			
		</td>
{/strip}
	
		<td style="width:15px;">
			<input  type="checkbox" 
			name="object_chk" class="objectCheck" title="{$objects[i].id}" />
		</td>
		
		<td>{$objects[i].id}</td>
		<td>{$objects[i].title}</td>
		<td>{$objects[i].name}</td>
		<td>{$objects[i].ObjectType.name}</td>
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

<div class="tab"><h2>{t}Operations on{/t} <span class="selecteditems evidence"></span> {t}selected records{/t}</h2></div>
<div>

{t}change status to{/t}: 	<select style="width:75px" id="newStatus" data="newStatus">
								<option value=""> -- </option>
								{html_options options=$conf->statusOptions}
							</select>
			<input id="changestatusSelected" type="button" value=" ok " />
	<hr />

	
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
	
</div>

{/if}

</form>

<br />
<br />
<br />
<br />
