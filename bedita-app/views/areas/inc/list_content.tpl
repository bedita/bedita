{*$html->script("jquery/jquery.disable.text.select", true)*}

<script type="text/javascript">
<!--
var urlAddObjToAss = "{$html->url('/pages/loadObjectToAssoc')}/{$object.id|default:0}/leafs/areas.inc.list_contents_for_section";
var priorityOrder = "{$priorityOrder|default:'asc'}";
var pageUrl = "{$beurl->getUrl('object_type_id')}";

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

		if ($("#noContents")) {
			$("#noContents").hide();
		}
		$("#areacontent").fixItemsPriority(startPriority);
		$("#areacontent").sortable("refresh");
		$("#areacontent table").find("tbody").sortable("refresh");
		setRemoveActions();
	});
}

function setRemoveActions() {
	$("#areacontent").find("input[name='remove']").click(function() {
		var contentField = $("#contentsToRemove").val() + $(this).parents("tr:first").find("input[name*='[id]']").val() + ",";
		$("#contentsToRemove").val(contentField);
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
		
		if (priorityOrder == "desc" && $(this) != $("#areacontent").find("input[name*='[priority]']:first")) {
			startPriority--;
		}
		
		$(this).parents("tr:first").remove();
		
		if ($("#areacontent tr:visible").not('#noContents').length == 0) {
			$("#noContents").show();
		}
		$("#areacontent").fixItemsPriority(startPriority);
	});
}

$(document).ready(function() {

	if ($("#areacontent").find("input[name*='[priority]']:first")) {
		var startPriority = $("#areacontent").find("input[name*='[priority]']:first").val();
	} else {
		var startPriority = 1;
	}

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
	
	setRemoveActions();
	
	$(".newcontenthere").click(function(){
		var urltogo = $('.selectcontenthere').val();
		window.location.href = urltogo;
		return false;
	});
		
	$("#selObjectType").change(function() {
		var url = ($(this).val() != "")? pageUrl + "/object_type_id:" + $(this).val() : pageUrl;
		location.href = url;
	});
	
});


    $(function() {
        //$('.disableSelection').disableTextSelect();
    });

{/literal}
//-->
</script>



<div style="min-height:120px; margin-top:10px;">

	<div id="areacontent">

	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
			<input type="hidden" name="contentsToRemove" id="contentsToRemove" value=""/>
			<tr id="noContents"{if !empty($objects)} style="display: none;"{/if}>
				<td><i>no items</i></td>
			</tr>
			{include file="../inc/list_contents_for_section.tpl" objsRelated=$objects}
		</tbody>
	</table>
	
	</div>

			
	<div id="contents_nav_leafs" style="margin-top:10px; padding:10px 0px 10px 0px; overflow:hidden; border-bottom:1px solid gray" class="ignore">	
		<div style="padding-left:0px; float:left;">
		{t}show{/t}
		{assign var="allLabel" value=$tr->t("all", true)}
		{$beToolbar->changeDimSelect('selectTop', [], [5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100, 1000000 => $allLabel])} &nbsp;
		{t}item(s){/t} 
		</div>	
		<div style="padding-left:30px; float:left;">
		{t}content type{/t}
		<select id="selObjectType">
			<option value=""{if empty($view->params.named.object_type_id)} selected="selected"{/if}>{t}all{/t}</option>
			{foreach from=$conf->objectTypes.leafs.id item="objectTypeId"}
				<option value="{$objectTypeId}" class="{$conf->objectTypes[$objectTypeId].module_name}" style="padding-left:5px"
						{if !empty($view->params.named.object_type_id) && $view->params.named.object_type_id == $objectTypeId}selected="selected"{/if}> {$conf->objectTypes[$objectTypeId].name}</option>
			{/foreach}
		</select>
		</div>
		<div class="toolbar sans" style="text-align:right; padding-left:30px; float:right;">
			
			{$beToolbar->first('page','','page')}
			 	
				{$beToolbar->current()} 
				&nbsp;{t}of{/t}&nbsp;
			
				{if ($beToolbar->pages()) > 0}
				{$beToolbar->last($beToolbar->pages(),'',$beToolbar->pages())}
				{else}1{/if}
			
			{if ($beToolbar->pages()) > 1}
			
				&nbsp;
				
				{$beToolbar->prev('‹ prev','','‹ prev')}
			
				&nbsp;
				
				{$beToolbar->next('next ›','','next ›')}
			
			{/if}
			
		</div>

	</div>


	<br style="clear:both" />
		
	{include file="inc/tools_commands.tpl" type="all"}

	{bedev}
	{include file="inc/bulk_actions.tpl" type="all"}
	{/bedev}


</div>	
	