<h2>{t}Evolution of content production, during the time{/t}</h2>

{if !empty($timeEvolution)}
<table class="graph">
    {foreach from=$timeEvolution key="date" item="types" name=""}
    <tr>
        <td class="label">{$date|date_format:"%b %Y"}</td>
        <td style="white-space:nowrap;">
        {if !empty($types)}
        {foreach from=$types key="objectType" item="num"}
            {if isset($conf->objectTypes[$objectType].module_name)}
            {math assign="pixel" equation="(x/y)*400" x=$num y=$maxTotalTimeEvolution}
            <div title="{$objectType}" style="width:{$pixel|format_number}px;" class="bar {$objectType}">&nbsp;</div>
            {/if}
        {/foreach}
        {/if}
            <span class="value">{$totalTimeEvolution[$date]}</span>
        </td>
    </tr>
    {/foreach}
</table>
{else}
    {t}None{/t}
{/if}

<hr />