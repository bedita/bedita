<script type="text/javascript">
<!--

var urlBaseSearchItem = "{$html->url('/areas/showObjects/')}";
var urlBaseAssocItem = "{$html->url('/areas/loadObjectToAssoc/')}";
var indexAssoc = {$objIndex|default:"0"} ;

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
				$("#itemsAssociated").reorderListItem() ;
				return ;
			// swap position of an item (to the position of the next)
			} else if(this == $(dropped).next().get(0)) {
				$(dropped).insertAfter($(this)) ;
				$("#itemsAssociated").reorderListItem() ;
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
			$("#itemsAssociated").reorderListItem() ;
		}
	}) ;
}


// Get data from modal window, uploaded files and insert new object in the form
function uploadItemById(id, rel) {
	$("#loadingAssoc").show();
	var emptyDiv = "<div><\/div>"; 
	$(emptyDiv).load(urlBaseAssocItem + id + "/" + index, function () {
		$("#itemsAssociated").append(this) ; 
		$("#itemsAssociated").reorderListItem();
		$(".itemBox", this).each(function() {
				setup_drag_drop_item_assoc(this) ;
			}) ;
		$("#loadingAssoc").hide();
		$(this).find("input[@type='button']").click(function() {
			$(this).parents(".itemBox").remove();
			$("#itemsAssociated").reorderListItem();
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
	$(".itemBox").each(function(index) { setup_drag_drop_item_assoc(this) ;}) ;
	$("#itemsAssociated .itemBox").find("input[@type='button']").click(function() {
		$(this).parents(".itemBox").remove();
		$("#itemsAssociated").reorderListItem();
	});
});
{/literal}
//-->
</script>

<h2 class="showHideBlockButton">{t}Connect to other items{/t}</h2>

<div class="blockForm" id="frmAssocObject" style="display:none">
	<div id="loadingAssoc" class="generalLoading" title="{t}Loading data{/t}"><span>&nbsp;</span></div>
	<div id="itemsAssociated">
		{foreach from=$relObjects item="relationType" key="rel"}
			{if $rel != "attach"}
				{foreach from=$relationType item="objRelated"}
					{include file="../pages/form_assoc_object.tpl"}
					{math equation="x+y" x=$objIndex y=1 assign=objIndex}
				{/foreach}
			{/if}
		{foreachelse}
		{/foreach}
	</div>
	
	<div class="itemAssocTree" style="clear: both;">
		<div class="assocItemSection" id="assocItemSection"></div>
		<div id="assocTreeControl">
			<a href="#">{t}Close all{/t}</a>
			<a href="#">{t}Expand all{/t}</a>
		</div>
		{$beTree->tree("assocTree", $tree)}
	</div>
	
</div>