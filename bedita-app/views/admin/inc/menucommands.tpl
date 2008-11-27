{*
Template incluso.
Menu comandi, seconda colonna da SX valido per tutte le pagine del controller.
*}




<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	  	   <label class="{$moduleName}" rel="{$html->url('/')}{$currentModule.path}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{include file="../common_inc/messages.tpl"}
	
	
	{if $module_modify eq '1'}
	<div class="insidecol">
		
		{if $method == "viewUser"}
		
			<input type="submit" id="submit" name="save" class="submit" 
			value="{if isset($user)}{t}Save{/t}{else}{t}create{/t}{/if}" />
		
		{elseif $method == "groups"}
		
			<input type="submit" name="save" class="submit" 
			value="{if isset($group)}{t}Modify{/t}{else}{t}create group{/t}{/if}" />
		
		
		{elseif $method == "systemInfo"}
		
			<form action="{$html->url('/admin/deleteEventLog')}" method="post">
			<input type="submit" value="{t}delete all{/t}"/>
			</form>
		
		
		{/if}
	</div>
	{/if}



</div>
	


	



