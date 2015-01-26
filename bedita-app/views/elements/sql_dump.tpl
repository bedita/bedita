{*
 *
 * SQL Dump element.  Dumps out SQL log information 
 *
 * cake/views/elements/sql_dump.ctp reported for Smarty
 *
*}
{if (!class_exists('ConnectionManager') || $conf->debug|default:0 < 2)}
	{* do nothing *}
{else}
	
	{if !isset($logs)}
		{$noLogs=true}
	{else}
		{$noLogs=false}
	{/if}
	
	{if $noLogs}
		{$sources=ConnectionManager::sourceList()}
		{$logs=[]}
		{foreach $sources as $source}
			{$db=ConnectionManager::getDataSource($source)}
			{if $db->isInterfaceSupported('getLog')}
				{$logs[$source]=$db->getLog()}
			{/if}
		{/foreach}
	{/if}
	
	{if $noLogs || isset($_forced_from_dbo_)}
		{foreach $logs as $source => $logInfo}
			{if $logInfo.count > 1}
				{$text='queries'}
			{else}
				{$text='query'}
			{/if}
			
			{$t=uniqid(time(), true)}
			<table class="cake-sql-log" id="cakeSqlLog_{$t|regex_replace:'/[^A-Za-z0-9_]/':'_'}" summary="Cake SQL Log" cellspacing="0" border = "0">
				<caption>({$source}) {$logInfo.count} {$text} took {$logInfo.time} ms</caption>
			
				<thead>
					<tr><th>Nr</th><th>Query</th><th>Error</th><th>Affected</th><th>Num. rows</th><th>Took (ms)</th></tr>
				</thead>
				<tbody>

				{foreach $logInfo.log as $k => $i}
					<tr>
						<td>{$k + 1}</td>
						<td>{$i.query|h}</td>
						<td> { {$i.error} } </td>
						<td style="text-align: right"> { {$i.affected} } </td>
						<td style="text-align: right"> { {$i.numRows} } </td>
						<td style="text-align: right"> { {$i.took} } </td>
					</tr>
				{/foreach}

				</tbody>
			</table>
		{/foreach}
	{else}
		<p>Encountered unexpected {$logs} cannot generate SQL log</p>
	{/if}
	
{/if}
