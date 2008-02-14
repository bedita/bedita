<script type="text/javascript">
<!--
{literal}
function commitFileUpload(tmp) {
	try {
		commitUploadAttachment(tmp) ;
	} catch(e) {
		parent.commitUploadAttachment(tmp) ;
	}
}

function rollbackFileUpload() {
	try {
		rollbackUploadAttachment() ;
	} catch(e) {
		parent.rollbackUploadAttachment() ;
	}
}
{/literal}
//-->
</script>
{include file="../pages/form_upload.tpl"}