<h2>{t}Contents with more comments (first 20){/t}</h2>

{if !empty($contentCommented)}
<table class="graph">
    {foreach from=$contentCommented item="c"}
    {math assign="pixel" equation="(x/y)*350" x=$c.count_relations y=$maxContentCommented}
    {if isset($c.ObjectType.module_name)}
    <tr>
        <td class="label">{$c.title|escape|truncate:20|default:'<i>[no title]</i>'}</td>
        <td style="white-space:nowrap;">
            <div style="width:{$pixel|format_number}px;" class="bar {$c.ObjectType.module_name}">&nbsp;</div><span class="value">{$c.count_relations}</span>
        </td>
    </tr>
    {/if}
    {/foreach}
</table>
{else}
    {t}None{/t}
{/if}

<hr />
