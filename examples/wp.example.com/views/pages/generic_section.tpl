<div id="container">
	<div id="content" role="main">

	{if !empty($section.contentRequested) || (!empty($section.childContents) && count($section.childContents) == 1 && $section.toolbar.dim > 1)}
		<div id="post-{$section.currentContent.id}" class="post-{$section.currentContent.id} page type-page hentry">
			<h1 class="entry-title">{$section.currentContent.title}</h1>

			<script type="text/javascript">
			{literal}
			$(document).ready(function() {
				$("a[rel='gallery']").colorbox();
			});
			{/literal}
			</script>

			{assign var="author" value=$section.currentContent.UserCreated.realname|default:$section.currentContent.UserCreated.userid}
			<div class="entry-meta">
				<span class="meta-prep meta-prep-author">Posted on</span> <span class="entry-date">{$section.currentContent.publication_date|date_format:"%B %e, %Y"}</span>
				{if !empty($author)}
					<span class="meta-sep">by</span>
					<span class="author vcard"><a class="url fn n" href="{$html->url('/search/user_created:')}{$section.currentContent.UserCreated.id}" title="View all posts by ">{$author}</a></span>
				{/if}
			</div><!-- .entry-meta -->

			<div class="entry-content">
				{$section.currentContent.body}
				{if !empty($section.currentContent.relations.attach)}
					{assign_associative var="options" mode="fill" width=100 height=100 modeparam="000000" upscale=true}
					{assign_associative var="htmlAttr" style="float: left; width: 100px; height: 100px; margin: 10px 20px 10px 0"}
					{assign_associative var="optionsBig" mode="fill" longside=600 URLonly=true}
					{foreach from=$section.currentContent.relations.attach item="attach"}
						<a rel="gallery" title="{$attach.title}" href="{$beEmbedMedia->object($attach, $optionsBig)}">{$beEmbedMedia->object($attach, $options, $htmlAttr)}</a>
					{/foreach}
				{/if}
			</div><!-- .entry-content -->

			<div class="entry-utility">
				{if !empty($section.currentContent.Category)}
				<span class="cat-links">
					<span class="entry-utility-prep entry-utility-prep-cat-links">Posted in</span> 
					{foreach from=$section.currentContent.Category item="cat" name="fccat"}
						{$cat.label}{if !$smarty.foreach.fccat.last},{/if}
					{/foreach}
				</span>
				<span class="meta-sep">|</span>
				{/if}

				{if !empty($section.currentContent.Tag)}
				<span class="tag-links">
					<span class="entry-utility-prep entry-utility-prep-tag-links">Tagged</span>
					{foreach from=$section.currentContent.Tag item="tag" name="fctag"}
						<a href="{$html->url('/tag/')}{$tag.name|replace:" ":"+"}">{$tag.label}</a>{if !$smarty.foreach.fctag.last},{/if}
					{/foreach}
				</span>
				<span class="meta-sep">|</span>
				{/if}

				Bookmark the <a href="{$html->url($section.currentContent.canonicalPath)}" title="Permalink to {$section.currentContent.title}" rel="bookmark">permalink</a>.
			</div><!-- .entry-utility -->

		</div><!-- #post-## -->

		<div id="nav-below" class="navigation">
			{if !empty($previousItem)}
				<div class="nav-previous"><a href="{$html->url($previousItem.canonicalPath)}" ><span class="meta-nav">&larr;</span> {$previousItem.title}</a>
				</div>
			{/if}

			{if !empty($nextItem)}
				<div class="nav-next"><a href="{$html->url($nextItem.canonicalPath)}" >{$nextItem.title} <span class="meta-nav">&rarr;</span></a>
				</div>
			{/if}
		</div><!-- #nav-below -->

		<div id="comments">
			{$view->element("show_comments")}
		</div><!-- #comments -->


	<!-- list of items -->
	{elseif !empty($section.childContents)}

		{assign_associative var="options" items=$section.childContents toolbar=$section.toolbar}
		{$view->element("list_items", $options)}
		
	{else}
		<p>No items yet.</p>
	{/if}

	</div><!-- #content -->

</div><!-- #container -->

{$view->element('right_column')}


	

