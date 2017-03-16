<div class="tab"><h2>{t}Public sites users statistics{/t}</h2></div>
<div id="users">
{if !empty($publications)}
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
{else}
    {t}None{/t}
{/if}
</div>