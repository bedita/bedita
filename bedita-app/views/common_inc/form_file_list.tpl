<script type="text/javascript">
var urlGetObj		= '{$html->url("/streams/get_item_form")}' ;
var urlGetObjId 	= '{$html->url("/streams/get_item_form_by_id")}' ;
var containerItem	= "#{$containerId}";
var urlGetAllItemNoAssoc = "{$html->url("/streams/showStreams")}/{$object.id|default:'0'}/{$collection|default:''}";
<!--
{literal}

$(document).ready(function(){
	
	$("#addRepositoryItems").click( function () {
			$("#loading").show();
			$("#repositoryItems").load(urlGetAllItemNoAssoc, function() {
				$("#loading").hide();
			});
		});

	
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

	// set file language to default object language [for upload file]
	$("#multimedia input[@name=lang]").attr("value", $("#main_lang").val());
	$("#main_lang").bind("change", function() {
		$("#multimedia input[@name=lang]").attr("value", $("#main_lang").val());
	});
	
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
			counter-- ;
			if(!counter) {
				$(containerItem).reorderListItem() ;
			}
			$("#loading").hide();
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		$(containerItem).reorderListItem();
	}
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
			counter-- ;
			if(!counter)  {
				$(containerItem).reorderListItem() ;
			}
			$("#loading").hide();
		}) ;
		priority++ ;
		index++ ;
	}
	if(!counter)  {
		$(containerItem).reorderListItem() ;
	}
}

// Remove item from queue
function removeItem(DivId) {
	$("#"+DivId).remove() ;
	$(containerItem).reorderListItem();
}



{/literal}

var priority 	= 1 ;
var index 		= 0 ;
var cols 		= 5 ;
//-->
</script>

<div class="tab"><h2>{t}{$title}{/t}</h2></div>
{*
		<div class="displaySizeToggle">
			<div id="displayLargeIcons"			class="displayLargeIcons"			title="{t}Large Icons{/t}"><span>&nbsp;</span></div>
			<div id="displaySmallIconsDisabled"	class="displaySmallIconsDisabled"	title="{t}Small Icons{/t}"><span>&nbsp;</span></div>
			<div id="displaySizeToggle"><span>{t}Display size{/t}</span></div>
		</div>
*}	
		
<fieldset id="multimedia">
	
	<input type="hidden" name="data[ObjectRelation][{$relation}][switch]" value="{$relation}" />
	<input type="hidden" name="lang" value=""/>

	<div id="{$containerId}">
		{foreach item=ob from=$items|default:$empty name=multimediaItems}
			{assign var="objIndex" value=$smarty.foreach.multimediaItems.index} 
			{include file="../common_inc/form_file_item.tpl" obj=$ob}
		{foreachelse}
		{/foreach}
	</div>
	
	<hr style="clear: left;" />
	
	<div id="loading"	class="loading"	title="{t}Loading data{/t}">&nbsp;</div>
	
	<ul class="htab" id="multimediaToolbar">

		<li rel="urlItems">{t}Add item{/t}</li>
		<li rel="uploadItems">{t}Upload several images{/t}</li>
		<li rel="repositoryItems" id="addRepositoryItems">{t}Add from Archive{/t}</li>

	</ul>

	
	<div class="htabcontent" id="urlItems">
		{include file="../common_inc/form_media_provider_audiovideo.tpl"}
	</div>
		
	<div class="htabcontent" id="uploadItems">
		{include file="../common_inc/form_upload.tpl"}
	</div>
		
	<div class="htabcontent" id="repositoryItems"></div>


</fieldset>