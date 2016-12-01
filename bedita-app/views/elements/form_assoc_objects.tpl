<script type="text/javascript">

var urlAddObjToAss= "{$html->url('/pages/loadObjectToAssoc/')}{$object.id}";

function relatedRefreshButton() {
	
	$(".remove",".relationList").click(function() {
		tableToReorder = $(this).parents("table");
		$(this).parents("tr").remove();
		tableToReorder.fixItemsPriority();
	});
}

function addObjToAssocRelated(url, postdata) {
	$("#loadingDownloadRel").show();
	return $.post(url, postdata, function(html){
		$("#loadingDownloadRel").hide();
		var newTrs = $(html);
		var tbody = $("#relationType_" + postdata.relation + " table:first").find("tbody");
		tbody.append( newTrs );
		$("#relationType_" + postdata.relation).fixItemsPriority();
		$(".relationList table.indexlist").find("tbody:first").sortable("refresh");
		newTrs.each(function() {
			$(document).trigger('relation_' + postdata.relation + ':added', $(this));
		});
		relatedRefreshButton();

		// execute select2 on new elements if available
		if ( $.isFunction($.fn.select2) ) {
			newTrs.find('select').select2();
		}
	});
}

$(document).ready(function() {
	$(".relationList table.indexlist").find("tbody:first").sortable({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority,
		cancel: 'span, input, textarea, [contenteditable]'
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

	$(document).on('click', '.relViewOptions', function() {
		$(this).closest('.relationList').toggleClass('boxed');
	});

});
</script>

{$view->set("object_type_id",$object_type_id)}
{if !empty($availabeRelations)}
{foreach $availabeRelations as $rel => $relLabel}

{* ticket github #539 - if defined a relationView in config model, include that view, generic relation view otherwise *}
{if !empty($allObjectsRelations[$rel]['relationView']) && file_exists($allObjectsRelations[$rel]['relationView'])}

{include file=$allObjectsRelations[$rel]['relationView']}

{else}

{$relcount = $relObjects.$rel|@count|default:0}
<div class="tab">
	<h2 {if $relcount == 0}class="empty"{/if}>
		{t}{$relLabel}{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}
	</h2>
</div>

<div class="relationList {if $rel == "attach"}boxed{/if}" id="relationType_{$rel}">

	<div class="relViewOptions">
		<img class="multimediaitemToolbar viewthumb" src="{$html->webroot}img/iconML-thumb.png" />
		<img class="multimediaitemToolbar viewsmall" src="{$html->webroot}img/iconML-list.png" />
	</div>

	<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][{$rel}][0][switch]" value="{$rel}" />
	<table class="indexlist">
		<thead>
			<tr>
				<th></th>
				<th></th>
				<th>{t}title{/t}</th>
				<th></th>
				<th>{t}status{/t}</th>
				<th>{t}lang{/t}</th>
				<th>{t}type{/t} and {t}size{/t}</th>
				<th>{t}more{/t}</th>
				<th style="text-align:right">{t}commands{/t}</th>
			</tr>
		</thead>
		<tbody>
			<tr class="trick"><td></td></tr>
		{if !empty($relObjects.$rel)}
			{assign_associative var="params" objsRelated=$relObjects.$rel rel=$rel}
			{$view->element('form_assoc_object', $params)}
		{/if}
		</tbody>
	</table>
	
	<input type="button" class="modalbutton" title="{t}{$rel}{/t} : {t}select an item to associate{/t}"
	rel="{$html->url('/pages/showObjects/')}{$object.id|default:0}/{$rel}/{$object_type_id}" 
	value="  {t}connect new items{/t}  " />
	
</div>
{/if}

{/foreach}
{/if}