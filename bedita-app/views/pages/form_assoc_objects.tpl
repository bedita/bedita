<script type="text/javascript">
<!--

var urlBaseSearchItem = "{$html->url('/areas/showObjects/')}";
var urlBaseAssocItem = "{$html->url('/areas/loadObjectToAssoc/')}";

{literal}

function setup_drag_drop_item_assoc(el) {
	if(!$(el)) return;
	$(el).Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.7,
		containment : 'itemsAssociated'
	});
	$(el).Droppable({
		accept:		'itemBox',
		hoverclass: 'dropOver',
		ondrop:		function(dropped) {
			if(this == dropped) return;

			// swap position of an item (to the position of the previous)
			if(this == $(dropped).prev().get(0)) {
				$(this).insertAfter($(dropped)) ;
				$(this).parents(".relationType").reorderListItem();
				return ;
			// swap position of an item (to the position of the next)
			} else if(this == $(dropped).next().get(0)) {
				$(dropped).insertAfter($(this)) ;
				$(this).parents(".relationType").reorderListItem();
				return ;
			}
			// If put at the beginning, insert before
			var pDropped 	= parseInt($(".priority", dropped).attr("value")) ;
			var pThis 		= parseInt($(".priority", this).attr("value")) ;
			if(pDropped > pThis) {
				$(dropped).insertBefore($(this)) ;
			} else {
				$(dropped).insertAfter($(this)) ;
			}

			var switchEl = $(dropped).children(".switch").val();
			var switchDiv = $(this).parents(".relationType").children("input[@class='relationTypeHidden']").val();  
			if (switchEl != switchDiv) {
				$(dropped).children(".switch").val(
					$(this).parents(".relationType").children("input[@class='relationTypeHidden']").val()
				);
				$("#itemsAssociated .relationType").each(function(){
					$(this).reorderListItem();
				});				
			} else {
				$(this).parents(".relationType").reorderListItem();
			}
		}
	}) ;
}


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
	$(".itemBox").each(function(index) { 
		setup_drag_drop_item_assoc(this) ;
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[ObjectRelation]["+index+"][id]") ;
		$(".switch", this).attr("name", "data[ObjectRelation]["+index+"][switch]") ;
		$(".priority", this).attr("name", "data[ObjectRelation]["+index+"][priority]") ;
	}) ;
	$("#itemsAssociated").find("input[@type='button']").click(function() {
		$(this).parents(".itemBox").remove();
		$("#itemsAssociated .relationType").each(function(){
			$(this).reorderListItem();
		});				
	});
});
{/literal}
//-->
</script>

<h2 class="showHideBlockButton">{t}Connect to other items{/t}</h2>

<div class="blockForm" id="frmAssocObject" style="display:none">
	<div id="loadingAssoc" class="generalLoading" title="{t}Loading data{/t}"><span>&nbsp;</span></div>
	<div id="itemsAssociated">
		{foreach from=$conf->objRelationType item="rel"}
			<div id="relationType_{$rel}" class="relationType" style="border-bottom: 1px solid black; min-height: 130px;">
				<span style="font-weight: bold">{$rel}</span>
				<input type="hidden" class="relationTypeHidden" name="data[ObjectRelation][{$rel}][switch]" value="{$rel}" />			
				{if !empty($relObjects.$rel)}
					{foreach from=$relObjects.$rel item="objRelated"}
						{include file="../pages/form_assoc_object.tpl"}
						{math equation="x+y" x=$objIndex y=1 assign=objIndex}
					{/foreach}
				{/if}
			</div>
		{foreachelse}
		{/foreach}
	</div>
	
	<div class="itemAssocTree" style="clear: both;">
		<div class="assocItemSection" id="assocItemSection"></div>
		<div>
			{t}Realtion type:{/t}
			<select name="relationType" id="selectRelationType">
			{foreach from=$conf->objRelationType item="relType"}
				<option value="{$relType}">{$relType}</option>
			{/foreach}
			</select>
		</div>
		<div id="assocTreeControl">
			<a href="#">{t}Close all{/t}</a>
			<a href="#">{t}Expand all{/t}</a>
		</div>
		{$beTree->tree("assocTree", $tree)}
	</div>
	
</div>