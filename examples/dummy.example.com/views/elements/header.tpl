<header class="mod1">
	<a title="Homepage" rel="Homepage" href="{$html->url('/')}">
		<img src="{$html->url('/')}img/ieb_z.png" />
	</a>
	
	<nav class="mod1">
		
		<ul>
			{foreach from=$menu item=item}
			<li>
				<a {if $item.nickname == $section.nickname}class="on"{/if} rel="{$item.title}" title="{$item.title}" href="{$item.objects[0].canonicalPath}">{$item.title}</a>
					<!-- eventuali sottodocumenti, dal 2 in poi -->
					{if !empty($item.objects[1])}
					<ul>
					
					{foreach from=$item.objects item=subsects}
						<li><a {if $subsects.nickname == $section.currentContent.nickname}class="on"{/if}>{$subsects.title}</a></li>
					{/foreach}
					</ul>
					{/if}
				</li>
			{/foreach}
		</ul>
	</nav>

</header>