{foreach from=$items item="c"}
	<div id="post-{$c.id}" class="post-{$c.id} post type-post hentry category-uncategorized">

		{if !empty($section)}
			{assign var="baseUrl" value=$c.canonicalPath}
		{else}
			{assign_concat var="baseUrl" 1="/" 2=$c.nickname}
		{/if}

		<h1 class="entry-title"><a href="{$html->url($baseUrl)}" title="Permalink to {$c.title}" rel="bookmark">{$c.title}</a></h1>

		{assign var="author" value=$c.UserCreated.realname|default:$c.UserCreated.userid}
		<div class="entry-meta">
			<span class="meta-prep meta-prep-author">Posted on</span> <span class="entry-date">{$c.publication_date|date_format:"%B %e, %Y"}</span>
			{if !empty($author)}
				<span class="meta-sep">by</span>
				<span class="author vcard"><a class="url fn n" href="{$html->url('/authorItems/')}{$c.UserCreated.id}" title="View all posts by {$author}">{$author}</a></span>
			{/if}
		</div><!-- .entry-meta -->

		<div class="entry-content">
			{assign_concat var="moreAnchor" 1='<a href="' 2=$html->url($baseUrl) 3='" class="more-link">Continue reading <span class="meta-nav">&rarr;</span></a>'}
			{$c.body|html_substr:200:$moreAnchor}
		</div><!-- .entry-content -->

		<div class="entry-utility">
			{if !empty($c.Category)}
			<span class="cat-links">
				<span class="entry-utility-prep entry-utility-prep-cat-links">Posted in</span>
				{foreach from=$c.Category item="cat" name="fccat"}
					{$cat.label}{if !$smarty.foreach.fccat.last},{/if}
				{/foreach}
			</span>
			<span class="meta-sep">|</span>
			{/if}

			{if !empty($c.Tag)}
			<span class="tag-links">
				<span class="entry-utility-prep entry-utility-prep-tag-links">Tagged</span>
				{foreach from=$c.Tag item="tag" name="fctag"}
					<a href="{$html->url('/tag/')}{$tag.name|replace:" ":"+"}">{$tag.label}</a>{if !$smarty.foreach.fctag.last},{/if}
				{/foreach}
			</span>
			<span class="meta-sep">|</span>
			{/if}

			{if $c.comments != "off"}
			<span class="comments-link"><a href="{$html->url($baseUrl)}#comment" title="Comment on ciao mondo!">Leave a comment</a></span>
			{/if}
		</div><!-- .entry-utility -->

	</div><!-- #post-## -->

{/foreach}

<div id="nav-below" class="navigation">
	{if !empty($section)}
		{if $toolbar.next != 0}
			<div class="nav-previous">
				<a href="{$html->url($section.canonicalPath)}/page:{$section.toolbar.next}" ><span class="meta-nav">&larr;</span> Older posts</a>
			</div>
		{/if}

		{if $section.toolbar.prev != 0}
			<div class="nav-next">
				<a href="{$html->url($section.canonicalPath)}/page:{$section.toolbar.prev}" >Newer posts <span class="meta-nav">&rarr;</span></a>
			</div>
		{/if}
	{else}
		{if $toolbar.prev != 0}
		<div class="nav-previous">
			<a href="{$html->url('/search')}/query:{$stringSearched}/page:{$toolbar.prev}" ><span class="meta-nav">&larr;</span> Previous results</a>
		</div>
		{/if}

		{if $toolbar.next != 0}
			<div class="nav-next">
				<a href="{$html->url('/search')}/query:{$stringSearched}/page:{$toolbar.next}" >Next results <span class="meta-nav">&rarr;</span></a>
			</div>
		{/if}
	{/if}
</div><!-- #nav-below -->