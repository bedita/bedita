<script type="text/javascript">
var URLBase = "{$html->url('index/')}" ;
var urlDelete = "{$html->url('delete/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}";
<!--
{literal}
$(document).ready(function(){
	//$("#tree").designTree({url:URLBase}) ;
	
	$("TABLE.indexList TD.cellList").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	$("a.delete").bind("click", function() {
		if(!confirm(message)) return false ;
		var idToDel = $(this).attr("title");
		$("#multimToDel").attr("value",idToDel);
		$("#formObject").attr("action", urlDelete) ;
		$("#formObject").get(0).submit() ;
	});
});
//-->
{/literal}
</script>	

<div id="containerPage">
{*	{if !empty($tree)}
	<div id="listAreas">
	{$beTree->tree("tree", $tree)}
	</div>
	{/if}
*}
	<div id="listElements">
	{if !empty($objects)}
	<form method="post" action="" id="formObject">
	<fieldset>
	<input type="hidden" id="multimToDel" name="data[id]"/>
	</fieldset>
	</form>
	<p class="toolbar">
		{t}{$streamTitle|capitalize}{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList">
	<tr>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{t}Thumb{/t}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{t}File name{/t}</th>
		<th>{t}File size{/t}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>&nbsp;</th>
	</tr>
	{section name="i" loop=$objects}
	<tr class="rowList">
		<td><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].id}</a></td>
		<td>
			{assign var="thumbWidth" 		value = 30}
			{assign var="thumbHeight" 		value = 30}
			{assign var="filePath"			value = $objects[i].path}
			{assign var="mediaPath"         value = $conf->mediaRoot}
			{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
			{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}

			{if strtolower($objects[i].ObjectType.name) == "image"}
				{thumb 
					width			= $thumbWidth
					height			= $thumbHeight
					file			= $mediaPath$filePath
					cache			= $mediaCacheBaseURL
					cachePATH		= $mediaCachePATH
				}
			{elseif ($objects[i].provider|default:false)}
				{assign_associative var="attributes" style="width:30px;heigth:30px;"}
				<div><a href="{$filePath}" target="_blank">{$mediaProvider->thumbnail($objects[i], $attributes) }</a></div>
			{else}
				<div><a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$objects[i].type}.gif" /></a></div>
			{/if}
		</td>
		<td class="cellList">{$objects[i].title}</td>
		<td class="cellList">{$objects[i].name}</td>
		<td class="cellList">{math equation="x/y" x=$objects[i].size|default:0 y=1024 format="%d"|default:""} KB</td>
		<td class="cellList">{$objects[i].status}</td>
		<td class="cellList">{$objects[i].created|date_format:'%b %e, %Y'}</td>
		<td class="cellList">{$objects[i].lang}</td>
		<td><a href="javascript:void(0);" class="delete" title="{$objects[i].id}">{t}Delete{/t}</a></td>
	</tr>				
	{/section}
	</table>
	<p class="toolbar">
		{t}{$streamTitle|capitalize}{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	{else}
		{t}No item found{/t}
	{/if}
	</div>
</div>