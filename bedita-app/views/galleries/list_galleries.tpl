<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the gallery?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected galleries?{/t}" ;
{literal}
$(document).ready(function(){
	$("TABLE.indexList TD.cellList").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	$("#selectAll").bind("click", function(e) {
		$(".galleryCheck").each(function() { this.checked = true; });
	}) ;
	$("#unselectAll").bind("click", function(e) {
		$(".galleryCheck").each(function() { this.checked = false; });
	}) ;
});
function delGallery(id) {
	if(!confirm(message)) return false ;
	$("#galleries_to_del").attr("value",id);
	$("#formGallery").attr("action", urlDelete) ;
	$("#formGallery").get(0).submit() ;
	return false ;
}
function delGalleries() {
	if(!confirm(messageSelected)) return false ;
	var gToDel = "";
	var checkElems = document.getElementsByName('gallery_chk');
	for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) gToDel+= ","+checkElems[i].title; }
	gToDel = (gToDel=="") ? "" : gToDel.substring(1);
	$("#galleries_to_del").attr("value",gToDel);
	$("#formGallery").attr("action", urlDelete) ;
	$("#formGallery").get(0).submit() ;
	return false ;
}
{/literal}
//-->
</script>
<div id="containerPage">
	<div id="listGalleries">
	<form method="post" action="" id="formGallery">
	<fieldset>
	<input type="hidden" name="data[id]"/>
	<input type="hidden" name="galleries_to_del" id="galleries_to_del"/>
	{if $galleries}
	<p class="toolbar">
		{t}Galleries{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList" cellpadding="0" cellspacing="0" style="width:578px">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>-</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$galleries}
	<tr class="rowList">
		<td><input type="checkbox" name="gallery_chk" class="galleryCheck" title="{$galleries[i].id}"/></td>
		<td class="cellList"><a href="{$html->url('view/')}{$galleries[i].id}">{$galleries[i].id}</a></td>
		<td class="cellList">{$galleries[i].title}</td>
		<td class="cellList">{$galleries[i].status}</td>
		<td class="cellList">{$galleries[i].created|date_format:$conf->date_format}</td>
		<td class="cellList">{$galleries[i].lang}</td>
		<td><a href="javascript:void(0);" class="delete" id="g{$galleries[i].id}" onclick="javascript:delGallery('{$galleries[i].id}');">{t}Delete{/t}</a></td>
	</tr>
	{/section}
	<tr><td colspan="7"><input id="selectAll" type="button" value="O - {t}Select all{/t}"/><input id="unselectAll" type="button" value="/ - {t}Unselect all{/t}"/></td></tr>
	<tr><td colspan="7"><input id="deleteSelected" type="button" value="X - {t}Delete selected items{/t}" onclick="javascript:delGalleries();"/></td></tr>
	</tbody>
	</table>
	<p class="toolbar">
		{t}Galleries{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	{else}
	{t}No galleries found{/t}
	{/if}
	</fieldset>
	</form>
	</div>
</div>