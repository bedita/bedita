<div id="container">
	<div id="content" role="main">

	{strip}
	<h1 class="page-title">
	{if !empty($stringSearched)}
		Search Results for: <span>{$stringSearched}</span>
	{elseif !empty($user)}
		Items by: <span>{$user.User.realname|default:$user.User.userid}</span>
	{/if}
	</h1>
	{/strip}

	{if !empty($searchResult.items)}

		{assign_associative var="options" items=$searchResult.items toolbar=$searchResult.toolbar}
		{$view->element("list_items", $options)}

	{else}
		<p>No items found.</p>
	{/if}

	</div><!-- #content -->

</div><!-- #container -->

{$view->element('right_column')}