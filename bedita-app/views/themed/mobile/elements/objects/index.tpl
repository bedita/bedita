<div data-role="page">

	<div data-role="header">
		<h1>{$currentModule.name|capitalize}</h1>
		<a href="{$html->url('/')}" data-icon="arrow-l" data-iconpos="notext">{t}Back{/t}</a>
		<a href="{$html->url('/')}{$currentModule.url}/view" data-icon="plus" data-iconpos="notext">{t}Add{/t}</a>
	</div><!-- /header -->

	<div data-role="content">
  {strip}
		{$view->element('list_objects')}
  {/strip}
	</div><!-- /content -->
	{$view->element('footer')}
</div><!-- /page -->