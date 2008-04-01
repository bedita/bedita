<script type="text/javascript">
{literal}

function commitFileUploadMedia(tmp) {
	{/literal}{$relation}CommitUploadItem(tmp, '{$relation}'){literal} ;
}

function showResponseMedia(data) {
	$("#loading").hide();
	if (data.UploadErrorMsg) {
    	$("#msgUpload").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
    } else {
	    var tmp = new Array() ;
	    var countFile = 0; 
	    $.each(data, function(entryIndex, entry) {
	    	tmp[countFile++] = entry['filename'];
	    });
		commitFileUpload(tmp);
	}
}

function resetErrorMedia() {
	$("#msgUpload").empty();
	$("#loading").show();
}

$(document).ready(function() {  
	var optionsForm = {
		beforeSubmit:	resetErrorMedia,
       		 success:    	showResponseMedia,  // post-submit callback 
        		url:       		"{/literal}{$html->url('/files/uploadAjaxMediaProvider')}{literal}",       // override for form's 'action' attribute 
        		dataType:  		'json'        // 'xml', 'script', or 'json' (expected server response type) 
    }; 

    $("#uploadFormMedia").bind("click", function() {
    	$('#updateForm').ajaxSubmit(optionsForm);
    	return false;
    }); 
	 
});
{/literal}
</script>
<div>
	<input type="hidden" name="lang" value="{if $session->check('Config.language')}{$session->read('Config.language')}{else}ita{/if}"/>
	{t}Url{/t}: <input type="text" name="url" />&nbsp;
	{t}Title{/t}: <input type="text" name="title" />&nbsp;
	<input type="button" id="uploadFormMedia" value="{t}uploadForm{/t}"/>
<div id="msgUpload"></div>
</div>