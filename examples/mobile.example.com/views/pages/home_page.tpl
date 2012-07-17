<div data-role="page">

	{$view->element('header')}

	<div data-role="content">

		{if !empty($section.contentRequested)}

			{$view->element('content')}

		{else}

			{if !empty($section.childContents)}

				<ul data-role="listview" data-theme="d">
				{foreach from=$section.childContents item="child"}
					<li>
						<a href="{$html->url($child.canonicalPath)}">
							{if !empty($child.relations.attach)}
								<img class="ui-li-thumb" src="{$beEmbedMedia->object($child.relations.attach.0, 
										[
											"presentation" => "thumb",
											"width" => 400,
											"height" => 400,
											"mode" => "resize",
											"modeparam" => "fill",
											"bgcolor" => "#000",
											"URLonly" => true
										]
									)}" />
							{/if}
							<h1>{$child.title}</h1>
							<p class="list_description">{$child.description}</p>
						</a>
					</li>
				{/foreach}
				</ul>

			{/if}

		{/if}

	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->

