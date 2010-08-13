<div id="container">
	<div id="content" role="main">

	<h1 class="page-title">Search Results for: <span>{$stringSearched}</span></h1>

	{if !empty($searchResult)}

		{assign_associative var="options" items=$searchResult.items toolbar=$searchResult.toolbar}
		{$view->element("list_items", $options)}

	{else}
		<p>No items found.</p>
	{/if}

	</div><!-- #content -->

</div><!-- #container -->

{$view->element('right_column')}