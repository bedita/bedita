<script type="text/javascript">
var urlGetObj		= '{$html->url("/streams/get_item_form_by_id")}' ;
var containerItem = "#multimediaItems";

{literal}
function commitUploadItem(IDs, rel) {
	//$("#loading").show();
	var emptyDiv = "<div  class=\"multimediaitem itemBox\"><\/div>";
	for(var i=0 ; i < IDs.length ; i++) {
		var id = escape(IDs[i]) ;
		$(emptyDiv).load(urlGetObj, {'id': id, 'relation':rel}, function (responseText, textStatus, XMLHttpRequest) {
			$(containerItem).append(this).reorderListItem() ; 
			//$("#loading").hide();
		}) ;
	}	
}


function showResponse(data) {
	$("#loading").hide();

	if (data.UploadErrorMsg) {
    	$("#addmultimedia").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
    } else {
	    var tmp = new Array() ;
	    var countFile = 0; 
	    $.each(data, function(entryIndex, entry) {
	    	tmp[countFile++] = entry['fileId'];
	    });
	    
	    $("#addmultimedia").find("input[@type=text]").attr("value", "");
	    $("#addmultimedia").find("input[@type=file]").attr("value", "");
	    $("#addmultimedia").find("textarea").attr("value", "");
	    
		commitUploadItem(tmp, "attach");
	}
}

function resetError() {
	$("#addmultimedia").find("label").remove();
	//$("#loading").show();
}

$(document).ready(function() {  
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
	 
});
{/literal}
</script>


<div class="tab"><h2>{t}Multimedia items{/t}</h2></div>	
<fieldset id="multimediaItems">	

<img class="multimediaitemToolbar" src="/img/px.gif" />

<hr />
	
{foreach from=$attach item="item"}
	<div class="multimediaitem itemBox{if $obj.status == "off"} off{/if}">
	{include file="../common_inc/form_file_item.tpl"}
	</div>
{/foreach}

</fieldset>


<div class="tab"><h2>{t}Add multimedia items{/t}</h2></div>

<fieldset id="addmultimedia">
	
	<ul class="htab">
		<li rel="uploadItems">	{t}upload new items{/t}</li>
		<li rel="urlItems">		{t}add by url{/t}</li>
		<li rel="repositoryItems">	{t}select from archive{/t}</li>
	</ul>
	
	
	<div class="htabcontent" id="uploadItems">
		{*
		<table class="bordered" style="width:100%; margin-bottom:20px;">
			<th colspan="4" id="queueinfo">uploading file <span class="evidence">2</span> of 3... </th>
			<tr id="7441_0">
				<td>immag288.jpg</td> 
				<td style="white-space:nowrap"><div class="progressBar" id="7441_0progress">&nbsp;</div>100% ready</td>
				<td><a id="7441_0deletebtn" class="cancelbtn" href='javascript:swfu.cancelFile("7441_0");'>cancel</a></td>
			</td>
			<tr id="7441_0">
				<td>aadadimmagsds288.jpg</td> 
				<td nowrap><div class="progressBar" style="width:25px" id="7441_0progress">&nbsp;</div>25% loading</td>
				<td><a id="7441_0deletebtn" class="cancelbtn" href='javascript:swfu.cancelFile("7441_0");'>cancel</a></td>
			</td>
			<tr id="7441_0">
				<td>qwewfdghjk_900_addd[2].png</td> 
				<td nowrap><div class="progressBar" style="width:1px" id="7441_0progress">&nbsp;</div>0% waiting</td>
				<td><a id="7441_0deletebtn" class="cancelbtn" href='javascript:swfu.cancelFile("7441_0");'>cancel</a></td>
			</td>
		</table>
		<input type="button" class="swfuploadbtn browsebtn" id="SWFUpload_0BrowseBtn" value="browse your hard disk again"  />
		*}
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
		<table>
		<tr>
			<td>{t}Url{/t}:</td>
			<td><input type="text" name="uploadByUrl[url]" size="40"/></td>
		</tr>
		<tr>
			<td>{t}Title{/t}:</td>
			<td><input type="text" name="uploadByUrl[title]" /></td>
		</tr>
		<tr>
			<td>{t}Description{/t}:</td>
			<td><textarea name="uploadByUrl[description]"></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="button" id="uploadFormMedia" value="{t}Add{/t}"/></td>
		</tr>
		</table>
	</div>
	
	
	<div class="htabcontent" id="repositoryItems">
		Lla awfwe wetrewt ert 
	</div>

</fieldset>