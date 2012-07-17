<div data-role="page">

	{$view->element('header')}

	<div data-role="content">

		{if !empty($tag.items)}
			<ul data-role="listview" data-theme="d" data-divider-theme="d" data-inset="true">
				<li data-role="list-divider" role="heading">
					{t}Objects tagged by{/t} "{$tag.label}"
					<span class="ui-li-count">{$tag.toolbar.size}</span>
				</li>
				{foreach from=$tag.items item="object"}
					<li>
						<a href="{$html->url('/')}{$object.nickname}">
							{if !empty($object.relations.attach)}
								<img class="ui-li-thumb" src="{$beEmbedMedia->object($object.relations.attach.0, 
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
							<h1>{$object.title}</h1>
							<p class="list_description">{$object.description}</p>
						</a>
					</li>
				{/foreach}
			</ul>
		{/if}

	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->