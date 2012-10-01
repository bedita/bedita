{foreach from=$objects item="obj"}
{if $contentTemplate}
{strip}
{assign var="content" value=$contentTemplate|replace:"[\$title]":$obj.title}
{assign var="content" value=$content|replace:"[\$description]":$obj.description}
{assign var="descriptionTruncated" value=$obj.description|strip_tags:false|truncate:$descriptionTruncateNumber:"..."}
{assign_concat var="regexp" 1="/\[" 2="\\$" 3="description.*\]/"}
{assign var="content" value=$content|regex_replace:$regexp:$descriptionTruncated}
{assign var="abstractTruncated" value=$obj.abstract|strip_tags:false|truncate:$abstractTruncateNumber:"..."}
{assign_concat var="regexp" 1="/\[" 2="\\$" 3="abstract.*\]/"}
{assign var="content" value=$content|regex_replace:$regexp:$abstractTruncated}
{assign var="bodyTruncated" value=$obj.body|strip_tags:false|truncate:$bodyTruncateNumber:"..."}
{assign_concat var="regexp" 1="/\[" 2="\\$" 3="body.*\]/"}
{assign var="content" value=$content|regex_replace:$regexp:$bodyTruncated}
{$content}
{if !empty($public_url)}
{t}Full news at{/t} {$public_url}/{$obj.nickname}
{/if}
{/strip}
{else}

{$obj.title}
{if !empty($obj.description)}
{$obj.description}
{/if}
{if !empty($obj.body)}{$obj.body|strip_tags:false|truncate:128}{/if}
{/if}
{/foreach}