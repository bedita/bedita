
{foreach from=$objects item="obj"}
{if $contentTemplate}
	<div style="clear: both">
	{if $obj.object_type_id == $conf->objectTypes.image.id}
		{assign_associative var="params" presentation="thumb" width=96 height=96 mode="crop" upscale=false URLonly=1}
		{assign_concat var="src" 1='src="' 2=$this->BeEmbedMedia->object($obj,$params) 3='"'}
		{assign var="content" value=$contentTemplate|regex_replace:'/src="[\S]*?"/':$src}
	{elseif $obj.object_type_id == $conf->objectTypes.video.id}
		{assign_associative var="params" presentation="thumb" height=96 URLonly=1}
		{assign_associative var="htmlAttr" height=96}
		{assign_concat var="src" 1='src="' 2=$this->BeEmbedMedia->object($obj,$params,$htmlAttr) 3='"'}
		{assign var="content" value=$contentTemplate|regex_replace:'/src="[\S]*?"/':$src}
	{elseif !empty($obj.relations.attach)}
		{assign_associative var="params" presentation="thumb" width=96 height=96 mode="crop" upscale=false URLonly=1}
		{assign_concat var="src" 1='src="' 2=$this->BeEmbedMedia->object($obj.relations.attach.0,$params) 3='"'}
		{assign var="content" value=$contentTemplate|regex_replace:'/src="[\S]*?"/':$src}
	{else}
		{assign var="content" value=$contentTemplate|regex_replace:"/\<img.[\S\s]*?\>/":""}
	{/if}
	
	{if !empty($public_url)}
		{assign_concat var="openAtag" 1="<a href=\"" 2=$public_url 3="/" 4=$obj.nickname 5="\">"}
		{assign var="closeAtag" value="</a>"}
		{assign_concat var="objTitle" 1=$openAtag 2=$obj.title 3=$closeAtag}
		{assign_concat var="continueUrl" 1=$openAtag 2="[...]" 3=$closeAtag}
	{else}
		{assign var="objTitle" value=$obj.title}
		{assign var="continueUrl" value="..."}
	{/if}

	{assign var="content" value=$content|replace:"[\$title]":$objTitle|default:""}
	
	{assign var="descriptionTruncated" value=$obj.description|html_substr:$descriptionTruncateNumber:$continueUrl}
	{assign_concat var="regexp" 1="/\[" 2="\\$" 3="description.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$descriptionTruncated}
	
	{assign var="abstractTruncated" value=$obj.abstract|html_substr:$abstractTruncateNumber:$continueUrl}
	{assign_concat var="regexp" 1="/\[" 2="\\$" 3="abstract.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$abstractTruncated}
	
	{assign var="bodyTruncated" value=$obj.body|html_substr:$bodyTruncateNumber:$continueUrl}
	{assign_concat var="regexp" 1="/\[" 2="\\$" 3="body.*\]/"}
	{assign var="content" value=$content|regex_replace:$regexp:$bodyTruncated}
	
	{$content}
	</div>
	
{else}
	{strip}
	{if $obj.object_type_id == $conf->objectTypes.image.id}
		{assign_associative var="params" presentation="thumb" width=96 height=96 mode="fill"}
		{assign_associative var="htmlAttr" width=96 height=96 style="float:left;margin:0px 20px 20px 0px;"}
		{$this->BeEmbedMedia->object($obj,$params,$htmlAttr)}
	{elseif $obj.object_type_id == $conf->objectTypes.video.id}
		{assign_associative var="params" presentation="thumb" height=96}
		{assign_associative var="htmlAttr" height=96 style="float:left;margin:0px 20px 20px 0px;"}
		{$this->BeEmbedMedia->object($obj,$params,$htmlAttr)}
	{elseif !empty($obj.relations.attach.0)}
		{assign_associative var="params" presentation="thumb" width=96 height=96 mode="fill" upscale=false}
		{assign_associative var="htmlAttr" width=96 height=96 style="float:left;margin:0px 20px 20px 0px;"}
		{$this->BeEmbedMedia->object($obj.relations.attach.0,$params,$htmlAttr)}
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

