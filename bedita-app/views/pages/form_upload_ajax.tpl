<script type="text/javascript">
{literal}

function commitFileUpload(tmp) {
	try {
		{/literal}{$controller}{literal}CommitUploadItem(tmp) ;
	} catch(e) {
		parent.{/literal}{$controller}{literal}CommitUploadItem(tmp) ;
	}
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
		commitFileUpload(tmp);
		$('#container-1 > ul').tabs('select',0);
	}
}

function resetError() {
	$("#msgUpload").empty();
	$("#loading").show();
}

$(document).ready(function() {  
	var optionsForm = {
		beforeSubmit:	resetError,
        success:    	showResponse,  // post-submit callback 
        url:       		"{/literal}{$html->url('/files/uploadAjax')}{literal}",       // override for form's 'action' attribute 
        dataType:  		'json'        // 'xml', 'script', or 'json' (expected server response type) 
    }; 

    $("#uploadForm").bind("click", function() {
    	$('#updateForm').ajaxSubmit(optionsForm);
    	return false;
    }); 
	 
});
{/literal}
</script>
<div>
	<input type="file" name="Filedata" />
	<input type="hidden" name="MAX_FILE_SIZE" value="3000" />
	<input type="hidden" name="lang" value="{if $session->check('Config.language')}{$session->read('Config.language')}{else}ita{/if}"/>
	<input type="button" id="uploadForm" value="{t}Upload{/t}"/>
<div id="msgUpload"></div>
</div>