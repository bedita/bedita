<div data-role="page">

	{$view->element('header')}

	<div data-role="content">

		{if !empty($section.contentRequested)}

			{$view->element('content')}

		{elseif !empty($section.childContents)}

			<ul data-role="listview">
			{foreach from=$section.childContents item="child"}
				<li>
					<a href="{$html->url($child.canonicalPath)}">
						<h1>{$child.title}</h1>
						<p class="list_description">{$child.description}</p>
					</a>
				</li>
			{/foreach}
			</ul>

		{/if}

	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->

