{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{$html->css('ui.datepicker', null, ['inline' => false])}
{$html->script('libs/jquery/ui/jquery.ui.datepicker.min')}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($view->action) && $view->action != "index" && $view->action != "categories"}
		{assign var="back" value=$session->read("backFromView")|escape}
	{else}
		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 

{if empty($categories)}
	{if !empty($view->action) && $view->action == "calendar"}	
		<form id="event-calendar-form" method="post" action="{$html->url('/events/calendar')}">
			{$beForm->csrf()}

			<label>{t}since{/t}:</label>
			<fieldset>
			    <input maxlength="10" type="text" class="start_date dateinput" name="start_date" data-date-iso="{$startDay|date_format:'%Y-%m-%d'|default:{$smarty.now|date_format:'%Y-%m-%d'}}"
			    value="{$startDay|date_format:$conf->datePattern|default:{$smarty.now|date_format:$conf->datePattern}}"/>
			</fieldset>

		    <label>{t}to{/t}:</label>
		    <fieldset>
    			<input maxlength="10" type="text" class="end_date dateinput" name="end_date" data-date-iso="{$endDay|date_format:'%Y-%m-%d'|default:{$smarty.now|date_format:'%Y-%m-%d'}}"
    			value="{$endDay|date_format:$conf->datePattern|default:''}"/>
    			<br>
		    </fieldset>

			<script>
				$.datepicker.setDefaults({
					speed: 'fast', 
					showOn: 'both',
					closeAtTop: false, 
					buttonImageOnly: true, 
				    buttonImage: '{$html->webroot}img/iconCalendar.gif', 
				    buttonText: '{t}Open Calendar{/t}',
				    dateFormat: '{$conf->dateFormatValidation|replace:'yyyy':'yy'}',
					firstDay: 1,
					nextText: "&rsaquo;&rsaquo;",
					prevText: "&lsaquo;&lsaquo;",
				    beforeShow: customRange
				}, $.datepicker.regional['{$currLang}']);
				
				$("input.dateinput").datepicker();
			</script>

			<input type='hidden' name="toolbarStartDate" class="js-toolbarStartDate">
			<input type='hidden' name="toolbarEndDate" class="js-toolbarEndDate">
			<input type="submit" value="{t}go{/t}">

			{bedev}
				<hr />
				export ics
				<br>
				export csv
			{/bedev}
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