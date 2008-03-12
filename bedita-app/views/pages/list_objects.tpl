<script type="text/javascript">
<!--
var urlDelete = "{$html->url('delete/')}" ;
var message = "{t}Are you sure that you want to delete the item?{/t}" ;
var messageSelected = "{t}Are you sure that you want to delete selected items?{/t}" ;
var URLBase = "{$html->url('index/')}" ;
{literal}
$(document).ready(function(){
	
	$("#tree").designTree({url: URLBase}) ;

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
});
function delObject(id) {
	if(!confirm(message)) return false ;
	$("#objects_to_del").attr("value",id);
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
	$("#objects_to_del").attr("value",oToDel);
	$("#formObject").attr("action", urlDelete) ;
	$("#formObject").get(0).submit() ;
	return false ;
}
{/literal}
{if !empty($areasectiontree)}
{literal}
function assocObjectsToAreaSection(id) {
	var oToDel = "";
	var checkElems = document.getElementsByName('object_chk');
	for(var i=0;i<checkElems.length;i++) { if(checkElems[i].checked) oToDel+= ","+checkElems[i].title; }
	oToDel = (oToDel=="") ? "" : oToDel.substring(1);
	$("#objects_to_del").attr("value",oToDel);
	$("#formObject").attr("action", '{/literal}{$html->url('addToAreaSection/')}{literal}') ;
	$("#formObject").get(0).submit() ;
	return false ;
}
{/literal}
{/if}
//-->
</script>	
<div id="containerPage">
	<div id="listAreas">
	{$beTree->tree("tree", $tree)}
	</div>
	<div id="listElements">
	<form method="post" action="" id="formObject">
	<fieldset>
	<input type="hidden" name="data[id]"/>
	<input type="hidden" name="objects_to_del" id="objects_to_del"/>
	{if $objects}
	<p class="toolbar">
		{t}{$moduleName|capitalize}{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList">
	<thead>
	<tr>
		<th><input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label></th>
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
		<td><input type="checkbox" name="object_chk" class="objectCheck" title="{$objects[i].id}"/></td>
		<td class="cellList"><a href="{$html->url('view/')}{$objects[i].id}">{$objects[i].id}</a></td>
		<td class="cellList">{$objects[i].title}</td>
		<td class="cellList">{$objects[i].status}</td>
		<td class="cellList">{$objects[i].created|date_format:'%b %e, %Y'}</td>
		<td class="cellList">{$objects[i].lang}</td>
		<td><a href="javascript:void(0);" class="delete" title="{$objects[i].id}">{t}Delete{/t}</a></td>
	</tr>
	{/section}
	<tr><td colspan="7"><input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/></td></tr>
	{if !empty($areasectiontree)}
	<tr>
		<td colspan="7">
			<input id="deleteSelected" type="button" value="(+) {t}Add selected items to area/section{/t}" onclick="javascript:assocObjectsToAreaSection();"/>
			<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
			{foreach from=$areasectiontree item=i}
				<option value="{$i.id}">{$i.title}</option>
				{if !empty($i.children)}
					{foreach from=$i.children item=ii}
					<option value="{$ii.id}">-- {$ii.title}</option>
					{if !empty($ii.children)}
						{foreach from=$ii.children item=iii}
						<option value="{$iii.id}">--- {$iii.title}</option>
						{/foreach}
					{/if}
					{/foreach}
				{/if}
			{/foreach}
			</select>
		</td>
	</tr>
	{/if}
	</tbody>
	</table>
	<p class="toolbar">
	{t}{$moduleName|capitalize}{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
	{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
	{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
	{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	{else}
	{t}No {$moduleName} found{/t}
	{/if}
	</fieldset>
	</form>
	</div>
</div>