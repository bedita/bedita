{literal}
<script type="text/javascript">
$(document).ready(function(){	

	$(".add").click(function (){
		var row = $(this).parent().parent("tr");
		$(row).clone(true).insertAfter(row).addClass("newrow").find(".iteration").empty();
	});
	
	$(".remove").click(function (){
		//da fare if not:first
		var row = $(this).parent().parent("tr");
		$(row).remove();
 	});

	showAnswers();

});

$("#tipoID").change(function () {
	showAnswers();
		$('#preview_container_question').hide();
		$('#preview').toggleValue("CLOSE PREVIEW","PREVIEW");
});

function showAnswers() {
	$(".answers div").hide();
	$(".answers div :input").attr("disabled", "disabled");
	$(".answers div :checkbox").attr("disabled", "disabled");
	var kind = $("#tipoID option:selected").val();
	$("." + kind).show();
	$("." + kind + " :input").attr("disabled", "");
	$("." + kind + " :checkbox").attr("disabled", "");
}

</script>
{/literal}


<div style="display:none" class="multiple single_radio single_pulldown checkopen">
	<table>
	{if !empty($object.QuestionAnswer)}
		{foreach from=$object.QuestionAnswer item="answer" name="fca"}
			<tr>
				<td class="iteration" style="padding-left:0px;">{$smarty.foreach.fca.iteration}.&nbsp;
					<input type="hidden" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][priority]" value="{$smarty.foreach.fca.iteration}" />
				</td>
				<td><textarea style="height:32px; width:240px !important;" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][description]" class="autogrowarea">{$answer.description}</textarea></td>
				<td>
					&nbsp;&nbsp;<input type="checkbox" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][correct]" value="1"{if $answer.correct == 1} checked="checked"{/if}>&nbsp;{t}correct{/t}&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					<input type="button" class="add" title="{t}add{/t}" value="+" />
				</td>
				<td>
					<input type="button" class="remove" title="{t}remove{/t}" value="-" />
				</td>
				<td>
					
				</td>
			</tr>
		{/foreach}
	{else} 
		{section name="w" loop=5}
			<tr>
				<td class="iteration" style="padding-left:0px;">{$smarty.section.w.iteration}.&nbsp;
					<input type="hidden" name="data[QuestionAnswer][{$smarty.section.w.index}][priority]" value="{$smarty.section.w.iteration}" />
				</td>
				<td><textarea style="height:32px; width:240px !important;" name="data[QuestionAnswer][{$smarty.section.w.index}][description]" class="autogrowarea"></textarea></td>
				<td>
					&nbsp;&nbsp;<input type="checkbox" name="data[QuestionAnswer][{$smarty.section.w.index}][correct]" value="1">&nbsp;{t}correct{/t}&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					<input type="button" class="add" title="{t}add{/t}" value="+" />
				</td>
				<td>
					<input type="button" class="remove" title="{t}remove{/t}" value="-" />
				</td>
				<td>
					
				</td>
			</tr>
		{/section}
	{/if}
	</table>
</div>

<!--  -->

<div style="display:none" class="freetext">
	
	max characters: <input style="width:60px" type="text" name="data[max_chars]" value="{$object.max_chars|default:''}" />
	
</div>