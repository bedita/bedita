{literal}
<script type="text/javascript">

	function toggleEditor(id) {
		if (!tinyMCE.get(id))
		tinyMCE.execCommand('mceAddControl', false, id);
		else
		tinyMCE.execCommand('mceRemoveControl', false, id);
	}
			
function reindexQuestions() {
	var fieldName = "";
	var updateFieldName = ""
	var indexQuestion = 0;
	var incrementIndex;
	$("#answers tr").each(function(rowindex) {
		incrementIndex = false;
		$(this).find(":input[name^='data[QuestionAnswer]']:enabled").each(function() {
			fieldName = $(this).attr("name");
			updateFieldName = fieldName.replace(/\[(\d+)\]/, "[" + indexQuestion + "]");
			$(this).attr("name", updateFieldName);
			incrementIndex = true;
		});
		if (incrementIndex) {
			indexQuestion++;
		}
	});
}

$(document).ready(function(){

	$(".toggleMCE").click(function (){
		var mceid = $(this).attr('rel');
		toggleEditor(mceid);
	});
			
	$(".add").click(function (){

		var row = $(this).parent().parent("tr");
		
		//disabilta mce prima di clonare
		var mceid = $(row).find("textarea").attr('id');
		tinyMCE.execCommand('mceRemoveControl', false, mceid);
		
		//clona e aggiunge nuopvo id al row e alla textarea
		var newrowid = "new"+($(".newrow").size())+mceid;
		alert(newrowid);
		var newtextareaid = "t"+newrowid;
		var mytxtarea = $(row).clone(true).insertAfter(row).addClass("newrow").attr("id",newrowid).find("textarea").html('').attr("id",newtextareaid);
		//prpare il toggler di mce
		$("#"+newrowid+" .toggleMCE").attr("rel",newtextareaid);
		
		//riabilita
		tinyMCE.execCommand('mceAddControl', false, mceid);
		tinyMCE.execCommand('mceAddControl', false, newtextareaid);
		
		$("#answers").fixItemsPriority();
		reindexQuestions();
	});
	
	$(".remove").click(function (){
		var row = $(this).parent().parent("tr");
		$(row).css("opacity","0.3");		
		$("input,textarea", row).attr("disabled", true);
		$(".undo", row).show().attr("disabled", false);
		$(".iteration, textarea", row).css("text-decoration","line-through");
		$(".add,.remove", row).hide();
		$("#answers").fixItemsPriority();
		reindexQuestions();
 	});

	$(".undo").click(function (){
		var row = $(this).parent().parent("tr");
		$(row).css("opacity","1");		
		$("input,textarea", row).attr("disabled", false);
		$(".undo", row).hide();
		$(".add,.remove", row).show();
		$(".iteration, textarea", row).css("text-decoration","none");
		$("#answers").fixItemsPriority();
		reindexQuestions();
 	});
	
	showAnswers();

});

</script>
{/literal}

<div style="display:none" class="multiple single_radio single_pulldown checkopen degree">

	{t}answers{/t}:
	
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