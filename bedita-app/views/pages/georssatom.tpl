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
{if !empty($item.GeoTag.0.latitude)}
<entry>
	<title>{$item.title}</title>
	<link rel="alternate" type="text/html" href="{$html->url($item.canonicalPath)}"/>
	<id>{$item.id}</id>
	<updated>{$item.modified}</updated>
	<content type="html">
	{$item.body|htmlentities}
	</content>
	<georss:point>{$item.GeoTag.0.latitude} {$item.GeoTag.0.longitude}</georss:point>
	<geo:lat>{$item.GeoTag.0.latitude}</geo:lat>
	<geo:long>{$item.GeoTag.0.longitude}</geo:long>
</entry>
{/if}
{/foreach}

</feed>