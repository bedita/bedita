<div data-role="page">

	{$view->element('header')}

	<div data-role="content">

		<ul data-role="listview">
		{foreach from=$topMenu item="child"}
			<li>
				<a href="{$html->url($child.canonicalPath)}">{$child.title}</a>
			</li>
		{/foreach}
		</ul>

		<ul data-role="listview" data-theme="a">
		{foreach from=$bottomMenu item="child"}
			<li>
				<a href="{$html->url($child.canonicalPath)}"><h1>{$child.title}</h1></a>
			</li>
		{/foreach}
		</ul>

	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->