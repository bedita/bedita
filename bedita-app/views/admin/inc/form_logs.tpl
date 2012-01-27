<script type="text/javascript">
<!--
var message = "{t}Are you sure that you want to empty the file?{/t}" ;
var urlEmptyFile = "{$html->url('emptyFile/')}";

	$(document).ready(function() { 
		$(".emptyFile").bind("click", function() { 
			if(!confirm(message)) { 
				return false ;
			} 
			var fileToEmpty = $(this).attr("title");
			$("#fileToEmpty").attr("value",fileToEmpty);
			$("#formObject").attr("action", urlEmptyFile) ;
			$("#formObject").submit() ;
		} );
	} );
//-->
</script>

<form method="post" action="" id="formObject">

<div class="tab"><h2>{t}System logs{/t}</h2></div>

<fieldset id="system_logs">
<div>
{if !empty($logs)}
	<input type="hidden" id="fileToEmpty" name="data[fileToEmpty]" />
	{foreach from=$logs item='log' key='k'}
	<p>
		<b>{$k}</b>:
		{if !empty($log) && !empty($log[0])}
		<ul>
		{foreach from=$log item='logrow' key='kk'}
			<li>{$logrow}</li>
		{/foreach}
		</ul>
		<span>...</span>
		<br/><input type="button" value="{t}Empty file{/t}" class="emptyFile" title="{$k}" />
		{else}
			<br/>{t}Empty file{/t}
		{/if}
	</p>
	<hr/>
	{/foreach}
{else}
	{t}No log file found{/t}
{/if}
</div>
</fieldset>

</form>