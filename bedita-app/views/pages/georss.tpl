{if !empty($atom)}
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dc="http://purl.org/dc/elements/1.1/"
      xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
      xmlns:georss="http://www.georss.org/georss">
<title>{$beFront->title()}</title>
<generator uri="http://www.bedita.com/">Bedita</generator>
<link rel="self" href="{$section.canonicalPath}" />

{assign var='entries' value=$items.0}
{foreach from=$entries item='entry'}
<entry>
	{if !empty($entry.title)}<title>{$entry.title|strip_tags}</title>{/if}
	{if !empty($entry.description)}<description>{$entry.description|strip_tags}</description>{/if}
	{if !empty($entry.updated)}<updated>{$entry.updated}</updated>{/if}
	{if !empty($entry.link)}<link rel="{$entry.link.rel}" type="{$entry.link.type}" href="{$entry.link.href}" />{/if}
	{if !empty($entry.content)}
	<content type="html">
	<p>
	<a href="{$entry.content.src}" title="{$entry.content.title}">
		<img src="{$entry.content.src}" alt="{$entry.content.alt}" />
	</a>
	</p>
	</content>
	<link rel="enclosure" type="image/jpeg" href="{$entry.content.src}" />
	{/if}
</entry>
{/foreach}
</feed>

{else}
{$rss->header()}
{assign_associative var='options' format='tags' slug='false'}
{assign var=channel value=$rss->channel(null,$channelData,$rss->serialize($items,$options))}
{$rss->document($attrib, $channel)}
{/if}