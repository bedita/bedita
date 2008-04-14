<script type="text/javascript">
{literal}

function commitFileUpload(tmp) {
	{/literal}{$relation}CommitUploadItem(tmp, '{$relation}'){literal} ;
}

function showResponse(data) {
	$("#loading").hide();
	if (data.UploadErrorMsg) {
    	$("#msgUpload").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
    } else {
	    var tmp = new Array() ;
	    var countFile = 0; 
	    $.each(data, function(entryIndex, entry) {
	    	tmp[countFile++] = entry['filename'];
	    });
	    // clear input form
	    $("#uploadMediaProvider").find("input[@type=text]").attr("value", "");
	    $("#uploadMediaProvider").find("input[@type=file]").attr("value", "");
	    $("#uploadMediaProvider").find("textarea").attr("value", "");
		commitFileUpload(tmp);
	}
}

function resetError() {
	$("#msgUpload").empty();
	$("#loading").show();
}

function showResponseMedia(data) {
	$("#loading").hide();
	if (data.UploadErrorMsg) {
    	$("#msgUpload").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
    } else {
    	if(data['filename'] == undefined) return ;
	
    	var tmp = new Array(data['filename']) ;
    	commitFileUpload(tmp);
    	
    	$("#uploadMediaProvider input[@type='text']").attr("value", "") ;
    }
}

$(document).ready(function() {  
	var optionsForm = {
		beforeSubmit:	resetError,
        success:    	showResponse,  // post-submit callback 
        url:       		"{/literal}{$html->url('/files/uploadAjax')}{literal}",       // override for form's 'action' attribute 
        dataType:  		'json'        // 'xml', 'script', or 'json' (expected server response type) 
    }; 

    $("#uploadForm").click( function() {
    	$('#updateForm').ajaxSubmit(optionsForm);
    	return false;
    }); 


	var optionsFormMedia = {
		beforeSubmit:		resetError,
       		 success:    	showResponseMedia,  // post-submit callback 
        		url:       	"{/literal}{$html->url('/files/uploadAjaxMediaProvider')}{literal}",       // override for form's 'action' attribute 
        		dataType:  	'json'        // 'xml', 'script', or 'json' (expected server response type) 
    }; 

    $("#uploadFormMedia").click( function() {
    	$('#updateForm').ajaxSubmit(optionsFormMedia);
    	return false;
    });
	
	/* image/video/audio switch - xho */
	$("input[@name='itemType']").change(function ()
	{
		if ( $("input[@value='image']").is(":checked") )
		{
			$("#addAudioForm").hide();
			$("#addVideoForm").hide();
			$("#addImageForm").show();
		}
		else if ( $("input[@value='video']").is(":checked") )
		{
			$("#addAudioForm").hide();
			$("#addImageForm").hide();
			$("#addVideoForm").show();
		}
		else if ( $("input[@value='audio']").is(":checked") )
		{
			$("#addVideoForm").hide();
			$("#addImageForm").show();
			$("#addAudioForm").hide();
		}
	})
});
{/literal}
</script>
<div id="uploadMediaProvider">
	<div style="float: left; width: 140px;"><span class="label">Item Type</span>
	<ul class="noBulletList">
		<li><input type="radio" name="itemType" id="itemTypeImage" value="image" checked=> <label for="itemTypeImage">{t}image{/t}</label></li>
		<li><input type="radio" name="itemType" id="itemTypeVideo" value="video"> <label for="itemTypeVideo">{t}video{/t}</label></li>
		<li><input type="radio" name="itemType" id="itemTypeAudio" value="audio"> <label for="itemTypeAudio">{t}audio{/t}</label></li>
	</ul>
	</div>

	<div id="addImageForm">
	<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td><strong>{t}Titolo{/t}</strong></td>
			<td style="padding-left:20px;"><strong>{t}Description{/t}</strong></td>
		</tr>
		<tr>
			<td><input type="text" name="streamUploaded[title]" class="formtitolo" value=""></td>
			<td style="padding-left:20px;" rowspan="4">
				<textarea name="streamUploaded[description]" style="width:280px; height:90px;"></textarea>
			</td>
		</tr>
		<tr>
			<td><strong>file:</strong></td>
		</tr>
		<tr>
			<td>
				<input type="file" name="Filedata" />
				<input type="button" id="uploadForm" value="{t}Upload{/t}"/>
			</td>
		</tr>
	</table>
	</div>

	<div id="addVideoForm" style="display: none;">
		<table class="tableForm" border="0">
		<tr>
			<td class="label">{t}Title{/t}:</td>
			<td class="field"><input type="text" name="title" /></td>
			<td class="status">&nbsp;</td>
		</tr>
		<tr>
			<td class="label">{t}Url{/t}:</td>
			<td class="field"><input type="text" name="url" /></td>
			<td class="status">&nbsp;</td>
		</tr>
		<tr>
			<td class="label"></td>
			<td class="field"><input type="button" id="uploadFormMedia" value="{t}Create{/t}"/></td>
			<td class="status">&nbsp;</td>
		</tr>
		</table>
	</div>

	<div id="addAudioForm" style="display: none;">
		empty
	</div>
	<div style="clear: left;"></div>

	<div id="msgUpload"></div>
</div>