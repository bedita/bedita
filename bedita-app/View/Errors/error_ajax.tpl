{if $output == "json"}
	{$htmlMsg = $this->Session->flash('error')}
	{$data = ["errorMsg" => $errorMsg, "htmlMsg" => $htmlMsg]}
	{$javascript->object($data)}
{elseif $output == "html"}
	{$this->Session->flash('error')}
{elseif $output == "beditaMsg"}
	<script type="text/javascript">
	var flashMsg = escape('{$this->Session->flash('error')}');
	$(document).ready(function() {
		$("#messagesDiv").empty().html(unescape(flashMsg)).triggerMessage("error");
	});
	</script>
{elseif $output == "reload"}
	<script type="text/javascript">
	location.reload();
	</script>
{/if}