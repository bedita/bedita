{$html->script("form", false)}

<script type="text/javascript">

	var message = "{t}Are you sure that you want to empty the file?{/t}" ;
	var urlEmptyFile = "{$html->url('emptyFile/')}";
	var urlRefreshFile = "{$html->url('refreshFile/')}";
	var intervalsAutoupdate = new Array();

	function emptyFile(elem) {
		if(!confirm(message)) { 
			return false ;
		} 
		var fileToEmpty = $(elem).attr("title");
		var form = $(elem).parents('form:first');
		$("#fileToEmpty").attr("value",fileToEmpty);
		form.attr("action", urlEmptyFile) ;
		form.submit() ;
	}

	function refreshFile(elem) {
		var fileToRefresh = $(elem).attr("title");
		var ajaxResultId = $(elem).attr("index");
		$("#" + ajaxResultId).empty();
		$("#" + ajaxResultId).addClass('loader');
		$("#" + ajaxResultId).show();
		var rowLimit = $("#rowLimit").attr("value");
		$("#" + ajaxResultId).load(urlRefreshFile,{ 'fileToRefresh':fileToRefresh, 'rowLimit': rowLimit }, function() {
			$("#" + ajaxResultId).removeClass('loader');
		});
	}

	function updateInterval(elem) {
		var index = $(elem).attr("index");
		if($(elem).attr("checked")) {
			intervalsAutoupdate[index] = setInterval(function(){ $("#"+index).click(); },3000);
		} else {
			clearInterval(intervalsAutoupdate[index]);
		}
	}

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
		<label>{t}Rows to show{/t}</label>:
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
	{include file="inc/form_logs.tpl" logs=$backendLogs titleTab='Backend Logs' type='backend' idForm='backendForm'}
	{/if}
	{if !empty($frontendLogs)}
	{include file="inc/form_logs.tpl" logs=$frontendLogs titleTab='Frontend Logs' type='frontend' idForm='frontendForm'}
	{/if}

</div>
