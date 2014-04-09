<script type="text/javascript">

var urlAddObjToAss= "{$html->url('/pages/loadObjectToAssoc/')}{$object.id}";

function relatedRefreshButton() {
	
	$(".remove",".relationList").click(function() {
		tableToReorder = $(this).parents("table");
		$(this).parents("tr").remove();
		tableToReorder.fixItemsPriority();
	});
}

function addObjToAssoc(url, postdata) {
	$("#loadingDownloadRel").show();
	$.post(url, postdata, function(html){
		$("#loadingDownloadRel").hide();
		var newTrs = $(html);
		var tbody = $("#relationType_" + postdata.relation + " table:first").find("tr").first().parent();
		tbody.append( newTrs );
		$("#relationType_" + postdata.relation).fixItemsPriority();
		$(".relationList table.indexlist").find("tbody:first").sortable("refresh");
		newTrs.each(function() {
			$(document).trigger('relation_' + postdata.relation + ':added', $(this));
		});
		relatedRefreshButton();
	});
}

$(document).ready(function() {
	$(".relationList table.indexlist").find("tbody:first").sortable({
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

	$(document).on('click', '.showmore', function() {
		var container = $(this).closest('.obj');
		$(this).toggleText('+', '-');
		$('.moredata > div', container).toggle();
		//mette il modified a 1 
		var mod = $('.mod', container).val();
		if (mod > 0) {
			$('.mod',container).val(0);
		}
		else {
			$('.mod',container).val(1);
		}
		//e mostra la textarea del titolo al posto del titolo 
		$('.assoc_obj_title textarea, .assoc_obj_title input[type="text"]', container).toggle();
		$('.assoc_obj_title h4', container).toggle();
	});

	$(document).on('click', '.relViewOptions', function() {
		$(this).closest('.relationList').toggleClass('boxed');
	});

});
</script>

{$view->set("object_type_id",$object_type_id)}

{foreach $availabeRelations as $rel => $relLabel}

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
				<th>{t}title{/t}</th>
				<th>{t}type{/t}</th>
				<th style="text-align:right">{t}size{/t}</th>
				<th>{t}status{/t}</th>
				<th>{t}lang{/t}</th>
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

{/foreach}


{*
<!--
<div class="tab"><h2>{t}Relations{/t}</h2></div>

<fieldset id="frmAssocObject">
	
	<div id="loadingDownloadRel" class="loader" title="{t}Loading data{/t}"></div>
	
	<table class="htab">
	<tr>
	{foreach $availabeRelations as $rel => $relLabel}
		<td rel="relationType_{$rel}">{t}{$relLabel}{/t}</td>
	{/foreach}
	</tr>
	</table>

	<div class="htabcontainer" id="relationContainer">
	{foreach $availabeRelations as $rel => $relLabel}
	<div class="htabcontent" id="relationType_{$rel}">
		<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][{$rel}][0][switch]" value="{$rel}" />
		
		<table class="indexlist" style="width:100%; margin-bottom:10px;">
			<tbody class="disableSelection">
				<tr><td colspan="10" style="padding: 0"></td></tr>
			{if !empty($relObjects.$rel)}
				{assign_associative var="params" objsRelated=$relObjects.$rel rel=$rel}
				{$view->element('form_assoc_object', $params)}
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
	

		
	</div>
	{/foreach}
	</div>

</fieldset>
-->
*}
