{foreach from=$objects item="obj"}

{$obj.title}
{if !empty($obj.description)}
{$obj.description}
{/if}
{if !empty($obj.body)}{$obj.body|strip_tags:false|truncate:128}{/if}
{/foreach}
{php}exit;{/php}