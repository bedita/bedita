{if isset($moduleList.tags)}

{literal}
<script type="text/javascript">
<!--
$(document).ready(function(){
    initTagsAutocomplete('#object-tags');
});
//-->
</script>
{/literal}

{$relcount = $object.Tag|@count|default:0}
<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}Tags{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}</h2></div>
<fieldset id="tags">
    {$tags = ''}
    {foreach $object.Tag|default:[] as $tag}
        {$tags = $tags|cat:$tag.label}
        {if !$tag@last}
            {$tags = $tags|cat:','}
        {/if}
    {/foreach}
    <input
        type="hidden"
        name="tags"
        id="object-tags"
        rel="{$html->url('/tags/search')}"
        value="{$tags|escape}"
        data-placeholder="{t}add comma separated words{/t}" />
</fieldset>

{/if}
