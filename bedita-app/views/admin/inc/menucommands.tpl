{*
Template incluso.
Menu comandi, seconda colonna da SX valido per tutte le pagine del controller.
*}




<div class="secondacolonna {if $fixed}fixed{/if}">
	
	<div class="modules">
	   <label class="admin" rel="{$html->url('/admin')}">{t}Admin{/t}</label>
	</div> 
	
	{include file="../messages.tpl"}
	
	
	{if $module_modify eq '1'}
	<div class="insidecol">
		
		{if $method == "viewUser"}
		
			<input type="submit" id="submit" name="save" class="submit" 
			value="{if isset($user)}{t}Save{/t}{else}{t}Create{/t}{/if}" />
		
		{elseif $method == "groups"}
		
			<input type="submit" name="save" class="submit" 
			value="{if isset($group)}{t}Modify{/t}{else}{t}Create group{/t}{/if}" />
		
		
		{elseif $method == "systemInfo"}
		
			<form action="{$html->url('/admin/deleteEventLog')}" method="post">
			<input type="submit" value="{t}Delete all events{/t}"/>
			</form>
		
		
		{/if}
	</div>
	{/if}



</div>
	


	



