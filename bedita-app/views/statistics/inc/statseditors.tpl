<div class="tab"><h2>{t}BEdita editors activity statistics{/t}</h2></div>
	<div id="statseditors">	

		<h2>{t}Objects production by editor{/t}</h2>

		<table class="graph sortableTable">
			<thead>
				<th style="text-align:right"><b>editor</b></th>
				<th><b>contents</b></th>
			</thead>
			<tbody>
			{foreach from=$objectsForUser key="user_id" item="user"}
			<tr>
				<td class="label" onclick="window.location.href='/admin/viewUser/{$user.id}'" style="cursor:pointer; border-right:1px solid gray; border-bottom:0px solid gray">
					{$user.realname}
				</td>
			<td style="text-align:right; text-align:center; border-right:1px solid gray; border-bottom:0px solid gray"> {$totalObjectsForUser[$user_id]} </td>
				<td style="white-space:nowrap;">
				{foreach from=$user.objects key="objectType" item="num"}
					{if isset($conf->objectTypes[$objectType].module)}
					{math assign="pixel" equation="(x/y)*350" x=$num y=$maxObjectsForUser}
					<div style="width:{$pixel}px;" class="{$conf->objectTypes[$objectType].module}">&nbsp</div>
					{/if}
				{/foreach}
					{*<span class="value">{$totalObjectsForUser[$user_id]}</span>*}
				</td>
			</tr>
			{/foreach}
			</tbody>
		</table>


	</div>