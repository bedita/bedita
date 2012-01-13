{strip}

{$view->element('header')}

<div class="main">

	<div class="content-main">
		<div class="textC">
		<h1 style="margin-bottom:30px;">{t}Category{/t} "{$category.label}"</h1>
		{$category.name}
		</div>
	</div>
</div>	
		
{$view->element('footer')}

{/strip}