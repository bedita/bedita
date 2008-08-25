<script type="text/javascript">
<!--

var urlBaseSearchItem = "{$html->url('/areas/inc/showObjects/')}";
var urlBaseAssocItem = "{$html->url('/areas/inc/loadObjectToAssoc/')}";

{literal}
// Get data from modal window, uploaded files and insert new object in the form
function uploadItemById(id, rel) {
	var divToFill = "#relationType_" + rel;
	$("#loadingAssoc").show();
	var emptyDiv = "<div><\/div>"; 
	$(emptyDiv).load(urlBaseAssocItem + id + "/" + rel, function () {
		$(divToFill).append(this) ; 
		$(divToFill).reorderListItem();
		$(".itemBox", this).each(function() {
				setup_drag_drop_item_assoc(this) ;
			}) ;
		$("#loadingAssoc").hide();
		$(this).find("input[@type='button']").click(function() {
			$(this).parents(".itemBox").remove();
			$(divToFill).reorderListItem();
		});
	}) ;
	
	try { $().alertSignal() ; } catch(e) {}
}

/*
$(document).ready(function() {
	$("#assocTree").designTree({
		id_control: "assocTreeControl",
		collapsed: false,
		urlVoid: true
	});
	$("#assocTree a").click(function() {
		var idAreaSection = $(this).parents("li").children("input[@name='id']").attr('value');
		$("#loadingAssoc").show();
		$("#assocItemSection").load(urlBaseSearchItem + idAreaSection, function() {
			$("#loadingAssoc").hide();
		});
	});
	
	$(".itemBox").each(function (index) {
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[ObjectRelation]["+index+"][id]") ;
		$(".switch", this).attr("name", "data[ObjectRelation]["+index+"][switch]") ;
		$(".priority", this).attr("name", "data[ObjectRelation]["+index+"][priority]") ;
	}) ;
			
	$("#itemsAssociated .itemBox").each(function() {setup_drag_drop_item_assoc(this) }) ;
	$("#itemsAssociated").find("input[@type='button']").click(function() {
		$(this).parents(".itemBox").remove();
		$("#itemsAssociated .relationType").each(function(){
			$(this).reorderListItem();
		});				
	});
});
*/
{/literal}
//-->
</script>



<div class="tab"><h2>{t}Connections{/t}</h2></div>

<fieldset id="frmAssocObject">
	
	<div id="loadingAssoc" class="generalLoading" title="{t}Loading data{/t}"></div>
	
	<ul class="htab">
	{foreach from=$conf->objRelationType item="rel"}
			<li rel="relationType_{$rel}">{$rel}</li>
	{/foreach}
	</ul>
	
	<div class="htabcontainer" id="pippo">
	{foreach from=$conf->objRelationType item="rel"}
	<div class="htabcontent" id="relationType_{$rel}">
		<input type="hidden" class="relationTypeHidden" name="data[ObjectRelation][{$rel}][switch]" value="{$rel}" />				
		
		{* /////// modello da eliminare , il loop corretto si trova in form_assoc_object.tpl ///////// *}
		<table class="indexlist">
			{section name=i loop=5}
			<tr>
				<td style="padding:0px; width:20px;">
					<input type="text" class="priority" 
					style="margin:0px; width:20px; text-align:right; background-color:transparent" 
					name="data[ObjectRelation][{$smarty.section.i.index}][priority]" value="{$smarty.section.i.iteration}" size="3" maxlength="3"/>
				</td>
				<td style="width:10px;">
					<span class="listrecent documents" style="margin:0px">&nbsp;</span>
				</td>
				<td>Nome dell'oggetto dev'esser sortable</td>
				<td>draft</td>
				<td>ita</td>
				<td>
					<input class="BEbutton link" href="{$html->url('/')}documents/view/101" name="details" type="button" value="details">
					<input class="BEbutton" name="remove" type="button" value="remove">
				</td>
			</tr>
			{/section}
		</table>
		<hr />
		aggiungi nuova relazione di tipo "{$rel}": 
		<br />
		<label>add by object ids</label>: <input type="text" size="12" /> 
		<input class="BEbutton" name="add" type="button" value="add">
		&nbsp; or &nbsp;
		<input type="button" class="modalbutton" rel="{$html->url('/areas/listAllContents')}" value="choose objects" />

		
		<br />
		
		
		
		
		{*  /////// end modello ///////// *}
		
		
		{if !empty($relObjects.$rel)}
			<table class="indexlist">
			{foreach from=$relObjects.$rel item="objRelated" name="assocForeach"}

				{include file="../common_inc/form_assoc_object.tpl"}
			
			{/foreach}
			</table>
		{/if}
		
	</div>
	{/foreach}
	</div>


	
</fieldset>