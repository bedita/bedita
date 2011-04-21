<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<feed xmlns="http://www.w3.org/2005/Atom"
	xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
	xmlns:georss="http://www.georss.org/georss">

<title>{$beFront->title()}</title>
<id>{$section.id}</id>
<subtitle>{$section.description}</subtitle>
<updated>{$section.modified}</updated>
<generator uri="http://www.bedita.com/">Bedita</generator>
{foreach from=$section.childContents item='item'}
{if !empty($item.GeoTag.0.latitude) && !empty($item.GeoTag.0.longitude)}

<entry>
	<title>{$item.title}</title>
	<link rel="alternate" type="text/html" href="{$html->url('/')}{$item.canonicalPath}" />
	<id>{$item.id}</id>
	<published>{$item.start_date|default:$item.created}</published>
	<updated>{$item.modified|default:$item.created}</updated>
	<content type="html">
	{$item.body|escape}
	{strip}{if !empty($item.relations.attach.0)}
		{if strpos($item.relations.attach.0.uri,'/') === 0}
			{assign_concat var="fileUrl"  0=$conf->mediaUrl  1=$item.relations.attach.0.uri}
		{else}
			{assign var="fileUrl"  value=$item.relations.attach.0.uri}
		{/if}
		&lt;img src="{$fileUrl}" alt="{$item.relations.attach.0.title|default:""}" width="600" /&gt;
	{/if}{/strip}
	</content>
	{if $item.creator}
	<author>
		<name>{$item.creator}</name>
		<uri></uri>
	</author>
	{/if}
	{strip}{if !empty($item.relations.attach.0)}
		{if strpos($item.relations.attach.0.uri,'/') === 0}
			{assign_concat var="fileUrl"  0=$conf->mediaUrl  1=$item.relations.attach.0.uri}
		{else}
			{assign var="fileUrl"  value=$item.relations.attach.0.uri}
		{/if}
		<link rel="enclosure" type="{$item.relations.attach.0.mime_type}" href="{$fileUrl}" />
	{/if}{/strip}
	<georss:point>{$item.GeoTag.0.latitude} {$item.GeoTag.0.longitude}</georss:point>
	<geo:lat>{$item.GeoTag.0.latitude}</geo:lat>
	<geo:long>{$item.GeoTag.0.longitude}</geo:long>
</entry>
{/if}
{/foreach}

</feed>