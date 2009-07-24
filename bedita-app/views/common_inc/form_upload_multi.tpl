{$javascript->link('swfobject', false)}
{$javascript->link('jquery/jquery.uploadify.v2.0.0.min', false)}

<script type="text/javascript">
<!--
var webroot = "{$html->webroot}";
var multiUploadUrl = "{$html->url('/')}files/upload";
var u_id = "{$session->read("BEAuthUser.id")}";
{literal}
$(document).ready(function() {

	if (getFlashVersion() !== false) {
		$('#inputFiledata').uploadify({
			'uploader': webroot + 'swf/uploadify.swf',
			'script':    multiUploadUrl,
			multi: true,
			auto: true,
			'cancelImg': webroot + 'img/uploadCancel.png',
			'buttonImg': webroot + 'img/multiupload-browse.png',
			width: 124,
			buttonText : 'browssssse',
			displayData: 'percentage',
			onComplete: completeUpload,
			scriptData: {userid: u_id}
		});

		$("#flashUploadContainer a").click(function() {
			$("#ajaxUploadContainer").show();
			$("#flashUploadContainer").hide();
		});

		$("#ajaxUploadContainer a").click(function() {
			$("#ajaxUploadContainer").hide();
			$("#flashUploadContainer").show();
		});
		
	} else {
		$("#flashUploadContainer").hide();
		$("#ajaxUploadContainer").show();
		$("#ajaxUploadContainer a").hide();
	}

	
});

function completeUpload(event, queueID, fileObj,response) {
	if (isNaN(parseInt(response))) {
		$("#inputFiledata" + queueID + " .fileName").text(" Error - " + fileObj.name + " - " + response);
		$("#inputFiledata" + queueID).css({'border': '3px solid #FBCBBC', 'background-color': '#FDE5DD'});
		return false;
	} else {
		objids = new Array();
		objids[0] = response;
		$("#loading").show();
		commitUploadItem(objids, "attach");
		return true;
	}
}

function getFlashVersion(){ 
	// ie 
	try { 
		try { 
			// avoid fp6 minor version lookup issues 
			// see: http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/ 
			var axo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash.6'); 
      		try { 
				axo.AllowScriptAccess = 'always'; 
			} catch(e) {
				return '6,0,0';
			} 
    	} catch(e) {} 
    	return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version').replace(/\D+/g, ',').match(/^,?(.+),?$/)[1]; 
	// other browsers 
	} catch(e) { 
		try { 
			if(navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin){ 
				return (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g, ",").match(/^,?(.+),?$/)[1]; 
			} 
		} catch(e) {} 
	} 

  	return false; 
} 

{/literal}
//-->
</script>

<div id="flashUploadContainer" style="padding:20px 0px 0px 20px">
<input type="file" name="Filedata" id="inputFiledata" />
<p><a href="javascript:void(0);">{t}If you have any problems try with browser upload{/t}</a></p>
</div>

{include file="../common_inc/form_upload_ajax.tpl"}