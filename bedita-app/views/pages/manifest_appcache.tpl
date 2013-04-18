CACHE MANIFEST
# {$hash}

# Assets
{foreach from=$assets item=asset}
{$html->webroot($asset)}
{/foreach}

NETWORK:
*