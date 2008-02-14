<script type="text/javascript">
<!--
{literal}
function commitFileUpload(tmp) {
	try {
		commitUploadImage(tmp) ;
	} catch(e) {
		parent.commitUploadImage(tmp) ;
	}
}

function rollbackFileUpload() {
	try {
		rollbackUploadImage() ;
	} catch(e) {
		parent.rollbackUploadImage() ;
	}
}
{/literal}
//-->
</script>
{include file="../pages/form_upload.tpl"}