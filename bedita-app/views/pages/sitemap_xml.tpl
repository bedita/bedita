<?xml version='1.0' encoding='UTF-8'?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9"
	url="http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
	xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$urlset item=s_url}
	{if !isset($s_url.menu) || $s_url.menu != '0'}
	<url>
		<loc>{$s_url.loc}</loc>
		{if !empty($s_url.lastmod)}<lastmod>{$s_url.lastmod}</lastmod>{/if}
		{if !empty($s_url.changefreq)}<changefreq>{$s_url.changefreq}</changefreq>{/if}
		{if !empty($s_url.priority)}<priority>{$s_url.priority}</priority>{/if}
	</url>
	{/if}
{/foreach}
</urlset>