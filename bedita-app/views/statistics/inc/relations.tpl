
<h2>{t}Objects with multiple relationships{/t}</h2>

{if !empty($relatedObject)}
<table class="graph">
    {foreach from=$relatedObject item="c"}
    {math assign="pixel" equation="(x/y)*350" x=$c.count_relations y=$maxRelatedObject}
    {if isset($c.ObjectType.module_name)}
    <tr>
        <td class="label">
            <a href="{$html->url('/')}{$c.ObjectType.module_name}/view/{$c.id}">
            {$c.title|escape|truncate:20|default:'<i>[no title]</i>'}</a>
        </td>
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
