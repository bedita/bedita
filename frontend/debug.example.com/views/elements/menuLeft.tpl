

<div class="menuleft">	

	<ul>
		{section name="m" loop=$menu}
			
			<li>
				<a title="{$menu[m].title}" href="{if empty($menu[m].objects)}{$html->url('/')}{$menu[m].nickname}{else}#{/if}">{$menu[m].title}</a>
				{if !empty($menu[m].objects)}
				{assign var=contents value=$menu[m].objects}
				<ul style="display:none;">
				{section name="c" loop=$contents}
					<li><a title="{$contents[c].title}" href="{$html->url('/')}{$contents[c].nickname}">{$contents[c].title}</a></li>
				{/section}
				</ul>
				{/if}
			</li>
		
		{/section}
	</ul>

</div>

