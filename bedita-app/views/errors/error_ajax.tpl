{if $output == "json"}
	{if ($session->check('Message.error'))}
		{assign_associative var="msgArr" errorMsg=$session->flash('error')}
		{$javascript->object($msgArr)}
	{/if}
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