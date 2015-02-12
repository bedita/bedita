<script type="text/javascript">
<!--

	$(document).ready(function() {
		$("#{$idForm} .emptyFile").each(function(){
			$(this).bind("click", function() {
				emptyFile(this);
			});
		});
		$("#{$idForm} .refreshFile").each(function(){
			$(this).bind("click", function() {
				refreshFile(this);
			});
		});
		$("#{$idForm} .refreshFileAuto").each(function(){
			$(this).bind("click", function() {
				updateInterval(this);
			});
		});
	} );
//-->
</script>

<form method="post" action="" id="{$idForm}">
{$beForm->csrf()}

<div class="tab"><h2>{t}{$titleTab}{/t}</h2></div>

<fieldset class="system_logs">
<div>
{if !empty($logs)}
	<input type="hidden" id="fileToEmpty" name="data[fileToEmpty]" />
	<input type="hidden" id="fileToRefresh" name="data[fileToRefresh]" />
	{foreach from=$logs item='log' key='k' name='l'}
	<p>
		<b>{$k}</b>:
		<br/><span>...</span>
		<div id="log_{$type}_{$smarty.foreach.l.index}">
		{include file="../refresh_file.tpl" log=$log}
		</div>
		<hr/>
		<input type="button" value="{t}Empty file{/t}" class="emptyFile" title="{$k}" />
		<input type="button" value="{t}Refresh file{/t}" class="refreshFile" title="{$k}" index="log_{$type}_{$smarty.foreach.l.index}" id="{$type}_{$smarty.foreach.l.index}" />
		<input type="checkbox" id="autoupdate_{$type}_{$smarty.foreach.l.index}" class="refreshFileAuto" index="{$type}_{$smarty.foreach.l.index}" /> {t}Autoupdate{/t}
	</p>
	<hr/>
	{/foreach}
{else}
	{t}No log file found{/t}
{/if}
</div>
</fieldset>

</form>