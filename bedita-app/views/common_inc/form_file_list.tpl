{$javascript->link("jquery/jquery.disable.text.select", true)}

{literal}
<script type="text/javascript">
	
    $(function() {
        $('.disableSelection').disableTextSelect();
    });	
{/literal}
	
var urlGetObj		= '{$html->url("/streams/get_item_form_by_id")}' ;
var urlGetAllItemNoAssoc = '{$html->url("/streams/showStreams")}/{$object.id|default:'0'}';
var containerItem = "#multimediaItems";

{literal}
function commitUploadItem(IDs) {

	var currClass =  $(".multimediaitem:last").attr("class");
	//alert(currClass);
	var emptyDiv = "<div  class=\' " + currClass + " \ gold '><\/div>";
	for(var i=0 ; i < IDs.length ; i++)
	{
		var id = escape(IDs[i]) ;

		$(emptyDiv).load(
			urlGetObj, {'id': id, 'relation':"attach"}, function (responseText, textStatus, XMLHttpRequest)
			{
				$("#loading").hide();
				$(containerItem).append(this).fixItemsPriority(); 
				$(containerItem).sortable("refresh");
			}
		)
	}	
}


function showResponse(data) {

	if (data.UploadErrorMsg) {
		$("#loading").hide();
		$("#addmultimedia").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
	} else {
		var tmp = new Array() ;
		var countFile = 0; 
		$.each(data, function(entryIndex, entry) {
			tmp[countFile++] = entry['fileId'];
		});

		commitUploadItem(tmp);
	}

		$("#addmultimedia").find("input[@type=text]").attr("value", "");
		$("#addmultimedia").find("input[@type=file]").attr("value", "");
		$("#addmultimedia").find("textarea").attr("value", "");
}

function resetError() {
	$("#addmultimedia").find("label").remove();
	$("#loading").show();
}

// Remove item from queue
function removeItem(divId) {
	$("#" + divId).remove() ;
	$("#multimediaItems").fixItemsPriority();
}



// JQuery stuff
$(document).ready(function()
{  
	var optionsForm = {
		beforeSubmit:	resetError,
		success:		showResponse,  // post-submit callback  
		dataType:		'json'        // 'xml', 'script', or 'json' (expected server response type) 
	};

	$("#uploadForm").click(function() {
		optionsForm.url = "{/literal}{$html->url('/files/uploadAjax')}{literal}"; // override form action
		$('#updateForm').ajaxSubmit(optionsForm);
		return false;
	});

	$("#uploadFormMedia").click(function() {
		optionsForm.url = "{/literal}{$html->url('/files/uploadAjaxMediaProvider')}{literal}"; // override form action
		$('#updateForm').ajaxSubmit(optionsForm);
		return false;
	});

	$(containerItem).sortable ({
		distance: 20,
		opacity:0.7,
		//handle: $(".multimediaitem").add(".multimediaitem img"), //try to fix IE7 handle on images, but don't work acc!
		update: $(this).fixItemsPriority
	}).css("cursor","move");

	$("#reposItems").click( function () {
		$("#loading").show();
		$("#ajaxSubcontainer").show();
		$("#ajaxSubcontainer").load(urlGetAllItemNoAssoc, function() {
			$("#loading").hide();
		});
	});
});
{/literal}
</script>


<div class="tab"><h2>{t}Multimedia items{/t}</h2></div>	

<div id="multimedia">
	
<fieldset id="multimediaItems" style="margin-left:10px">	

<img class="multimediaitemToolbar viewsmall" src="{$html->webroot}img/iconML-small.png" />
<img class="multimediaitemToolbar viewthumb" src="{$html->webroot}img/iconML-thumb.png" />

<hr />
<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][{$relation}][0][switch]" value="{$relation}" />


{foreach from=$attach item="item"}
	<div class="multimediaitem itemBox {if $item.status != "on"} off{/if} disableSelection" id="item_{$item.id}">
		
			{include file="../common_inc/form_file_item.tpl"}
			
	</div>
{/foreach}


</fieldset>


<fieldset id="addmultimedia">	

<div id="loading" style="clear:both" class="multimediaitem itemBox small">&nbsp;</div>

	<table class="htab">
		<td rel="uploadItems">{t}upload new items{/t}</td>
		<td rel="urlItems">{t}add by url{/t}</td>
		<td rel="repositoryItems" id="reposItems">{t}select from archive{/t}</td>
	</table>
	
<div class="htabcontainer" id="addmultimediacontents">

	<div class="htabcontent" id="uploadItems">
		{include file="../common_inc/form_upload_multi.tpl"}
	</div>

	
	<div class="htabcontent" id="urlItems">
		{*<table>
			<tr>
				<th>direct url / feed / podcast</th>
			</tr>
			<tr>
				<td><input style="width:270px" name="url" type="text" /></td>
				<td><b>OK</b></td><td>video/bliptv</td>
				<td>cancel</td>
			</tr>
			<tr>
				<td><input style="width:270px"  name="url" type="text" /></td>
				<td><b>OK</b></td><td>video/youtube</td>
				<td>cancel</td>
			</tr>
			<tr>
				<td><input style="width:270px"  name="url" type="text" /></td>
				<td><b>ERR</b></td><td>feed/podcast</td>
				<td></td>
			</tr>
			<tr>
				<td><input style="width:270px"  name="url" type="text" /></td>
				<td><strong></strong></td><td></td>
				<td></td>
			</tr>
		</table>
		*}
		<table style="margin-bottom:20px">
		<tr>
			<td>{t}url{/t}:</td>
			<td><input type="text" style="width:270px;" name="uploadByUrl[url]" /></td>
		</tr>
		<tr>
			<td>{t}title{/t}:</td>
			<td><input type="text" style="width:270px;" name="uploadByUrl[title]" /></td>
		</tr>
		<tr>
			<td>{t}description{/t}:</td>
			<td><textarea style="width:270px; min-height:16px; height:16px;" class="autogrowarea" name="uploadByUrl[description]"></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="button" style="width:160px; margin-top:15px" id="uploadFormMedia" value="{t}Add{/t}"/>
			</td>
		</tr>
		</table>
	</div>


	<div class="htabcontent" id="repositoryItems">
		<div id="ajaxSubcontainer"></div>
	</div>

</div>

</fieldset>

</div>




