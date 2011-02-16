{if $output == "json"}
	{$javascript->object($errorMsg)}
{elseif $output == "html"}
	{$session->flash('error')}
{elseif $output == "beditaMsg"}
	<script type="text/javascript">
	var flashMsg = escape('{$session->flash('error')}');
	{literal}
	$(document).ready(function() {
		$("#messagesDiv").empty().html(unescape(flashMsg)).triggerMessage("error");
	});
	{/literal}
	</script>
{elseif $output == "reload"}
	<script type="text/javascript">
	location.reload();
	</script>
{/if}