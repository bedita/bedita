{if $output == "json"}
	{$htmlMsg = $session->flash('error')}
	{$data = ["errorMsg" => $errorMsg, "htmlMsg" => $htmlMsg]}
	{$javascript->object($data)}
{elseif $output == "html"}
	{$session->flash('error')}
{elseif $output == "beditaMsg"}{* you have to define a jQuery triggerMessage function in order to use this option in frontends *}
	<script type="text/javascript">
	var flashMsg = escape('{$session->flash('error')}');
	$(document).ready(function() {
		$("#messagesDiv").empty().html(unescape(flashMsg)).triggerMessage("error");
	});
	</script>
{elseif $output == "reload"}
	<script type="text/javascript">
	location.reload();
	</script>
{/if}