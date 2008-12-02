{strip}
{foreach from=$objects item="obj"}
	<div style="clear: both">
	
	{if !empty($obj.relations.attach)}
		<div style="float:left;margin:0px 20px 20px 0px;">
		{$beEmbedMedia->object($obj.relations.attach.0,"96","96",false,"fill","000000",null,false)}
		</div>
	{/if}
	<h2>{$obj.title}</h2>
	{if !empty($obj.description)}
		<h3>{$obj.description}</h3>
	{/if}
	{if !empty($obj.body)}{$obj.body|html_substr:128:"..."}{/if}
	
	</div>
{/foreach}
{/strip}
