<div id="primary" class="widget-area" role="complementary">
	<ul class="xoxo">

		<li id="search-2" class="widget-container widget_search">
			{$view->element("form_search")}
		</li>

		{if (count($section.childContents) == 1 ||  !empty($section.contentRequested)) && !empty($section.currentContent.relations.seealso)}
		<li id="seealso_list" class="widget-container">
			<h3 class="widget-title">See also</h3>
			{foreach from=$section.currentContent.relations.seealso item="seealso"}
				<a title="{$seealso.title}" href="{$html->url('/')}{$seealso.nickname}">
				{$seealso.title}
				</a>
			{/foreach}
		</li>
		{/if}

		{if (count($section.childContents) == 1 || !empty($section.contentRequested)) && !empty($section.currentContent.relations.download)}
		<li id="download_list" class="widget-container">
			<h3 class="widget-title">Download</h3>
			{foreach from=$section.currentContent.relations.download item="download"}
				<a title="{$download.title}" href="{$html->url('/download/')}{$download.nickname}">
				{$download.title}
				</a>
			{/foreach}
		</li>
		{/if}

		{if (count($section.childContents) == 1 || !empty($section.contentRequested)) && !empty($section.currentContent.relations.link)}
		<li id="seealso_list" class="widget-container">
			<h3 class="widget-title">Link a risorse esterne</h3>
			{foreach from=$section.currentContent.relations.link item="link"}
				<a title="{$link.title}" href="{$link.url}" terget=="_blank">
				{$link.title}
				</a>
			{/foreach}
		</li>
		{/if}

		{if !empty($listTags)}
		<li id="tag_cloud" class="widget-container">
			<h3 class="widget-title">Tag cloud</h3>
			{foreach from=$listTags item="tag"}
				<a title="{$tag.weight}" class="{$tag.class|default:""}" href="{$html->url('/tag/')}{$tag.name|replace:' ':'+'}">
				{$tag.label}
				</a>
			{/foreach}
		</li>
		{/if}




{*
		<li id="recent-posts-2" class="widget-container widget_recent_entries">
			<h3 class="widget-title">Recent Posts</h3>
			<ul>
				<li><a href="http://localhost/workspace/wordpress/?p=1" title="Hello world!">Hello world!</a></li>
			</ul>
		</li>

		<li id="recent-comments-2" class="widget-container widget_recent_comments">
			<h3 class="widget-title">Recent Comments</h3>
			<ul id="recentcomments">
				<li class="recentcomments"><a href='http://wordpress.org/' rel='external nofollow' class='url'>Mr WordPress</a> on <a href="http://localhost/workspace/wordpress/?p=1#comment-1">Hello world!</a></li>
			</ul>
		</li>

		<li id="archives-2" class="widget-container widget_archive">
			<h3 class="widget-title">Archives</h3>		
			<ul>
				<li><a href='http://localhost/workspace/wordpress/?m=201008' title='August 2010'>August 2010</a></li>
			</ul>
		</li>

		<li id="categories-2" class="widget-container widget_categories">
			<h3 class="widget-title">Categories</h3>		
			<ul>
				<li class="cat-item cat-item-1"><a href="http://localhost/workspace/wordpress/?cat=1" title="View all posts filed under Uncategorized">Uncategorized</a>
				</li>
			</ul>
		</li>

		<li id="meta-2" class="widget-container widget_meta">
			<h3 class="widget-title">Meta</h3>			
			<ul>
				<li><a href="http://localhost/workspace/wordpress/wp-admin/">Site Admin</a></li>			
				<li><a href="http://localhost/workspace/wordpress/wp-login.php?action=logout&amp;_wpnonce=979ea292cf">Log out</a></li>
				<li><a href="http://localhost/workspace/wordpress/?feed=rss2" title="Syndicate this site using RSS 2.0">Entries <abbr title="Really Simple Syndication">RSS</abbr></a>
				</li>
				<li><a href="http://localhost/workspace/wordpress/?feed=comments-rss2" title="The latest comments to all posts in RSS">Comments <abbr title="Really Simple Syndication">RSS</abbr></a></li>
				<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress.org</a></li>
			</ul>
		</li>
*}
	</ul>

</div><!-- #primary .widget-area -->
