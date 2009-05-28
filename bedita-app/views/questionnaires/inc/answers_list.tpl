{literal}
<script type="text/javascript">

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

<div style="display:none" class="multiple single_radio single_pulldown checkopen degree">

	answers:
	
	<table id="answers" style="margin-top:5px;">
	{if !empty($object.QuestionAnswer)}

		{foreach from=$object.QuestionAnswer item="answer" name="fca"}
			
			{include file="./inc/single_answer.tpl" i=$smarty.foreach.fca.index it=$smarty.foreach.fca.iteration}	

		{/foreach}
	
	{else}

		{section name="w" loop=3}

			{include file="./inc/single_answer.tpl" i=$smarty.section.w.index it=$smarty.section.w.iteration}	

		{/section}
	{/if}
	</table>
</div>

<!--  --------------------  -->

<div style="display:none" class="freetext">
	
	max characters: <input style="width:60px" type="text" name="data[max_chars]" value="{$object.max_chars|default:''}" />
	
</div>