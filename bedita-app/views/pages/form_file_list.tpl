<h2 class="showHideBlockButton">{t}{$title}{/t}</h2>
<div class="blockForm" id="imgs" style="display:none">
<script type="text/javascript">
var urlGetObj		= '{$html->url("/multimedia/get_item_form")}' ;
var urlGetObjId 	= '{$html->url("/multimedia/get_item_form_by_id")}' ;
var containerItem	= "#{$containerId}";
<!--
{literal}
// set draggable list items
$(document).ready(function(){
	$("div.itemBox").each(function(index) { setup_drag_drop_item(this) ;}) ;
	$('#container-1 > ul').tabs();
});

// Get data from modal window, uploaded files and insert new object in the form
var counter =  0 ;
function {/literal}{$relation}{literal}CommitUploadItem(files, rel) {
	$("#loading").show();
	var emptyDiv = "<div><\/div>"; 
	for(var i=0 ; i < files.length ; i++) {
		var filename = escape(files[i]) ;
		counter++ ;
		$(emptyDiv).load(urlGetObj, {'filename': filename, 'priority':priority, 'index':index,  'relation':rel, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$(containerItem).append(this) ; 
			$(".itemBox", this).each(function() {
				setup_drag_drop_item(this) ;
			}) ;
			counter-- ;
			if(!counter) {
				reorderListItem() ;
				// Show that data changed
				try { $().alertSignal() ; } catch(e) {}
			}
			$("#loading").hide();
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		reorderListItem();
		// Show that data changed
		try { $().alertSignal() ; } catch(e) {}
	}
}

function {/literal}{$relation}{literal}RollbackUploadItem() {
}

// Per gli oggetti gia' registrati
var counter =  0 ;
function {/literal}{$relation}{literal}CommitUploadById(IDs, rel) {
	$("#loading").show();
	var emptyDiv = "<div><\/div>"; 
	for(var i=0 ; i < IDs.length ; i++) {
		var id	= escape(IDs[i]) ;
		counter++ ;
		$(emptyDiv).load(urlGetObjId, {'id': id, 'priority':priority, 'index':index, 'relation':rel, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$(containerItem).append(this) ; 
			$(".itemBox", this).each(function() {
				setup_drag_drop_item(this) ;
			}) ;
			counter-- ;
			if(!counter)  {
				reorderListItem() ;
				// Show that data changed
				try { $().alertSignal() ; } catch(e) {}
			}
			$("#loading").hide();
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		reorderListItem() ;
		// Show that data changed
		try { $().alertSignal() ; } catch(e) {}
	}
}

// Remove item from queue
function removeItem(DivId) {
	$("#"+DivId).remove() ;
	reorderListItem();
}

// Reorder queue list
function reorderListItem() {
	$(".itemBox").each(function (index) {
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[ObjectRelation]["+index+"][id]") ;
		$(".switch", this).attr("name", "data[ObjectRelation]["+index+"][switch]") ;
		$(".priority", this).attr("name", "data[ObjectRelation]["+index+"][priority]") ;
		$(".priority", this).attr("value", index+1) ;
	}) ;
}

function setup_drag_drop_item(el) {
	if(!$(el)) return;
	$(el).Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.8
	});
	$(el).Droppable({
		accept:		'itemBox',
		hoverclass: 'dropOver',
		ondrop:		function(dropped) {
			if(this == dropped) return;
			// swap position of an item (to the position of the previous)
			if(this == $(dropped).prev().get(0)) {
				$(this).insertAfter($(dropped)) ;
				reorderListItem() ;
				return ;
			// swap position of an item (to the position of the next)
			} else if(this == $(dropped).next().get(0)) {
				$(dropped).insertAfter($(this)) ;
				reorderListItem() ;
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
			reorderListItem() ;
		}
	}) ;
}

{/literal}
var priority 	= 1 ;
var index 		= 0 ;
var cols 		= 5 ;
//-->
</script>
<div id="loading" class="loading">{t}Loading data{/t}...</div>
<div id="container-1">
	<ul>
		<li><a href="#fragment-1"><span>{t}{$title} items{/t}</span></a></li>
		<li><a href="#fragment-2"><span>{t}Upload new items{/t}</span></a></li>
		<li><a href="#fragment-3"><span>{t}{$title} items repository{/t}</span></a></li>
	</ul>
	<div id="fragment-1">
		<fieldset id="{$containerId}">
		{assign var="newPriority" 	value=1}
		{assign var="index" 		value=0}
		{foreach item=ob from=$items|default:$empty}
			{include file="../pages/form_file_item.tpl" obj=$ob}
			{math equation="x+y" x=$objIndex y=1 assign=objIndex}
		{foreachelse}
			{t}Empty{/t}.<br />{t}To insert item here, switch to the 'Upload new items' tab or the '{$title} items repository' tab{/t}.
		{/foreach}
		<script type="text/javascript">
		<!--
		index = {$index} ;
		priority = {$newPriority} ;
		//-->
		</script>
		</fieldset>
	</div>
	<div id="fragment-2">
		{if $conf->uploadType == "ajax"}
			{include file="../pages/form_upload_ajax.tpl"}
		{else if $conf->uploadType == "flash"}
			{include file="../pages/form_upload.tpl"}
		{/if}
	</div>
	<div id="fragment-3">
		{include file="../pages/form_multimedia_assoc.tpl" itemType=$relation items=$bedita_items}
	</div>
</div>

</div>