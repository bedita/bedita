{$javascript->link("jquery/jquery.disable.text.select", true)}

<script type="text/javascript">
<!--
var urlAddObjToAss = "{$html->url('/areas/loadObjectToAssoc')}/{$object.id|default:0}/leafs/list_contents_for_section.tpl";
var priorityOrder = "{$priorityOrder|default:'asc'}";

{literal}

function addObjToAssoc(url, postdata) {
	$.post(url, postdata, function(html){
		if(priorityOrder == 'asc') {
			var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
			$("#areacontent tr:last").after(html);
		} else {
			var startPriority = parseInt($("#areacontent").find("input[name*='[priority]']:first").val());
			var beforeInsert = parseInt($("#areacontent tr").size());
			$("#areacontent tr:first").before(html);
			var afterInsert = parseInt($("#areacontent tr").size());
			startPriority = startPriority + (afterInsert - beforeInsert);
		}

		if ($("#noContents"))
			$("#noContents").remove();
		$("#areacontent").fixItemsPriority(startPriority);
		$("#areacontent").sortable("refresh");
		$("#areacontent table").find("tbody").sortable("refresh");
		setRemoveActions();
	});
}

function setRemoveActions() {
	$("#areacontent").find("input[@name='remove']").click(function() {
		var contentField = $("#contentsToRemove").val() + $(this).parents().parents().find("input[name*='[id]']").val() + ",";
		$("#contentsToRemove").val(contentField);
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
		
		if (priorityOrder == "desc" && $(this) != $("#areacontent").find("input[name*='[priority]']:first")) {
			startPriority--;
		}
		
		$(this).parents().parents("tr").remove();
		

		$("#areacontent").fixItemsPriority(startPriority);
	});
}

$(document).ready(function() {

	if ($("#areacontent").find("input[name*='[priority]']:first"))
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
	else
		var startPriority = 1;
		
	var urlC = ajaxContentsUrl + "/{/literal}{$selectedId|default:''}{literal}";

	//$("#areacontent").sortable ({
	$("#areacontent table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: function() {
					if (priorityOrder == 'desc' && startPriority < $("#areacontent").find("input[name*='[priority]']:first").val()) {
						startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
					}
					$(this).fixItemsPriority(startPriority);
				}
	}).css("cursor","move");

	$("#contents_nav_leafs a").click(function() {
		$("#loading").show();
		$("#areacontentC").load(urlC, 
				{
					page:$(this).attr("rel"),
					dim:$("#dimContentsPage").val()
				}
				, function() {
			$("#loading").hide();
		});
	});
	
	$("#dimContentsPage").change(function() {
		$("#loading").show();
		$("#areacontentC").load(urlC, {dim:$(this).val()}, function() {
			$("#loading").hide();
		});
	});
	
	setRemoveActions();
	
	$(".modalbutton").click(function () {
		$(this).BEmodal();
	});
	
});


    $(function() {
        $('.disableSelection').disableTextSelect();
    });

{/literal}
//-->
</script>



<div style="min-height:120px; margin-top:10px;">

	<div id="areacontent">

	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
			<input type="hidden" name="contentsToRemove" id="contentsToRemove" value=""/>
			{include file="inc/list_contents_for_section.tpl" objsRelated=$contents.items}
			<tr class="obj">
				
			</tr>
		</tbody>
	</table>
	
	</div>
	
	<div id="contents_nav_leafs">

	{*
	{if $contents.toolbar.prev > 0}
		<a href="javascript:void(0);" rel="{$contents.toolbar.prev}" class="graced" style="font-size:3em">‹</a>
	{/if}
	{if $contents.toolbar.next > 0}
		<a href="javascript:void(0);" rel="{$contents.toolbar.next}" class="graced" style="font-size:3em">›</a>
	{/if}
		
	dim:
	<select name="dimContentsPage" id="dimContentsPage">
		<option value="5"{if $dim == 5} selected{/if}>5</option>
		<option value="10"{if $dim == 10} selected{/if}>10</option>
		<option value="20"{if $dim == 20} selected{/if}>20</option>
		<option value="50"{if $dim == 50} selected{/if}>50</option>
		<option value="1000000"{if $dim == 1000000} selected{/if}>tutti</option>
	</select>
	*}
	</div>






	<br />
	<input style="width:220px" type="button" rel="{$html->url('/areas/showObjects/')}{$object.id|default:0}/0/0/leafs" class="modalbutton" value=" {t}add contents{/t} " />
	
	<hr />
</div>	

	