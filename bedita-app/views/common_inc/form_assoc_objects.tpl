<script type="text/javascript">
<!--

var urlBaseSearchItem = "{$html->url('/areas/showObjects/')}";
var urlBaseAssocItem = "{$html->url('/areas/loadObjectToAssoc/')}";

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

<div class="tab"><h2>{t}Connect to other items{/t}</h2></div>

<fieldset id="frmAssocObject">
	
	<div id="loadingAssoc" class="generalLoading" title="{t}Loading data{/t}"><span>&nbsp;</span></div>
	<div id="itemsAssociated">
		{foreach from=$conf->objRelationType item="rel"}
			<div id="relationType_{$rel}" class="relationType" style="border-bottom: 1px solid black; min-height: 130px;">
				<span style="font-weight: bold">{$rel}</span>
				<input type="hidden" class="relationTypeHidden" name="data[ObjectRelation][{$rel}][switch]" value="{$rel}" />			
				{if !empty($relObjects.$rel)}
					{foreach from=$relObjects.$rel item="objRelated" name="assocForeach"}
						{include file="../common_inc/form_assoc_object.tpl"}
					{/foreach}
				{/if}
			</div>
		{foreachelse}
		{/foreach}
	</div>
	

	
</fieldset>