{$view->element('header')}

<article>
		
		<section class="mod3 detail" style="margin-right:0px;">
			
			<figure>
				{assign_associative var="params" width=630 height=273 mode="crop" upscale=false URLonly=false}					
				{$beEmbedMedia->object($section.currentContent.relations.attach[0],$params)}
			</figure>
			
			<h1>{$section.currentContent.title}</h1>
			{$section.currentContent.description}
			{$section.currentContent.abstract}
			{$section.currentContent.body}
			
			
		</section>

</article>

{$view->element('footer')}