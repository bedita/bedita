
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
	
	{if !empty($public_url)}
		{assign_concat var="openAtag" 0="<a href=\"" 1=$public_url 2="/" 3=$obj.nickname 4="\">"}
		{assign var="closeAtag" value="</a>"}
		{assign_concat var="objTitle" 0=$openAtag 1=$obj.title 2=$closeAtag}
		{assign_concat var="continueUrl" 0=$openAtag 1="[...]" 2=$closeAtag}
	{else}
		{assign var="objTitle" value=$obj.title}
		{assign var="continueUrl" value="..."}
	{/if}

	{assign var="content" value=$content|replace:"[\$title]":$objTitle|default:""}
	
	{assign var="descriptionTruncated" value=$obj.description|html_substr:$descriptionTruncateNumber:$continueUrl}
	{assign_concat var="regexp" 0="/\[" 1="\\$" 2="description.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$descriptionTruncated}
	
	{assign var="abstractTruncated" value=$obj.abstract|html_substr:$abstractTruncateNumber:$continueUrl}
	{assign_concat var="regexp" 0="/\[" 1="\\$" 2="abstract.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$abstractTruncated}
	
	{assign var="bodyTruncated" value=$obj.body|html_substr:$bodyTruncateNumber:$continueUrl}
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

