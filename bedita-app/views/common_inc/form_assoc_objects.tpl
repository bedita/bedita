<script type="text/javascript">
var urlAddObjToAss= "{$html->url('/areas/loadObjectToAssoc')}";
<!--
{literal}

function relatedRefreshButton() {
	$("#relationContainer").find("input[@name='details']").click(function() {
		location.href = $(this).attr("rel");
	});
	
	$("#relationContainer").find("input[@name='remove']").click(function() {
		tableToReorder = $(this).parents("table");
		$(this).parents("tr").remove();
		tableToReorder.fixItemsPriority();
	});
}

function addObjToAssoc(url, postdata) {
	$.post(url, postdata, function(html){
		$("#relationType_" + postdata.relation + " tr:last").after(html);
		$("#relationType_" + postdata.relation).fixItemsPriority();
		$("#relationContainer table").find("tbody").sortable("refresh");
		relatedRefreshButton();
	});
}

$(document).ready(function() {
	$("#relationContainer table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority
	}).css("cursor","move");
	
	relatedRefreshButton();
	
	$("input[@name='addIds']").click(function() {
		obj_sel = {};
		input_ids = $(this).siblings("input[@name='list_object_id']");
		obj_sel.object_selected = input_ids.val();
		obj_sel.relation = $(this).siblings("input[name*='switch']").val();
		addObjToAssoc(urlAddObjToAss, obj_sel);
		input_ids.val("");
	});
	
	// manage enter key on search text to prevent default submit
	$("input[@name='list_object_id']").keypress(function(event) {
		if (event.keyCode == 13 && $(this).val() != "") {
			event.preventDefault();
			obj_sel = {};
			obj_sel.object_selected = $(this).val();
			obj_sel.relation = $(this).siblings("input[name*='switch']").val();
			addObjToAssoc(urlAddObjToAss, obj_sel);
			$(this).val("");
		}
	});
	
});
{/literal}
//-->
</script>



<div class="tab"><h2>{t}Connections{/t}</h2></div>

<fieldset id="frmAssocObject">
	
	<div id="loadingAssoc" class="generalLoading" title="{t}Loading data{/t}"></div>
	
	<ul class="htab">
	{foreach from=$conf->objRelationType item="rel"}
			<li rel="relationType_{$rel}">{$rel}</li>
	{/foreach}
	</ul>
	
	<div class="htabcontainer" id="relationContainer">
	{foreach from=$conf->objRelationType item="rel"}
	<div class="htabcontent" id="relationType_{$rel}">
		<input type="hidden" class="relationTypeHidden" name="data[ObjectRelation][{$rel}][switch]" value="{$rel}" />				
		
		<table class="indexlist">
		<tbody>
		{if !empty($relObjects.$rel)}
			{include file="../common_inc/form_assoc_object.tpl" objsRelated=$relObjects.$rel}
		{else}
			<tr><td></td></tr>
		{/if}
		</tbody>
		</table>
		
		<hr />
		aggiungi nuova relazione di tipo "{$rel}": 
		<br />
		<label>{t}add by object ids{/t}</label>: <input type="text" name="list_object_id" size="12" /> 
		<input class="BEbutton" name="addIds" type="button" value="{t}add{/t}">
		&nbsp; {t}or{/t} &nbsp;
		
		<input type="button" class="modalbutton" rel="{$html->url('/areas/showObjects/')}{$rel}" value="{t}choose objects{/t}" />
		
		
		
	</div>
	{/foreach}
	</div>


	
</fieldset>