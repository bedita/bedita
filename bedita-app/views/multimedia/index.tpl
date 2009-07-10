{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

{*$javascript->link("jquery/jquery.MultiFile.pack", false)*}

<script type="text/javascript">
	
	
var urlGetObj		= '{$html->url("/streams/get_item_form_by_id")}' ;
var containerItem = "#multimediaItems";

{literal}
$(document).ready(function() {  
	var optionsForm = {
		beforeSubmit:	resetError,
		success:		showResponse,  // post-submit callback  
		dataType:		'json',        // 'xml', 'script', or 'json' (expected server response type)
		url: "{/literal}{$html->url('/files/uploadAjax')}{literal}"
	};

	$("#uploadForm").click(function() {
		$('#uploadAjaxMedia').ajaxSubmit(optionsForm);
		return false;
	});
});
			
function commitUploadItem(IDs, rel) {

	var emptyDiv = "<div  class='multimediaitem itemBox gold'><\/div>";
	for(var i=0 ; i < IDs.length ; i++)
	{
		var id = escape(IDs[i]) ;

		$(emptyDiv).load(
			urlGetObj, {'id': id, 'relation':rel, 'template':'common_inc/file_item.tpl'}, function (responseText, textStatus, XMLHttpRequest)
			{
				$("#loading").hide();
				$(containerItem).append(this); 
			}
		)
	}	
}

function showResponse(data) {
	if (data.UploadErrorMsg) {
		$("#loading").hide();
		$("#ajaxUploadContainer").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
	} else {
		var tmp = new Array() ;
		var countFile = 0; 
		$.each(data, function(entryIndex, entry) {
			tmp[countFile++] = entry['fileId'];
		});

		commitUploadItem(tmp, "attach");
	}
	
	$("#ajaxUploadContainer").find("input[@type=text]").attr("value", "");
	$("#ajaxUploadContainer").find("input[@type=file]").attr("value", "");
	$("#ajaxUploadContainer").find("textarea").attr("value", "");
}

function resetError() {
	$("#ajaxUploadContainer").find("label").remove();
	$("#loading").show();
}

{/literal}
</script>
</head>

<body>


{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index" fixed=true}

{include file="../common_inc/toolbar.tpl"}

<div class="mainfull">

	{include file="./inc/list_streams.tpl" method="index" streamTitle="multimedia"}
	

	<div class="tab"><h2>{t}Add multiple items{/t}</h2></div>
	
	<div class="htabcontent">
		<div style="clear:both; margin:-20px 0px 20px -20px">
		{* form needed for ajax upload *}
		<form id="uploadAjaxMedia" action="#" method="post" enctype="multipart/form-data">
		{include file="../common_inc/form_upload_multi.tpl"}
		</form>
		</div>		 

		<div id="loading" style="clear:both" class="multimediaitem itemBox small">&nbsp;</div>
		<div id="multimediaItems"></div>
			

	</div>
	
</div>

