{$view->element('header')}
	
	<article>
		
		<section class="mod3" id="evidence" style="margin-right:0px;">
						
			<figure>
				{assign_associative var="params" width=630 height=273 mode="crop" upscale=false URLonly=false}					
				{$beEmbedMedia->object($intro[0].relations.attach[0],$params)}
			</figure>
			
			<h1>{$intro[0].description}</h1>
			
			{$intro[0].body}
			
		</section>
		
	</article>
	
{$view->element('footer')}