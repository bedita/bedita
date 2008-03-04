<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the event?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected events?{/t}" ;
{literal}
$(document).ready(function(){
	$("TABLE.indexList TD.cellList").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	$("#selectAll").bind("click", function(e) {
		$(".documentCheck").each(function() { this.checked = true; });
	}) ;
	$("#unselectAll").bind("click", function(e) {
		$(".documentCheck").each(function() { this.checked = false; });
	}) ;
});
function delDocument(id) {
	if(!confirm(message)) return false ;
	$("#objects_to_del").attr("value",id);
	$("#formDocument").attr("action", urlDelete) ;
	$("#formDocument").get(0).submit() ;
	return false ;
}
function delDocuments() {
	if(!confirm(messageSelected)) return false ;
	var dToDel = "";
	var checkElems = document.getElementsByName('document_chk');
	for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) dToDel+= ","+checkElems[i].title; }
	dToDel = (dToDel=="") ? "" : dToDel.substring(1);
	$("#objects_to_del").attr("value",dToDel);
	$("#formDocument").attr("action", urlDelete) ;
	$("#formDocument").get(0).submit() ;
	return false ;
}
{/literal}
//-->
</script>	
<div id="containerPage">
	<div id="listAree">
	{$beTree->tree("tree", $tree)}
	</div>
	<div id="listDocuments">
	<form method="post" action="" id="formDocument">
	<fieldset>
	<input type="hidden" name="data[id]"/>
	<input type="hidden" name="objects_to_del" id="objects_to_del"/>
	{if $objects}
	<p class="toolbar">
		{t}Events{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList">
	<thead>
	<tr>
		<th>&nbsp;</th>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$objects}
	<tr class="rowList">
		<td><input type="checkbox" name="document_chk" class="documentCheck" title="{$objects[i].id}"/></td>
		<td class="cellList"><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].id}</a></td>
		<td class="cellList">{$objects[i].title}</td>
		<td class="cellList">{$objects[i].status}</td>
		<td class="cellList">{$objects[i].created|date_format:'%b %e, %Y'}</td>
		<td class="cellList">{$objects[i].lang}</td>
		<td><a href="javascript:void(0);" class="delete" id="d{$objects[i].id}" onclick="javascript:delDocument('{$objects[i].id}');">{t}Delete{/t}</a></td>
	</tr>
	{/section}
	<tr><td colspan="7"><input id="selectAll" type="button" value="O - {t}Select all{/t}"/><input id="unselectAll" type="button" value="/ - {t}Unselect all{/t}"/></td></tr>
	<tr><td colspan="7"><input id="deleteSelected" type="button" value="X - {t}Delete selected items{/t}" onclick="javascript:delDocuments();"/></td></tr>
	</tbody>
	</table>
	<p class="toolbar">
	{t}Events{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
	{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	{else}
	{t}No events found{/t}
	{/if}
	</fieldset>
	</form>
	</div>
</div>