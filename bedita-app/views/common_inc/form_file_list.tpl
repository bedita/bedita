<script type="text/javascript">
var urlGetObj		= '{$html->url("/streams/get_item_form_by_id")}' ;
var containerItem = "#multimediaItems";


{literal}
function commitUploadItem(IDs, rel) {

	var currClass =  $(".multimediaitem:last").attr("class");
	//alert(currClass);
	var emptyDiv = "<div  class=\' " + currClass + " \ gold '><\/div>";
	for(var i=0 ; i < IDs.length ; i++) {
		var id = escape(IDs[i]) ;
		$(emptyDiv).load(urlGetObj, {'id': id, 'relation':rel}, function (responseText, textStatus, XMLHttpRequest) {
			$("#loading").hide();
			$(containerItem).append(this).reorderListItem(); 
		})
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
 
		commitUploadItem(tmp, "attach");
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
	$("#multimediaItems").reorderListItem();
}



// JQuery stuff
$(document).ready(function()
{  
	var optionsForm = {
		beforeSubmit:	resetError,
        success:    	showResponse,  // post-submit callback  
        dataType:  		'json'        // 'xml', 'script', or 'json' (expected server response type) 
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
	
	$("#multimediaItems").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).reorderListItem
	}).css("cursor","move");
});
{/literal}
</script>


<div class="tab"><h2>{t}Multimedia items{/t}</h2></div>	

<div id="multimedia">
	
<fieldset id="multimediaItems">	

				<img class="multimediaitemToolbar viewsmall" src="{$html->webroot}img/iconML-small.png" />
				<img class="multimediaitemToolbar viewthumb" src="{$html->webroot}img/iconML-thumb.png" />

<hr />

{foreach from=$attach item="item"}
	<div class="multimediaitem itemBox{if $item.status == "off"} off{/if} small" id="item_{$item.id}">
		
			{include file="../common_inc/form_file_item.tpl"}
			
	</div>
{/foreach}

	


</fieldset>


<fieldset id="addmultimedia">	

<div id="loading" style="clear:both" class="multimediaitem itemBox small">&nbsp;</div>

	<ul class="htab">
		<li rel="uploadItems">	{t}upload new items{/t}</li>
		<li rel="urlItems">		{t}add by url{/t}</li>
		<li rel="repositoryItems">	{t}select from archive{/t}</li>
	</ul>
	
	
	<div class="htabcontent" id="uploadItems">
		{include file="../common_inc/form_upload_ajax.tpl"}
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
			<td>{t}Url{/t}:</td>
			<td><input type="text" style="width:270px;" name="uploadByUrl[url]" /></td>
		</tr>
		<tr>
			<td>{t}Title{/t}:</td>
			<td><input type="text" style="width:270px;" name="uploadByUrl[title]" /></td>
		</tr>
		<tr>
			<td>{t}Description{/t}:</td>
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
		Lla awfwe wetrewt ert 
	</div>

</fieldset>

</div>




