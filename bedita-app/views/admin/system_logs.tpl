{$html->script("jquery/jquery.treeview", false)}
{$html->script("form", false)}

<script type="text/javascript">
	$(document).ready(function() { 
		var openAtStart =".system_logs";
		$(openAtStart).prev(".tab").BEtabstoggle();

		$("#rowLimit").change(function() { 
			var url = "{$html->url('systemLogs/')}" + $(this).attr("value");
			location.href = url;
		} );
	} );
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="systemLogs"}

{include file="inc/menucommands.tpl" method="systemLogs" fixed=true}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}System logs{/t}</h2>
		<label>Rows to show</label>:
		<select id="rowLimit" name="data[rowLimit]">
			<option value="10"{if $maxRows == '10'} selected="selected"{/if}>10</option>
			<option value="20"{if $maxRows == '20'} selected="selected"{/if}>20</option>
			<option value="50"{if $maxRows == '50'} selected="selected"{/if}>50</option>
			<option value="100"{if $maxRows == '100'} selected="selected"{/if}>100</option>
		</select>
	</div>
</div>

<div class="mainfull">

	{if !empty($backendLogs)}
	{include file="inc/form_logs.tpl" logs=$backendLogs titleTab='Backend Logs'}
	{/if}
	{if !empty($frontendLogs)}
	{include file="inc/form_logs.tpl" logs=$frontendLogs titleTab='Frontend Logs'}
	{/if}

</div>
