<div data-role="page">

	<div data-role="header">
		<h1>Dashboard</h1>
	</div><!-- /header -->

	<div data-role="content">

  {strip}
		{$beTree->designBranchMobile($tree)}
  {/strip}

	</div><!-- /content -->
	{$view->element('footer')}
</div><!-- /page -->