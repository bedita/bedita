<script type="text/javascript">

var urlGetObj = '{$html->url("/streams/get_item_form_by_id")}' ;
var urlGetAllItemNoAssoc = '{$html->url("/streams/showStreams")}/{$object.id|default:'0'}';
var containerItem = "#multimediaItems";

function commitUploadItem(IDs) {

	var currClass =  $(".multimediaitem:last").prop("class");
	//alert(currClass);
	
	for(var i=0 ; i < IDs.length ; i++)
	{
		var id = escape(IDs[i]) ;
		var emptyDiv = "<div id='item_" + id + "' class=' " + currClass + " gold '><\/div>";
		$(emptyDiv).load(
			urlGetObj, { 'id': id, 'relation':"attach" }, function (responseText, textStatus, XMLHttpRequest)
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
		//$("#addmultimedia").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
		showMultimediaAjaxError(null, data.UploadErrorMsg, null);
	} else {
		var tmp = new Array() ;
		var countFile = 0; 
		$.each(data, function(entryIndex, entry) {
			tmp[countFile++] = entry['fileId'];
		});

		commitUploadItem(tmp);
	}

		$("#addmultimedia").find("input[type=text]").val("");
		$("#addmultimedia").find("input[type=file]").val("");
		$("#addmultimedia").find("textarea").val("");
}

function showMultimediaAjaxError(XMLHttpRequest, textStatus, errorThrown) {
	var submitUrl = "{$html->url('/pages/showAjaxMessage/')}";
	var errorMsg = textStatus;
	if (XMLHttpRequest != null && XMLHttpRequest.responseText) {
		errorMsg += "<br/><br/> " + XMLHttpRequest.responseText;
	}
	$("#messagesDiv").load(submitUrl,{ "msg":errorMsg,"type":"error" }, function() {
		$("#loading").hide();
	});
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
		dataType:		'json',        // 'xml', 'script', or 'json' (expected server response type)
		error: showMultimediaAjaxError
	};

	$("#uploadForm").click(function() {
		optionsForm.url = "{$html->url('/files/uploadAjax')}"; // override form action
		$('#updateForm').ajaxSubmit(optionsForm);
		return false;
	});

	$("#uploadFormMedia").click(function() {
		optionsForm.url = "{$html->url('/files/uploadAjaxMediaProvider')}"; // override form action
		$('#updateForm').ajaxSubmit(optionsForm);
		return false;
	});
});
</script>

<div class="tab"><h2>{t}{$tabTitle|default:"Multimedia items"}{/t}</h2></div>	

<div id="multimedia">
	
<fieldset id="multimediaItems" style="margin-left:10px">	

<img class="multimediaitemToolbar viewsmall" src="{$html->webroot}img/iconML-small.png" />
<img class="multimediaitemToolbar viewthumb" src="{$html->webroot}img/iconML-thumb.png" />

<hr />
<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][{$relation}][0][switch]" value="{$relation}" />

{foreach from=$relObjects[$relation]|default:[] item="item"}
	<div class="multimediaitem itemBox {if $item.status != "on"} off{/if} XdisableSelection" id="item_{$item.id}">
			{$view->element('form_file_item', ['item' => $item, 'relation' => $relation])}
	</div>
{/foreach}

</fieldset>


<fieldset id="addmultimedia">	

<div id="loading" style="clear:both" class="multimediaitem itemBox small">&nbsp;</div>

	<table class="htab">
	<tr>
		<td class="on" rel="uploadItems">{t}upload new items{/t}</td>
		{if empty($disableRemote)}
		<td rel="urlItems">{t}add by url{/t}</td>
		{/if}
		<td rel="repositoryItems" id="reposItems">{t}select from archive{/t}</td>
	</tr>
	</table>
	
<div class="htabcontainer" id="addmultimediacontents">

	<div class="htabcontent" id="uploadItems">
		{$view->element('form_upload_multi')}
	</div>

    {if empty($disableRemote)}
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
    {/if}

	<div class="htabcontent" id="repositoryItems">
		<div id="ajaxSubcontainer"></div>
	</div>

</div>

</fieldset>

</div>




