{if !empty($totalObjectsNumber)}
<h2>{t}Number of objects{/t}</h2>

<table class="graph">
	{foreach from=$totalObjectsNumber key="objectType" item="num" name="fctotal"}
		{if $num > 0} 
		{if isset($conf->objectTypes[$objectType].module_name)}
		<tr>
			<td class="label">{$objectType}</td>
			<td style="white-space:nowrap;">
				{math assign="pixel" equation="(x/y)*400" x=$num y=$maxTotalObjectsNumber}
				<div title="{$objectType}" style="width:{$pixel|format_number}px;" class="bar {$objectType}">&nbsp;</div> <span class="value">{$num}</span>
			</td>
		</tr>
		{/if}
		{/if}
	{/foreach}
</table>

<hr />
{/if}
