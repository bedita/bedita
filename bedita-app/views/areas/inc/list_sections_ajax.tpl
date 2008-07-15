{if !empty($sections.items)}

	<ul style="margin-top:10px; display: block;" id="areasections" class="bordered">
		{foreach from=$sections.items item=s}
		<li>
			<input type="text" class="priority" 
			style="text-align:right; margin-left: -30px; margin-right:10px; width:35px; float:left; background-color:transparent" 
			name="" value="{$s.priority}" size="3" maxlength="3"/>
	
			<a title="{$s.created}" href="{$html->url('/')}/areas/index/id:{$s.id}">{$s.title}</a>
			
		</li>
		{/foreach}
	</ul>		
	
	<a href="#" class="graced" style="font-size:3em">‹ ›</a>

{else}
	{t}no sections{/t}
{/if}