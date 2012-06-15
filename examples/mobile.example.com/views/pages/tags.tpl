<div data-role="page">

	{$view->element('header')}

	<div data-role="content">

		<ul class="tags">
			{foreach from=$listTags item="tag"}
				<li><a href="{$html->url('/tag/')}{$tag.name}" title="{$tag.label}">{$tag.label}</a></li>
			{/foreach}
		</ul>
		
	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->