<h3>sections tree: $sectionsTree</h3>
<a href="javascript:void(0)" class="open-close-link">open/close</a>
<div style="display: none">
<pre>
{dump var=$sectionsTree}
</pre>
</div>

{if !empty($feedNames)}
<h3>feeds available: $feedNames</h3>
<ul>
{foreach from=$feedNames item=feed}
	<li><a href="{$html->url('/rss')}/{$feed.nickname}">{$feed.title}</a></li>
{/foreach}
</ul>
{/if}
