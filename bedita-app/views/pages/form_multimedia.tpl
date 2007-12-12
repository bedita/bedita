{*
params:		
multimedia		elenco oggetti associati ad un oggetto o all'interno di una galleria
*}
{php}
$vs = &$this->get_template_vars() ;
$vs['empty'] = array() ;
{/php}

<script type="text/javascript">
var URLGetObjMultimedia 	= '{$html->url('/multimedia/get_item_form')}' ;
var URLGetObjMultimediaId 	= '{$html->url('/multimedia/get_item_form_by_id')}' ;
<!--
{literal}

/*
funzioni che rende draggable gli item della lista
*/
$(document).ready(function(){
	$("div.imageBox").each(function(index) {
		setupDragDropItem(this) ;
	}) ;
});


/* 
funzione che preleva i dati passati dalla finestra modale
con i file scaricati sul server e inserisce i nuovi oggetti nel form.
*/
var counter =  0 ;
function commitUploadImage(files) {	
	
	for(var i=0 ; i < files.length ; i++) {
		var filename	= escape(files[i]) ;
		counter++ ;
		$("<div></div>").load(URLGetObjMultimedia, {'filename': filename, 'priority':priority, 'index':index, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$("#containerMultimedia").append(this) ; 
			
			$(".imageBox", this).each(function() {
				setupDragDropItem(this) ;				
			}) ;

			counter-- ;
			if(!counter)  {
				reorderListMultimendia() ;
		
				// Indica l'avvenuto cambiamento dei dati
				try { $().alertSignal() ; } catch(e) {}
		
				tb_remove() 
			}
		}) ;
		
		priority++ ;
		index++ ;
	}
	
	if(!counter)  {
		reorderListMultimendia();
		
		// Indica l'avvenuto cambiamento dei dati
		try { $().alertSignal() ; } catch(e) {}
		
		tb_remove() ;	
	}
}

function rollbackUploadImage() {
	tb_remove() ;
}

// Per gli oggetti gia' registrati
var counter =  0 ;
function commitUploadImageById(IDs) {	
	for(var i=0 ; i < IDs.length ; i++) {
		var id	= escape(IDs[i]) ;
		counter++ ;
		$("<div></div>").load(URLGetObjMultimediaId, {'id': id, 'priority':priority, 'index':index, 'cols':cols}, function (responseText, textStatus, XMLHttpRequest) {
			$("#containerMultimedia").append(this) ; 
			
			$(".imageBox", this).each(function() {
				setupDragDropItem(this) ;				
			}) ;
			
			
			counter-- ;
			if(!counter)  {
				reorderListMultimendia() ;
		
				// Indica l'avvenuto cambiamento dei dati
				try { $().alertSignal() ; } catch(e) {}
		
				tb_remove() 
			}
		}) ;
		
		priority++ ;
		index++ ;
	}
	
	if(!counter)  {
		reorderListMultimendia() ;
		
		// Indica l'avvenuto cambiamento dei dati
		try { $().alertSignal() ; } catch(e) {}
		
		tb_remove() ;	
	}
}

// Elimina un elemento dalla lista degli oggetti multimendiali
function removeItemMultimedia(DivId) {
	$("#"+DivId , this).remove() ;
	
	reorderListMultimendia();
}

// Riordina i dati della lista degli oggetti selezionati
function reorderListMultimendia() {
	$(".imageBox").each(function (index) {
		$("input[@name='index']", this).attr("value", index) ;
		$(".id", this).attr("name", "data[multimedia]["+index+"][id]") ;
		$(".priority", this).attr("name", "data[multimedia]["+index+"][priority]") ;
		$(".priority", this).attr("value", index+1) ;
	}) ;
}

function setupDragDropItem(el) {

	$(el).Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.8
	});

	$(el).Droppable({
		accept:		'imageBox',
		hoverclass: 'dropOver',
		ondrop:		function(dropped) {
			if(this == dropped) return;
			
			// sposta un elemento al posto del precedente
			if(this == $(dropped).prev().get(0)) {
				$(this).insertAfter($(dropped)) ;
				
				reorderListMultimendia() ;
				return ;
			
			// sposta un elemento al posto del sucessivo	
			} else if(this == $(dropped).next().get(0)) {
				$(dropped).insertAfter($(this)) ;

				reorderListMultimendia() ;
				return ;
			}
			
			// Se sis posta verso l'inizio si inserisce prima
			var pDropped 	= parseInt($(".priority", dropped).attr("value")) ;
			var pThis 		= parseInt($(".priority", this).attr("value")) ;
			
			if(pDropped > pThis) {
				$(dropped).insertBefore($(this)) ;
			} else {
				$(dropped).insertAfter($(this)) ;
			}

			reorderListMultimendia() ;
		}
	}) ;

}

{/literal}
var priority 	= 1 ;
var index 		= 0 ;
var cols 		= 5 ;
//-->
</script>

<fieldset id="containerMultimedia">
{* NON cambiare la notazione degli URL qui sotto (?....) altrimenti non funzia il plugin che apre la finestra modale !!!!! *}
<a href="{$html->url('/multimedia')}/frm_upload/?keepThis=true&TB_iframe=true&height=480&width=640&modal=true" title="{t}Add multimedia by upload{/t}" class="thickbox">{t}Add multimedia by upload{/t}</a>
|
<a href="{$html->url('/multimedia')}/frm_upload_bedita/?keepThis=true&TB_iframe=true&height=480&width=640&modal=true" title="{t}Add multimedia by BEdita{/t}" class="thickbox">{t}Add multimedia by BEdita{/t}</a>

{* | <a href="{$html->url('/multimedia')}/frm_upload_url/?keepThis=true&TB_iframe=true&height=480&width=640&modal=true" title="{t}Add multimedia by URL{/t}" class="thickbox">{t}Add multimedia by URL{/t}</a>*}

{assign var="newPriority" 	value=1}
{assign var="index" 		value=0}
{foreach key=index item=obj from=$multimedia|default:$empty}
	{include file="../pages/item_form_multimedia.tpl" 
		obj			= $obj
		CACHE		= $CACHE
		MEDIA_ROOT	= $MEDIA_ROOT
		MEDIA_URL	= $MEDIA_URL
		thumbWidth	= 100
		thumbHeight	= 100
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