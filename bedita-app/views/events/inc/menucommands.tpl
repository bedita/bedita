{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 

	{if empty($categories)}

	{if !empty($view->action) && $view->action == "calendar"}

			
		<form id="calendar_from" style="padding:10px" name="calendar_from" method="get">
				<label>{t}cerca eventi a partire da:{/t}</label> 
				<fieldset style="margin:10px 0 10px 0;  border-bottom:1px solid #999; padding-bottom:10px; display:block">
					{$time=$html->params.pass.1|default:$smarty.now|date_format:'%s'}
					{html_select_date field_order="DMY" field_separator="" time=$time end_year="+1" display_days=true}
				</fieldset>
				<input type="submit" value="{t}vai{/t}">
		</form>

	{elseif !empty($view->action) && $view->action != "index" && $view->action != "categories" && $view->action != "calendar"}
		<div class="insidecol">
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
			<input class="bemaincommands" type="button" value=" {t}Publish{/t} " name="publish" id="publishBEObject" />
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		</div>
		
		{$view->element('prevnext')}
		
	{/if}

	{/if}

</div>

