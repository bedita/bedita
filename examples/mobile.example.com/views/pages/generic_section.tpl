<div data-role="page">

	{$view->element('header')}

	{if !empty($homeImage)}
		{assign_associative var="params" width=900 height=400 mode="croponly"}
		{assign_associative var="htmlAttr" width="100%"}
		{$beEmbedMedia->object($homeImage,$params,$htmlAttr)}
	{elseif !empty($section.contentRequested)}
		{$gmaps->staticMap($section.currentContent)}	
	{/if}

	<div data-role="content">

		{if !empty($section.contentRequested)}

			{$view->element('content')}

		{elseif !empty($section.childContents)}

			

			<ul data-role="listview">
			{foreach from=$section.childContents item="child"}
				<li>
					<a href="{$html->url($child.canonicalPath)}">
						<h1>{$child.title}</h1>
						<p>{$child.description}</p>
					</a>
				</li>
			{/foreach}
			</ul>

		{/if}

	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->