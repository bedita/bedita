{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''|escape}</label></div>


	<ul class="menuleft insidecol bordered">
		<li {if $view->action eq 'systemEvents'}class="on"{/if}><a href="{$html->url('/admin/systemEvents')}">{t}System Events{/t}</a></li>
		<li {if $view->action eq 'systemInfo'}class="on"{/if}><a href="{$html->url('/admin/systemInfo')}">{t}System Info{/t}</a></li>
		<li {if $view->action eq 'systemLogs'}class="on"{/if}><a href="{$html->url('/admin/systemLogs')}">{t}System Logs{/t}</a></li>
	</ul>

	<ul class="menuleft insidecol bordered">
		<li {if $view->action eq 'viewConfig'}class="on"{/if}><a href="{$html->url('/admin/viewConfig')}">{t}Configuration{/t}</a></li>
	</ul>

	<ul class="menuleft insidecol bordered">
		<li {if $view->action eq 'customproperties'}class="on"{/if}><a href="{$html->url('/admin/customproperties')}">{t}Custom properties{/t}</a></li>
		<li {if $view->action eq 'customRelations'}class="on"{/if}><a href="{$html->url('/admin/customRelations')}">{t}Custom relations{/t}</a></li>
		{* tpl relations still to develop *}
	</ul>
	
	<ul class="menuleft insidecol bordered">
		<li {if $view->action eq 'utility'}class="on"{/if}><a href="{$html->url('/admin/utility')}">{t}Utility{/t}</a></li>
	</ul>

	<ul class="menuleft insidecol bordered">
		<li {if $view->action eq 'coreModules'}class="on"{/if}><a href="{$html->url('/admin/coreModules')}">{t}Core Modules{/t}</a></li>
		<li {if $view->action eq 'pluginModules'}class="on"{/if}><a href="{$html->url('/admin/pluginModules')}">{t}Plugin Modules{/t}</a></li>
		<li {if $view->action eq 'sortModules'}class="on"{/if}><a href="{$html->url('/admin/sortModules')}">{t}Sort Modules{/t}</a></li>
		<li {if $view->action eq 'addons'}class="on"{/if}><a href="{$html->url('/admin/addons')}">{t}Addons{/t}</a></li>
	</ul>

	<ul class="menuleft insidecol bordered">
		<li {if $view->action eq 'emailInfo'}class="on"{/if}><a href="{$html->url('/admin/emailInfo')}">{t}Mail Queue{/t}</a></li>
		<li {if $view->action eq 'emailLogs'}class="on"{/if}><a href="{$html->url('/admin/emailLogs')}">{t}Mail Logs{/t}</a></li>
	</ul>
		
	{$view->element('user_module_perms')}

</div>