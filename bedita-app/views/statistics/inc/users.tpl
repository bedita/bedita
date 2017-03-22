<div class="tab"><h2>{t}Users{/t}</h2></div>
<div id="regusers">
	{if !empty($groupstats)}
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
	{else}
		{t}None{/t}
	{/if}
</div>
