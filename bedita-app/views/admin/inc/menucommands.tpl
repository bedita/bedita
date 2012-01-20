{*
Template incluso.
Menu comandi, seconda colonna da SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}


	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
 	{$view->element('messages')}
	
	{if $module_modify eq '1'}
	<div class="insidecol">



	
		{if $view->action == "viewUser"}
		
			<input class="bemaincommands" type="button" name="save" onClick="$('#userForm').submit()" 
			value="{if isset($userdetail)}{t}Save{/t}{else}{t}create{/t}{/if}" />

		{elseif $view->action == "groups"}
		
			<input class="bemaincommands" type="button" name="save" onClick="$('#groupForm').submit()" 
			value="{if isset($group)}{t}Modify{/t}{else}{t}create group{/t}{/if}" />
		
		
		{elseif $view->action == "systemEvents"}
		
			<form action="{$html->url('/admin/deleteEventLog')}" method="post">
			<input type="submit" value="{t}delete all{/t}"/>
			</form>

		{elseif $view->action == "emailInfo"}

			<form action="{$html->url('/admin/deleteAllMailUnsent')}" method="post">
			<input type="submit" value="{t}delete all{/t}"/>
			<br/>[{t}Not newsletter mail{/t}]
			</form>

		{elseif $view->action == "emailLogs"}

			<form action="{$html->url('/admin/deleteAllMailLogs')}" method="post">
			<input type="submit" value="{t}delete all{/t}"/>
			</form>

		{elseif $view->action == "viewConfig"}

			<input class="bemaincommands" type="button" name="save" onClick="$('#configForm').submit()"
			value="{t}save{/t}" />
		
		
		{/if}
	</div>
	{/if}



</div>
	


	



