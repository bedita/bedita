
<div class="tab"><h2>{t}Public sites users statistics{/t}</h2></div>
<div id="users">	
{foreach from=$publications item="pub"}
	{if !empty($pub.stats_provider)}
	<a href="{$pub.stats_provider_url|default:'#'}" target="_blank">
		› {t}access {/t}<strong>{$pub.stats_provider}</strong>
	</a>
	<hr />
	{/if}
	{if isset($conf->logStatsUrl[$pub.nickname])}
	<a href="{$conf->logStatsUrl[$pub.nickname]}" target="_blank">
		› {t}access server log statistics{/t}
	</a>
	<hr />
	{/if}		
{/foreach}
</div>
	
<div class="tab"><h2>{t}Users{/t}</h2></div>
<div id="regusers">

		<table class="graph sortableTable">
			<thead>
				<th style="text-align:right"><b>{t}group{/t}</b></th>
				<th><b>{t}users{/t}</b></th>
			</thead>
			<tbody>
			{foreach from=$groupstats item="item"}
				<tr>
					<td class="label">{$item.Group.name}</td>
					<td style="white-space:nowrap;">
						<div style="width:{$item.Group.userscount}px;" class="bar">&nbsp;</div> <span class="value">{$item.Group.userscount}</span>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
		
	{*{dump var=$groupstats}*}
	
	
	
</div>