<h2 class="showHideBlockButton">{t}Attachments{/t}</h2>
<div class="blockForm" id="attachments" style="display:none">

<script type="text/javascript">
var URLGetObjAttachment 	= '{$html->url('/attachments/get_item_form')}' ;
var URLGetObjAttachmentId 	= '{$html->url('/attachments/get_item_form_by_id')}' ;
<!--
{literal}

/*
funzioni che rende draggable gli item della lista
*/
$(document).ready(function(){
	$("div.attachBox").each(function(index) {
		setupDragDropItemAttach(this) ;
	}) ;
});


/* 
funzione che preleva i dati passati dalla finestra modale
con i file scaricati sul server e inserisce i nuovi oggetti nel form.
*/
var counter =  0 ;
function commitUploadAttachment(files) {	
	var emptyDiv = "<div><\/div>";	
	for(var i=0 ; i < files.length ; i++) {
		var filename	= escape(files[i]) ;
		counter++ ;
		$(emptyDiv).load(URLGetObjAttachment, {'filename': filename, 'priority':priority, 'index':index, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$("#containerAttachment").append(this) ; 
			$(".attachBox", this).each(function() {
				setupDragDropItemAttach(this) ;
			}) ;
			counter-- ;
			if(!counter)  {
				reorderListAttachments() ;
				// Indica l'avvenuto cambiamento dei dati
				try { $().alertSignal() ; } catch(e) {}
				tb_remove() 
			}
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		reorderListAttachments();
		// Show that data changed
		try { $().alertSignal() ; } catch(e) {}
		tb_remove() ;
	}
}

function rollbackUploadAttachment() {
	tb_remove() ;
}

// Per gli oggetti gia' registrati
var counter =  0 ;
function commitUploadAttachById(IDs) {
	var emptyDiv = "<div><\/div>";
	for(var i=0 ; i < IDs.length ; i++) {
		var id	= escape(IDs[i]) ;
		counter++ ;
		$(emptyDiv).load(URLGetObjAttachmentId, {'id': id, 'priority':priority, 'index':index, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$("#containerAttachment").append(this) ; 
			$(".attachBox", this).each(function() {
				setupDragDropItemAttach(this) ;
			}) ;
			counter-- ;
			if(!counter)  {
				reorderListAttachments() ;
				// Show that data changed
				try { $().alertSignal() ; } catch(e) {}
				tb_remove() 
			}
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		reorderListAttachments() ;
		// Show that data changed
		try { $().alertSignal() ; } catch(e) {}
		tb_remove() ;
	}
}

// Remove item from queue
function removeItemAttachment(DivId) {
	$("#"+DivId , this).remove() ;
	reorderListAttachments();
}

// Reorder queue items
function reorderListAttachments() {
	$(".attachBox").each(function (index) {
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[attachments]["+index+"][id]") ;
		$(".priority", this).attr("name", "data[attachments]["+index+"][priority]") ;
		$(".priority", this).attr("value", index+1) ;
	}) ;
}

function setupDragDropItemAttach(el) {
	$(el).Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.8
	});
	$(el).Droppable({
		accept:		'attachBox',
		hoverclass: 'dropOver',
		ondrop:		function(dropped) {
			if(this == dropped) return;
			// swap position of an item (to the position of the previous)
			if(this == $(dropped).prev().get(0)) {
				$(this).insertAfter($(dropped)) ;
				reorderListAttachments() ;
				return ;
			// swap position of an item (to the position of the next)
			} else if(this == $(dropped).next().get(0)) {
				$(dropped).insertAfter($(this)) ;
				reorderListAttachments() ;
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
			reorderListAttachments() ;
		}
	}) ;

}

{/literal}
var priority 	= 1 ;
var index 		= 0 ;
var cols 		= 5 ;
//-->
</script>

<fieldset id="containerAttachment">
{* NON cambiare la notazione degli URL qui sotto (?....) altrimenti non funzia il plugin che apre la finestra modale !!!!! *}
<a href="{$html->url('/attachments')}/frm_upload/?keepThis=true&amp;TB_iframe=true&amp;height=480&amp;width=640&amp;modal=true" title="{t}Add attachments by upload{/t}" class="thickbox">{t}Add attachments by upload{/t}</a>
|
<a href="{$html->url('/attachments')}/frm_upload_bedita/?keepThis=true&amp;TB_iframe=true&amp;height=480&amp;width=640&amp;modal=true" title="{t}Add attachments by BEdita{/t}" class="thickbox">{t}Add attachments by BEdita{/t}</a>

{* | <a href="{$html->url('/attachments')}/frm_upload_url/?keepThis=true&amp;TB_iframe=true&amp;height=480&amp;width=640&amp;modal=true" title="{t}Add attachments by URL{/t}" class="thickbox">{t}Add attachments by URL{/t}</a>*}
{assign var="newPriority" 	value=1}
{assign var="index" 		value=0}
{foreach key=index item=obj from=$attachments|default:$empty}
	{include file="../pages/item_form_attachment.tpl" 
		obj			= $obj
		CACHE		= $CACHE
		MEDIA_ROOT	= $MEDIA_ROOT
		MEDIA_URL	= $MEDIA_URL
		index		= $index
		priority	= $obj.priority
		cols		= 5
	}
{/foreach}
<script type="text/javascript">
<!--
index 		= {$index} ;
priority 	= {$newPriority} ;
//-->
</script>

</fieldset>
</div>