{*
Template incluso.
Menu comandi, seconda colonna da SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}


	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
		
	{if $module_modify eq '1'}
	<div class="insidecol">

	
		{if $view->action == "systemEvents"}
			{$view->Form->create(null, ['action' => 'deleteEventLog'])}
			<input type="submit" value="{t}delete all{/t}"/>
			{$view->Form->end()}

		{elseif $view->action == "systemLogs"}
		
			{$view->Form->create(null, ['action' => 'emptySystemLog'])}
			<input type="submit" value="{t}empty all{/t}"/>
			{$view->Form->end()}

		{elseif $view->action == "emailInfo"}

			{$view->Form->create(null, ['action' => 'deleteAllMailUnsent'])}
			<input type="submit" value="{t}delete all{/t}*"/>
				<p style="margin:10px; padding:10px; border-top:1px solid gray; border-bottom:1px solid gray; font-style:italic">
					* {t}does not affect email newsletter{/t}
				</p>
			{$view->Form->end()}
		
		{elseif $view->action == "emailLogs"}

			{$view->Form->create(null, ['action' => 'deleteAllMailLogs'])}
			<input type="submit" value="{t}delete all{/t}"/>
			{$view->Form->end()}

		{elseif $view->action == "viewConfig"}

			<input class="bemaincommands" type="button" name="save" onClick="$('#configForm').submit()"
			value="{t}save{/t}" />

		{elseif $view->action == "sortModules"}

			<input class="bemaincommands" type="button" name="save" onClick="$('#sortModules').submit()" 
			value="{t}save{/t}" />

		{elseif $view->action == "customRelations"}

			<!--
			<input class="bemaincommands" type="button" name="save" onClick="$('#customRelations').submit()" 
			value="{t}save all{/t}" />
			-->
		{/if}

	</div>
	{/if}



</div>
	


	



