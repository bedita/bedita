{*
<h3>{t}sections tree{/t}: $sectionsTree</h3>
<a href="javascript:void(0)" class="open-close-link">{t}show/hide{/t}</a>
<div style="display: none">
<pre>
{dump var=$sectionsTree}
</pre>
</div>

{if !empty($feedNames)}
<h3>{t}feeds available{/t}: $feedNames</h3>
<ul>
{foreach from=$feedNames item=feed}
	<li><a href="{$html->url('/rss')}/{$feed.nickname}">{$feed.title}</a></li>
{/foreach}
</ul>
{/if}
*}