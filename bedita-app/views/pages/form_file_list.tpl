{* controller = 'attachments' or 'multimedia' *}
<h2 class="showHideBlockButton">{$title}</h2>
<div class="blockForm" id="imgs" style="display:none">
<script type="text/javascript">
var urlGetObj 	= '{$html->url("/$controller/get_item_form")}' ;
var urlGetObjId 	= '{$html->url("/$controller/get_item_form_by_id")}' ;
var containerItem = "#{$containerId}";
<!--
{literal}
// set draggable list items
$(document).ready(function(){
	$("div.itemBox").each(function(index) { setup_drag_drop_item(this) ;}) ;
});

// Get data from modal window, uploaded files and insert new object in the form
var counter =  0 ;
function commitUploadItem(files) {	
	var emptyDiv = "<div><\/div>"; 
	for(var i=0 ; i < files.length ; i++) {
		var filename = escape(files[i]) ;
		counter++ ;
		$(emptyDiv).load(urlGetObj, {'filename': filename, 'priority':priority, 'index':index, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$(containerItem).append(this) ; 
			$(".itemBox", this).each(function() {
				setup_drag_drop_item(this) ;
			}) ;
			counter-- ;
			if(!counter) {
				reorderListItem() ;
				// Show that data changed
				try { $().alertSignal() ; } catch(e) {}
				tb_remove() 
			}
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		reorderListItem();
		// Show that data changed
		try { $().alertSignal() ; } catch(e) {}
		tb_remove() ;	
	}
}

function rollbackUpload() {
	tb_remove() ;
}

// Per gli oggetti gia' registrati
var counter =  0 ;
function commitUploadById(IDs) {
	var emptyDiv = "<div><\/div>"; 
	for(var i=0 ; i < IDs.length ; i++) {
		var id	= escape(IDs[i]) ;
		counter++ ;
		$(emptyDiv).load(urlGetObjId, {'id': id, 'priority':priority, 'index':index, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$(containerItem).append(this) ; 
			$(".itemBox", this).each(function() {
				setup_drag_drop_item(this) ;
			}) ;
			counter-- ;
			if(!counter)  {
				reorderListItem() ;
				// Show that data changed
				try { $().alertSignal() ; } catch(e) {}
				tb_remove() 
			}
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		reorderListItem() ;
		// Show that data changed
		try { $().alertSignal() ; } catch(e) {}
		tb_remove() ;	
	}
}

// Remove item from queue
function removeItem(DivId) {
	alert('delete ' + DivId);
	$("#"+DivId , this).remove() ;
	reorderListItem();
}

// Reorder queue list
function reorderListItem() {
	$(".itemBox").each(function (index) {
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[{/literal}{$controller}{literal}]["+index+"][id]") ;
		$(".priority", this).attr("name", "data[{/literal}{$controller}{literal}]["+index+"][priority]") ;
		$(".priority", this).attr("value", index+1) ;
	}) ;
}

function setup_drag_drop_item(el) {
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

<fieldset id="{$containerId}">
<a href="{$html->url("/$controller")}/frm_upload/?keepThis=true&amp;TB_iframe=true&amp;height=480&amp;width=640&amp;modal=true" title="{$title} -{t}add by upload{/t}" class="thickbox">{$title} - {t}add by upload{/t}</a> |
<a href="{$html->url("/$controller")}/frm_upload_bedita/?keepThis=true&amp;TB_iframe=true&amp;height=480&amp;width=640&amp;modal=true" title="{$title} - {t}add by BEdita{/t}" class="thickbox">{$title} - {t}add by BEdita{/t}</a>
{* | <a href="{$html->url("/$controller")}/frm_upload_url/?keepThis=true&amp;TB_iframe=true&amp;height=480&amp;width=640&amp;modal=true" title="{$title} - {t}add by URL{/t}" class="thickbox">{$title} - {t}add by URL{/t}</a>*}

{assign var="newPriority" 	value=1}
{assign var="index" 		value=0}
{foreach key=index item=ob from=$items|default:$empty}
	{include file="../pages/form_file_item.tpl" obj=$ob}
{/foreach}
<script type="text/javascript">
<!--
index = {$index} ;
priority = {$newPriority} ;
//-->
</script>
</fieldset>
</div>