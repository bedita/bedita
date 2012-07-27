{* @todo: This array must be moved in Controller (check if module's mobile-views exist) *}
{$mobileModuleList = ['documents']}

<div data-role="page">

	<div data-role="header">
		<h1>{t}Dashboard{/t}</h1>
	</div><!-- /header -->

	<div data-role="content">

	{strip}
		{if !empty($moduleList)}
		<ul data-role="listview" data-inset="true" data-filter="true">
		{foreach from=$moduleList key=k item=mod}
		{if (in_array($mod.name,$mobileModuleList)) && ($mod.status == 'on')}
			{assign_concat var='link' 1=$html->url('/') 2=$mod.url}
			<li><a href="{$link}" title="{t}{$mod.label}{/t}" class="{$mod.name|default:''} {if ($mod.name == $moduleName|default:'')} on{/if}">{t}{$mod.label}{/t}</a></li>
		{/if}
		{/foreach}
		</ul>
		{/if}
	{/strip}

	</div><!-- /content -->
	{$view->element('footer')}
</div><!-- /page -->