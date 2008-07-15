{if !empty($contents.items)}
	
	<ul style="margin-top:10px; display: block;" id="areacontent" class="bordered">
		{foreach from=$contents.items item="c"}
		<li>
			<input type="text" class="priority" 
			style="text-align:right; margin-left: -30px; margin-right:10px; width:35px; float:left; background-color:transparent" 
			name="" value="{$c.priority}" size="3" maxlength="3"/>
	
			<span class="listrecent {$c.module}" style="margin-left:0px">&nbsp;&nbsp;</span>
			<a title="{$c.created}" href="{$html->url('/')}{$c.module}/view/{$c.id}">{$c.title}</a>
			
		</li>
		{/foreach}
	</ul>		
	
	<a href="#" class="graced" style="font-size:3em">‹ ›</a>
{else}
	{t}no contents{/t}
{/if}