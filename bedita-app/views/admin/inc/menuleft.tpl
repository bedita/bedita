{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>


	<ul class="menuleft insidecol">
		<li {if $view->action eq 'systemEvents'}class="on"{/if}>{$tr->link('System Events', '/admin/systemEvents')}</li>
		<li {if $view->action eq 'systemInfo'}class="on"{/if}>{$tr->link('System Info', '/admin/systemInfo')}</li>
		<li {if $view->action eq 'systemLogs'}class="on"{/if}>{$tr->link('System Logs', '/admin/systemLogs')}</li>
	</ul>

	<ul class="menuleft insidecol">
		<li {if $view->action eq 'viewConfig'}class="on"{/if}>{$tr->link('Configuration', '/admin/viewConfig')}</li>
	</ul>
	
	<ul class="menuleft insidecol">
		<li {if $view->action eq 'emailInfo'}class="on"{/if}>{$tr->link('Mail Queue', '/admin/emailInfo')}</li>
		<li {if $view->action eq 'emailLogs'}class="on"{/if}>{$tr->link('Mail Logs', '/admin/emailLogs')}</li>
	</ul>

	<ul class="menuleft insidecol">
		<li {if $view->action eq 'customproperties'}class="on"{/if}>{$tr->link('Custom properties', '/admin/customproperties')}</li>
		{bedev}<li {if $view->action eq 'customrelations'}class="on"{/if}>{$tr->link('Custom relations', '/admin/relations')}</li>{/bedev}
	</ul>

	<ul class="menuleft insidecol">
		<li {if $view->action eq 'pluginModules'}class="on"{/if}>{$tr->link('Plugin Modules', '/admin/pluginModules')}</li>
		<li {if $view->action eq 'addons'}class="on"{/if}>{$tr->link('Addons', '/admin/addons')}</li>
	</ul>

	<ul class="menuleft insidecol">
		{bedev}<li {if $view->action eq 'importData'}class="on"{/if}>{$tr->link('Import Data', '/admin/importData')}</li>{/bedev}
		<li {if $view->action eq 'utility'}class="on"{/if}>{$tr->link('Utility', '/admin/utility')}</li>
	</ul>
	
	{$view->element('user_module_perms')}

</div>