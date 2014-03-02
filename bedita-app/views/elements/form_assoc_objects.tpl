{*$html->script("jquery/jquery.disable.text.select", true)*}

<script type="text/javascript">
var urlAddObjToAss= "{$html->url('/pages/loadObjectToAssoc/')}{$object.id}";
<!--

function relatedRefreshButton() {
	$("#relationContainer").find("input[name='details']").click(function() {
		location.href = $(this).attr("rel");
	});
	
	$("#relationContainer").find("input[name='remove']").click(function() {
		tableToReorder = $(this).parents("table");
		$(this).parents("tr").remove();
		tableToReorder.fixItemsPriority();
	});
}

function addObjToAssoc(url, postdata) {
	$("#loadingDownloadRel").show();
	$.post(url, postdata, function(html){
		$("#loadingDownloadRel").hide();
		$("#relationType_" + postdata.relation + " table:first").find("tr:last").after(html);
		$("#relationType_" + postdata.relation).fixItemsPriority();
		$("#relationContainer table").find("tbody").sortable("refresh");
		relatedRefreshButton();
	});
}

function commitUploadItemDownloadRel(IDs) {
	obj_sel = {};
	obj_sel.object_selected = "";
	for(var i=0 ; i < IDs.length ; i++) {
		obj_sel.object_selected += IDs[i] + ",";
	}
	obj_sel.relation = "download";
	addObjToAssoc(urlAddObjToAss, obj_sel);	
}

function showResponseDownloadRel(data) {
	if (data.UploadErrorMsg) {
		$("#loadingDownloadRel").hide();
		$("#ajaxUploadContainerDownloadRel").append("<label class='error'>"+data.UploadErrorMsg+"<\/label>").addClass("error");
	} else {
		var tmp = new Array() ;
		var countFile = 0; 
		$.each(data, function(entryIndex, entry) {
			tmp[countFile++] = entry['fileId'];
		});

		commitUploadItemDownloadRel(tmp);
	}
	
	$("#ajaxUploadContainerDownloadRel").find("input[@type=text]").attr("value", "");
	$("#ajaxUploadContainerDownloadRel").find("input[@type=file]").attr("value", "");
	$("#ajaxUploadContainerDownloadRel").find("textarea").attr("value", "");
}

function resetErrorDownloadRel() {
	$("#ajaxUploadContainerDownloadRel").find("label").remove();
	$("#loadingDownloadRel").show();
}

$(document).ready(function() {
	$("#relationContainer table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority
	}).css("cursor","move");
	
	relatedRefreshButton();
	
	$("input[name='addIds']").click(function() {
		obj_sel = {};
		input_ids = $(this).siblings("input[name='list_object_id']");
		obj_sel.object_selected = input_ids.val();
		obj_sel.relation = $(this).siblings("input[name*='switch']").val();
		addObjToAssoc(urlAddObjToAss, obj_sel);
		input_ids.val("");
	});
	
	// manage enter key on search text to prevent default submit
	$("input[name='list_object_id']").keypress(function(event) {
		if (event.keyCode == 13 && $(this).val() != "") {
			event.preventDefault();
			obj_sel = {};
			obj_sel.object_selected = $(this).val();
			obj_sel.relation = $(this).siblings("input[name*='switch']").val();
			addObjToAssoc(urlAddObjToAss, obj_sel);
			$(this).val("");
		}
	});

	// upload ajax for download relation
	var optionsFormDownloadRel = {
		beforeSubmit:	resetErrorDownloadRel,
		success:		showResponseDownloadRel,  // post-submit callback  
		dataType:		'json',        // 'xml', 'script', or 'json' (expected server response type)
		url: "{$html->url('/files/uploadAjax/DownloadRel')}"
	};

	$("#uploadFormDownloadRel").click(function() {
		$('#updateForm').ajaxSubmit(optionsFormDownloadRel);
		return false;
	});
	
});

$(function() {
   // $('.disableSelection').disableTextSelect();
});

//-->
</script>


{$view->set("object_type_id",$object_type_id)}
<div class="tab"><h2>{t}Relationships{/t}</h2></div>

<fieldset id="frmAssocObject">
	
	<div id="loadingDownloadRel" class="loader" title="{t}Loading data{/t}"></div>
	
	<table class="htab">
	<tr>
	{foreach from=$availabeRelations item="rel"}
		<td rel="relationType_{$rel}">{t}{$rel}{/t}</td>
	{/foreach}
	</tr>
	</table>


	<div class="htabcontainer" id="relationContainer">
	{foreach from=$availabeRelations item="rel"}
	<div class="htabcontent" id="relationType_{$rel}">

		<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][{$rel}][0][switch]" value="{$rel}" />				
		
		<table class="indexlist" style="width:100%; margin-bottom:10px;">
			<tbody class="disableSelection">
			{if !empty($relObjects.$rel)}
				{assign_associative var="params" objsRelated=$relObjects.$rel rel=$rel}
				{$view->element('form_assoc_object', $params)}
			{else}
				<tr><td colspan="10"></td></tr>
			{/if}
			</tbody>
		</table>
		
		<input type="button" class="modalbutton" title="{t}{$rel}{/t} : {t}select an item to associate{/t}"
		rel="{$html->url('/pages/showObjects/')}{$object.id|default:0}/{$rel}/{$object_type_id}" style="width:200px" 
		value="  {t}connect new items{/t}  " />
		
		{if $rel == "download"}
			{assign_associative var="params" uploadIdSuffix="DownloadRel"}
			{$view->element('form_upload_multi', $params)}
		{/if}
		
		{*
		<br /><br />
		{t}or{/t} &nbsp;
		<label>{t}add by object ids{/t}</label>: <input type="text" name="list_object_id" size="12" /> 
		<input class="BEbutton" name="addIds" type="button" value="{t}add{/t}">
		*}

		
	</div>
	{/foreach}
	</div>


	
</fieldset>