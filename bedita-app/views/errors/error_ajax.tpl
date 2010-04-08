{if $output == "json"}
	{$javascript->object($errorMsg)}
{elseif $output == "html"}
	{if $session->flash('error')}{/if}
{elseif $output == "beditaMsg"}
	<script type="text/javascript">
	var flashMsg = escape('{if $session->flash('error')}{/if}');
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