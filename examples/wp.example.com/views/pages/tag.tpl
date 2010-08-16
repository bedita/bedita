<div id="container">
	<div id="content" role="main">

	<h1 class="page-title">Tag Archives: <span>{$tag.label}</span></h1>

	{if !empty($tag.items)}

		{assign_associative var="options" items=$tag.items toolbar=$tag.toolbar}
		{$view->element("list_items", $options)}

	{else}
		<p>No items found.</p>
	{/if}

	</div><!-- #content -->

</div><!-- #container -->

{$view->element('right_column')}