<script type="text/javascript">
{literal}
$(document).ready(function() {
	$("div.reply a").click(function() {
		//var formComment = $("#respond").detach();
		$(this).parent("div.reply").parent("div:first").append($("#respond"));
		$("span#cancel-reply").show();
		$("#thread_parent_id").val($(this).parent().attr("rel"));
	});

	$("span#cancel-reply").click(function() {
		$("#respond").insertAfter("#anchor-comment");
		$("span#cancel-reply").hide();
		$("#thread_parent_id").val("");
	});
});
{/literal}
</script>

{if empty($object) && !empty($section.currentContent)}
	{assign var="object" value=$section.currentContent}
{/if}

{if $object.comments != "off"}

	{if !empty($object.Comment)}
		
		<h3 id="comments-title">{$object.num_of_comment|default:0}
		{t}Response to{/t} <em>{$object.title}</em></h3>

		{$wp->showComments($object.Comment)}
	
	{/if}

	{$view->element('form_comments')}
	
{/if}

