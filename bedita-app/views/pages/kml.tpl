<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://earth.google.com/kml/2.2">
	<Document id="{$section.id}">
		<Style id="BEdefault">
			<BalloonStyle>
			</BalloonStyle>
			<IconStyle><Icon><href>{$publication.public_url}/img/earth-point.png</href></Icon></IconStyle>
		</Style>
				
		<name>{$beFront->title()}</name>
		<open>1</open>
		{if !empty($publication.description)}<Snippet maxLines="2">{$publication.description|strip_tags:false}</Snippet>
		<description><![CDATA[{$publication.description}]]></description>{/if}

		<Folder>
			<name>{$section.title}</name>
  			<open>1</open>                        <!-- boolean -->
			<atom:link href="{$publication.public_url}{$section.canonicalPath|default:"link"}"/>            <!-- xmlns:atom -->
		{foreach from=$section.childContents item='item'}
		{if !empty($item.GeoTag.0.latitude) && !empty($item.GeoTag.0.longitude)}
			<Placemark id="{$item.nickname}">
				<name>{$item.title|strip_tags:false}</name>
				{if !empty($item.description)}<Snippet maxLines="2">{$item.description|strip_tags:false}</Snippet>{/if}
				<description>
					<![CDATA[
					{if !empty($item.description)}<b>{$item.description|strip}</b>{/if}
					{$item.body}
					{strip}{if !empty($item.relations.attach.0)}
						{assign_associative var="mediaParams" presentation="thumb" width="500" mode="fill" URLonly=0}
						{assign_associative var="htmlAttr" width="500"}
						{$beEmbedMedia->object($item.relations.attach.0, $mediaParams, $htmlAttr)}
						<p>{$item.relations.attach.0.title|escape|default:""} [{$conf->objectTypes[$item.relations.attach.0.object_type_id].name|escape|default:""}]</p>
					{/if}{/strip}
					]]>
				</description>
				<Point>
					<coordinates>{$item.GeoTag.0.longitude},{$item.GeoTag.0.latitude}</coordinates>
				</Point>
				<styleUrl>#BEdefault</styleUrl>
			</Placemark>
		{/if} {* !empty($item.GeoTag.0.latitude) && !empty($item.GeoTag.0.longitude) *}
		{/foreach}
		</Folder>

		<ScreenOverlay>
			<name>Logo</name>
			<Icon><href>{$publication.public_url}/img/earth-header.png</href></Icon>
			<overlayXY x="0.5" y="1" xunits="fraction" yunits="fraction" />
			<screenXY x="0.5" y="1" xunits="fraction" yunits="fraction" />
			<size x="0" y="0" xunits="fraction" yunits="fraction" />
		</ScreenOverlay>
	</Document>
</kml>



{*
Da aggiungere  in qualche tag

<updated>{$section.modified}</updated>
<generator uri="http://www.bedita.com/">Bedita</generator>
<entry>
	{if $publication.public_url}<link rel="alternate" type="text/html" href="{$publication.public_url}{$item.canonicalPath}" />{/if}
	<id>{$item.id}</id>
	<published>{$item.start_date|default:$item.created}</published>
	<updated>{$item.modified|default:$item.created}</updated>
	<author>
		<name>{$item.creator}</name>
		<uri></uri>
	</author>
	<link rel="enclosure" type="{$item.relations.attach.0.mime_type}" href="{$beEmbedMedia->object($item.relations.attach.0, $mediaParams)}" />
</entry>
*}