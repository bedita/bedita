<div data-role="page">

	{$view->element('header')}

	<div data-role="content">

		<h1 style="margin-bottom:30px;">{t}Objects tagged by{/t} "{$tag.label}"</h1>

		{if !empty($tag.items)}
			<ul data-role="listview">
			{foreach from=$tag.items item="object"}
				<li><a href="{$html->url('/')}{$object.nickname}">{$object.title}</a></li>
			{/foreach}
			</ul>
		{/if}

	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->