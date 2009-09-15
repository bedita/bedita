{strip}

{if isset($conf->staging) && ($conf->staging)}
{$view->element('staging_toolbar')}
{/if}

{$view->element('header')}

<div class="main">

	<div class="content-main">

	{$view->element('menu')}

{if (!empty($section.currentContent))}

	{if $section.currentContent.object_type == "Gallery"}

		{$view->element('gallery')}
		
	{else}
	
		{if (!empty($section.currentContent.abstract)) or (isset($section.currentContent.relations))}
		
			{assign var="class" value="twocols"}
			
		{/if}
	
	
	<div class="textC {$class|default:''}">
		
		<h1>{$section.currentContent.title}</h1>

		<h3>{$section.currentContent.description}</h3>

		<p class="testo">{$section.currentContent.body}</p>
		
		{assign_associative var="options" object=$section.currentContent showForm=true}
		{$view->element('show_comments', $options)}

	</div>
		
	<div class="abstract {$class|default:''}">

			{$section.currentContent.abstract|default:''}
			{$view->element('related')}
			
	</div>
		
	{/if}	
		
	</div>
	
{/if}
	
</div>	
		
{$view->element('footer')}

{/strip}