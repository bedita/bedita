{literal}
<script type="text/javascript">

function showAnswers() {
	$(".answers div").hide();
	$(".answers div :input").attr("disabled", "disabled");
	$(".answers div :checkbox").attr("disabled", "disabled");
	var kind = $("#tipoID option:selected").val();
	$("." + kind).show();
	$("." + kind + " :input").attr("disabled", "");
	$("." + kind + " :checkbox").attr("disabled", "");
}

$("#tipoID").change(function () {
	showAnswers();
	$('#preview_container_question').hide();
	$('#preview').toggleValue("CLOSE PREVIEW","PREVIEW");
});

$(document).ready(function(){	

	$(".add").click(function (){
		var row = $(this).parent().parent("tr");
		$(row).clone(true).insertAfter(row).addClass("newrow").find("textarea").text("");
		$("#answers").fixItemsPriority();
	});
	
	$(".remove").click(function (){
		var row = $(this).parent().parent("tr");
		$(row).css("opacity","0.3");		
		$("input", row).attr("disabled", true);
		$(".undo", row).show().attr("disabled", false);
		$(".iteration, textarea", row).css("text-decoration","line-through");
		$(".add,.remove", row).hide();
		$("#answers").fixItemsPriority();
 	});

	$(".undo").click(function (){
		var row = $(this).parent().parent("tr");
		$(row).css("opacity","1");		
		$("input", row).attr("disabled", false);
		$(".undo", row).hide();
		$(".add,.remove", row).show();
		$(".iteration, textarea", row).css("text-decoration","none");
		$("#answers").fixItemsPriority();
 	});
	
	showAnswers();

});

</script>
{/literal}


<div style="display:none" class="multiple single_radio single_pulldown checkopen">
	<table id="answers">
	{if !empty($object.QuestionAnswer)}
		{foreach from=$object.QuestionAnswer item="answer" name="fca"}
			<tr>
				<td style="vertical-align:top; padding-left:0px;">
					<input class="iteration" type="text" style="width:14px" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][priority]" value="{$smarty.foreach.fca.iteration}" />
				</td>
				<td><textarea style="height:32px; width:240px !important;" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][description]" class="autogrowarea">{$answer.description}</textarea></td>
				<td>
					&nbsp;&nbsp;<input type="checkbox" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][correct]" value="1" 
					{if $answer.correct == 1} checked="checked"{/if}>&nbsp;{t}correct{/t}&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					<input type="button" class="add" title="{t}add{/t}" value="+" />
				</td>
				<td>
					<input type="button" class="remove" title="{t}remove{/t}" value="-" />
					<input type="button" style="display:none;" class="undo" value="{t}u{/t}" />
				</td>
			</tr>
		{/foreach}
	{else} 
		{section name="w" loop=3}
			<tr>
				<td style="padding-left:0px;">
					<input class="iteration" type="text" name="data[QuestionAnswer][{$smarty.section.w.index}][priority]" value="{$smarty.section.w.iteration}" />
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