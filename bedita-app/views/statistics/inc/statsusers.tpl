
<div class="tab"><h2>{t}Public sites users statistics{/t}</h2></div>
	<div id="users">
			
	{foreach from=$publications item="pub"}
		{if !empty($pub.stats_provider) }
		<label style="display:block;">{$pub.stats_provider}</label>

		<textarea class="shortdesc autogrowarea" 
		style="font-size:0.8em; color:gray; padding-top:40px; display:block; margin:0px 0px 0px 10px; width:470px; background: white url('{$html->url('/img/')}googleanalyticslogo.gif') no-repeat">
		{$pub.stats_code|default:''}
		</textarea>
	
		<a href="{$pub.stats_provider_url|default:'#'}" target="_blank">
			› {t}access {/t}{$pub.stats_provider}
		</a>
		<br>
		{if isset($conf->logStatsUrl[$pub.nickname])}
		<a href="{$conf->logStatsUrl[$pub.nickname]}" target="_blank">
			› {t}access server log statistics{/t}
		</a>
		{else}
		{t}No server log statistics defined{/t}
		{/if}
		<br>
		{else}
		{t}No statistics provider defined for {/t} {$pub.title}
		{/if}
		<hr />
	{/foreach}
		{*		
		<embed type="application/x-shockwave-flash" 
		src="http://piwik.org/demo/libs/open-flash-chart/open-flash-chart.swf" style="" 
		id="VisitsSummarygetLastVisitsGraphChart_swf" name="VisitsSummarygetLastVisitsGraphChart_swf" 
		bgcolor="transparent" quality="high" allowscriptaccess="sameDomain" 
		wmode="transparent" 
		flashvars="data=http%3A%2F%2Fpiwik.org%2Fdemo%2Findex.php%3Fmodule%3DVisitsSummary%26action%3DgetLastVisitsGraph%26idSite%3D1%26period%3Dday%26date%3D2008-10-31%252C2008-11-29%26viewDataTable%3DgenerateDataChartEvolution" 
		height="150" width="100%">
		*}

	</div>
	
