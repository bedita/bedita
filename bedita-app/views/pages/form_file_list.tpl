<script type="text/javascript">
var urlGetObj		= '{$html->url("/streams/get_item_form")}' ;
var urlGetObjId 	= '{$html->url("/streams/get_item_form_by_id")}' ;
var containerItem	= "#{$containerId}";
var urlGetAllItemNoAssoc = "{$html->url("/streams/showStreams")}/{$object.id|default:'0'}/{$collection|default:''}";
<!--
{literal}
// set draggable list items
$(document).ready(function(){
	$(containerItem + " div.itemBox").each(function(index) { setup_drag_drop_item(this) ;}) ;

	$("#addItemsContainerClose").click( function () {
		$('#addItemsContainer').hide();
	});

	$("#addRepositoryItems").click( function () {
			$("#loading").show();
			$("#addItemsContainer").hide();
			$("#staticSubcontainer-1").hide();
			$("#staticSubcontainer-2").hide();
			$("#ajaxSubcontainer").show();
			$("#ajaxSubcontainer").load(urlGetAllItemNoAssoc, function() {
				$("#loading").hide();
				$("#addItemsContainer").show();
				});
		});
	
	$("#addItem").click ( function() {
			$("#addItemsContainer").hide();
			$("#ajaxSubcontainer").hide();
			$("#staticSubcontainer-2").hide();
			$("#staticSubcontainer-1").show();
			$("#addItemsContainer").show();
		});
	
	$("#addMultipleItems").click ( function() {
			$("#addItemsContainer").hide();
			$("#ajaxSubcontainer").hide();
			$("#staticSubcontainer-1").hide();
			$("#staticSubcontainer-2").show();
			$("#addItemsContainer").show();
		});
	
	$("input").bind('click', function () { this.focus(); });
	$("textarea").bind('click', function () { this.focus(); });

	// toggle small/large icons views
	$('.displaySizeToggle').toggle (
		function () {
			$('.itemBox').addClass('itemBoxSmall');
			$('.itemFooter').hide();
			$('.itemInfo').hide();
			$('.itemInfoSmall').show();
			$('div#displaySmallIconsDisabled').removeClass('displaySmallIconsDisabled').addClass('displaySmallIcons');
			$('div#displayLargeIcons').removeClass('displayLargeIcons').addClass('displayLargeIconsDisabled');
			$('#displaySizeToggle').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(200);
		}, function () {
			$('.itemBox').removeClass ('itemBoxSmall');
			$('.itemInfoSmall').hide();
			$('.itemInfo').show();
			$('.itemFooter').show();
			$('div#displaySmallIconsDisabled').removeClass ('displaySmallIcons').addClass ('displaySmallIconsDisabled');
			$('div#displayLargeIcons').removeClass ('displayLargeIconsDisabled').addClass ('displayLargeIcons');
			$('#displaySizeToggle').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(200);
		}
	);
	
	
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
				$(containerItem).reorderListItem() ;
				// Show that data changed
				try { $().alertSignal() ; } catch(e) {}
			}
			$("#loading").hide();
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		$(containerItem).reorderListItem();
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
				$(containerItem).reorderListItem() ;
				// Show that data changed
				try { $().alertSignal() ; } catch(e) {}
			}
			$("#loading").hide();
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		$(containerItem).reorderListItem() ;
		// Show that data changed
		try { $().alertSignal() ; } catch(e) {}
	}
}

// Remove item from queue
function removeItem(DivId) {
	$("#"+DivId).remove() ;
	$(containerItem).reorderListItem();
}

function setup_drag_drop_item(el) {
	if(!$(el)) return;
	$(el).Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.7,
		containment : '#mutimediacontainer'
	});
	$(el).Droppable({
		accept:		'itemBox',
		hoverclass: 'dropOver',
		ondrop:		function(dropped) {
			if(this == dropped) return;
			// swap position of an item (to the position of the previous)
			if(this == $(dropped).prev().get(0)) {
				$(this).insertAfter($(dropped)) ;
				$(containerItem).reorderListItem() ;
				return ;
			// swap position of an item (to the position of the next)
			} else if(this == $(dropped).next().get(0)) {
				$(dropped).insertAfter($(this)) ;
				$(containerItem).reorderListItem() ;
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
			$(containerItem).reorderListItem() ;
		}
	}) ;
}

{/literal}

var priority 	= 1 ;
var index 		= 0 ;
var cols 		= 5 ;
//-->
</script>

<h2 class="showHideBlockButton">{t}{$title}{/t}</h2>
<div class="blockForm" id="multimedia" style="display:none">
	<input type="hidden" name="data[ObjectRelation][{$relation}][switch]" value="{$relation}" />
	<div class="multimediaToolbar">
		<div class="displaySizeToggle">
			<div id="displayLargeIcons"			class="displayLargeIcons"			title="{t}Large Icons{/t}"><span>&nbsp;</span></div>
			<div id="displaySmallIconsDisabled"	class="displaySmallIconsDisabled"	title="{t}Small Icons{/t}"><span>&nbsp;</span></div>
			<div id="displaySizeToggle"><span>{t}Display size{/t}</span></div>
		</div>
		<div> | </div>
		<div id="addItem"					class="addItem"						title="{t}Add Item{/t}"><span>{t}Add item{/t}</span></div>
		<div id="addMultipleItems"			class="addMultipleItems"			title="{t}Upload several images{/t}"><span>{t}Upload several images{/t}</span></div>
		<div id="addRepositoryItems"		class="addRepositoryItems"			title="{t}Add items from Repository{/t}"><span>{t}Add from Archive{/t}</span></div>
		<div id="loading"					class="loading"						title="{t}Loading data{/t}"><span>&nbsp;</span></div>
	</div>


	<div id="{$containerId}"> {* mutimediacontainer *}
		{* DA ELIMINARE assign var="newPriority" 	value=1}
		{assign var="index" 		value=0 *}
		{foreach item=ob from=$items|default:$empty name=multimediaItems}
			{assign var="objIndex" value=$smarty.foreach.multimediaItems.index} 
			{include file="../pages/form_file_item.tpl" obj=$ob}
			
			{* DA ELIMINARE? (ho messo l'assign objindex sopra) math equation="x+y" x=$objIndex y=1 assign=objIndex*}

		{foreachelse}
		{/foreach}
		{* DA ELIMINARE  ma a che serve sta roba?
		<script type="text/javascript">
		<!--
		index = {$index} ;
		priority = {$newPriority} ;
		//-->
		</script>
		*}
	</div>
	<div style="clear: left;"></div>

	<div id="addItemsContainer" style="position: relative; margin: 8px 0; padding: 4px; background-color: #dddddd; border: 1px solid #666; display: none;">
		<div style="position: absolute; top: 5px; right: 5px; z-index:9999; cursor: pointer;" id="addItemsContainerClose">close</div>
		<div id="ajaxSubcontainer"></div>
		<div id="staticSubcontainer-1" style="display: none;">
			{*include file="../pages/form_upload_ajax.tpl"*}
			{include file="../pages/form_media_provider_audiovideo.tpl"}
		</div>
		<div id="staticSubcontainer-2" style="display: none;">{include file="../pages/form_upload.tpl"}</div>
	</div>

</div>