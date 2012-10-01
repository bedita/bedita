<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<feed xmlns="http://www.w3.org/2005/Atom"
	xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
	xmlns:georss="http://www.georss.org/georss">

<title>{$this->BeFront->title()}</title>
<id>{$section.id}</id>
<subtitle>{$section.description}</subtitle>
<updated>{$section.modified}</updated>
<generator uri="http://www.bedita.com/">Bedita</generator>
{foreach from=$section.childContents item='item'}
{if !empty($item.GeoTag.0.latitude) && !empty($item.GeoTag.0.longitude)}
<entry>
	<title>{$item.title}</title>
	{if $publication.public_url}<link rel="alternate" type="text/html" href="{$publication.public_url}{$item.canonicalPath}" />
{/if}
	<id>{$item.id}</id>
	<published>{$item.start_date|default:$item.created}</published>
	<updated>{$item.modified|default:$item.created}</updated>
	<content type="html">
	{$item.body|escape}
		{strip}{if !empty($item.relations.attach.0)}
		{assign_associative var="mediaParams" presentation="thumb" width="500" mode="fill" URLonly=0}
		{assign_associative var="htmlAttr" width="500"}
		{$this->BeEmbedMedia->object($item.relations.attach.0, $mediaParams, $htmlAttr)|escape}
		&lt;p&gt;{$item.relations.attach.0.title|escape|default:""} [{$conf->objectTypes[$item.relations.attach.0.object_type_id].name|escape|default:""}]&lt;/p&gt;
	{/if}{/strip}
	</content>
	{if $item.creator}
	<author>
		<name>{$item.creator}</name>
		<uri></uri>
	</author>
	{/if}
	{strip}{if !empty($item.relations.attach.0)}
		{assign_associative var="mediaParams" presentation="link" URLonly=1}
		<link rel="enclosure" type="{$item.relations.attach.0.mime_type}" href="{$this->BeEmbedMedia->object($item.relations.attach.0, $mediaParams)}" />
	{/if}{/strip}
	<georss:point>{$item.GeoTag.0.latitude} {$item.GeoTag.0.longitude}</georss:point>
	<geo:lat>{$item.GeoTag.0.latitude}</geo:lat>
	<geo:long>{$item.GeoTag.0.longitude}</geo:long>
</entry>
{/if}
{/foreach}
</feed>