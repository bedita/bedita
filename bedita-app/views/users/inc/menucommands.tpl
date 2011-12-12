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

		{elseif $view->action == "viewGroup"}
		
			<input class="bemaincommands" type="button" name="save" onClick="$('#groupForm').submit()" 
			value="{if isset($group)}{t}save{/t}{else}{t}create group{/t}{/if}" />

			{if ($module_modify eq '1' && !empty($group))}
				<input class="bemaincommands"  type="button" name="deleteGroup" value="{t}Delete{/t}" 
				onclick="javascript:delGroupDialog('{$group.Group.name}',{$group.Group.id});"/>
			{/if}
		
		{/if}
	</div>
	{/if}

</div>