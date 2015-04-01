{*
Template incluso.
Menu comandi, seconda colonna da SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	{if !empty($view->action) && $view->action == "viewGroup"}
		{assign_concat var="back" 1=$html->url('/') 2="users/groups"}
	{else}
		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}
	{/if}
	
	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
 	
	
	{if $module_modify eq '1'}
	<div class="insidecol">

		{if $view->action == "viewUser" && !$userDeleted|default:false}
		
			<input class="bemaincommands" type="button" name="saveUser" value="{if isset($userdetail)}{t}Save{/t}{else}{t}create{/t}{/if}" />

		{elseif $view->action == "viewGroup"}

			<input class="bemaincommands" 
			{if (!empty($group) && $group.Group.immutable == 1)}disabled=disabled{/if}
			type="button" name="saveGroup" value="{if isset($group)}{t}save{/t}{else}{t}create group{/t}{/if}" />

			{if ($module_modify eq '1' && !empty($group))}
				<input class="bemaincommands" 
				{if ($group.Group.immutable == 1)}disabled=disabled{/if}
				type="button" name="deleteGroup" value="{t}Delete{/t}"/>
			{/if}
			
		{/if}
	</div>
	{/if}

</div>