
{foreach from=$objects item="obj"}
{if $contentTemplate}
	<div style="clear: both">
	{if !empty($obj.relations.attach)}
		{assign_associative var="params" presentation="thumb" width=96 height=96 mode="fill" upscale=false URLonly=1}
		{assign_concat var="src" 0='src="' 1=$beEmbedMedia->object($obj.relations.attach.0,$params) 2='"'}
		{assign var="content" value=$contentTemplate|regex_replace:'/src="[\S]*?"/':$src}
	{else}
		{assign var="content" value=$contentTemplate|regex_replace:"/\<img.[\S\s]*?\>/":""}
	{/if}
	
	{assign var="content" value=$content|replace:"[\$title]":$obj.title}
	
	{assign var="descriptionTruncated" value=$obj.description|html_substr:$descriptionTruncateNumber:"..."}
	{assign_concat var="regexp" 0="/\[" 1="\\$" 2="description.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$descriptionTruncated}
	
	{assign var="abstractTruncated" value=$obj.abstract|html_substr:$abstractTruncateNumber:"..."}
	{assign_concat var="regexp" 0="/\[" 1="\\$" 2="abstract.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$abstractTruncated}
	
	{assign var="bodyTruncated" value=$obj.body|html_substr:$bodyTruncateNumber:"..."}
	{assign_concat var="regexp" 0="/\[" 1="\\$" 2="body.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$bodyTruncated}
	
	{$content}
	</div>
	
{else}
	{strip}
	{if !empty($obj.relations.attach.0)}
		{assign_associative var="params" presentation="thumb" width=96 height=96 mode="fill" upscale=false}
		{assign_associative var="htmlAttr" width=96 height=96 style="float:left;margin:0px 20px 20px 0px;"}
		{$beEmbedMedia->object($obj.relations.attach.0,$params,$htmlAttr)}
	{/if}
	<h2>{$obj.title}</h2>
	{if !empty($obj.description)}
		<h3>{$obj.description}</h3>
	{/if}
	{if !empty($obj.body)}{$obj.body|html_substr:128:"..."}{/if}
	<hr style="clear:both" />
	{/strip}
{/if}
{/foreach}

