<div data-role="page">

	{$view->element('header')}

	<div data-role="content">

		<ul data-role="listview" data-divider-theme="d" data-inset="true">
			<li data-role="list-divider" role="heading">
				{t}You have searched{/t} "{$stringSearched}"
				<span class="ui-li-count">{$searchResult.toolbar.size}</span>
			</li>
			{if !empty($searchResult.items)}
				{foreach from=$searchResult.items item="object"}
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
			{else}
				<li>{t}No items found.{/t}</li>
			{/if}		
		</ul>
	</div><!-- /content -->

	{$view->element('footer')}

</div><!-- /page -->