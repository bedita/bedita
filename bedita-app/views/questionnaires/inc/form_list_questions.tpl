<script type="text/javascript">
<!--
var urlAddObjToAssQuestion = "{$html->url('/questionnaires/loadQuestionAjax')}";

{literal}

function questionRefreshButton() {
	$("#loadQuestion").hide();
	$("#questionAssociated").find("input[@name='details']").click(function() {
		location.href = $(this).attr("rel");
	});
	
	$("#questionAssociated").find("input[@name='remove']").click(function() {
		tableToReorder = $(this).parents("table");
		$(this).parents("tr").remove();
		tableToReorder.fixItemsPriority();
	});
}


function addObjToAssocQuestion(url, postdata) {
	$.post(url, postdata, function(html){
		$("#loadQuestion").show();
		$("#questionAssociated tr:last").after(html);
		$("#questionAssociated").fixItemsPriority();
		$("#questionAssociated").find("tbody").sortable("refresh");
		questionRefreshButton();
	});
}

$(document).ready(function() {
	$("#questionAssociated").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).fixItemsPriority
	}).css("cursor","move");

	questionRefreshButton();
});

{/literal}
//-->
</script>
	
<div class="tab"><h2>{t}Questions{/t}</h2></div>

<fieldset id="questions">
	<div class="loader" id="loadQuestion"><span></span></div>
	<input type="hidden" class="relationTypeHidden" name="data[RelatedObject][question][0][switch]" value="question" />
	<table class="indexlist" id="questionAssociated">
		<tr>
			<th></th>
			<th>{t}title{/t}</th>
			<th>{t}type{/t}</th>
			<th>{t}status{/t}</th>
			<th></th>
		</tr>
		<tbody>
		{if !empty($relObjects.question)}
			{include file="inc/form_question_ajax.tpl" objsRelated=$relObjects.question}
		{/if}
		</tbody>
	</table>
	
	<table class="indexlist">
	<tr>
		<th colspan="5" style="padding:10px; text-align:center">
			<input class="modalbutton" type="button" value="{t}insert more questions{/t}" rel="{$html->url('/pages/showObjects/')}{$object.id|default:0}/question/{$object_type_id}" />
		</th>
	</tr>
	</table>
	
</fieldset>