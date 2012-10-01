{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>


	<ul class="menuleft insidecol">
		<li {if $view->action eq 'systemEvents'}class="on"{/if}><a href="{$this->Html->url('/admin/systemEvents')}">{t}System Events{/t}</a></li>
		<li {if $view->action eq 'systemInfo'}class="on"{/if}><a href="{$this->Html->url('/admin/systemInfo')}">{t}System Info{/t}</a></li>
		<li {if $view->action eq 'systemLogs'}class="on"{/if}><a href="{$this->Html->url('/admin/systemLogs')}">{t}System Logs{/t}</a></li>
	</ul>

	<ul class="menuleft insidecol">
		<li {if $view->action eq 'viewConfig'}class="on"{/if}><a href="{$this->Html->url('/admin/viewConfig')}">{t}Configuration{/t}</a></li>
	</ul>
	
	<ul class="menuleft insidecol">
		<li {if $view->action eq 'emailInfo'}class="on"{/if}><a href="{$this->Html->url('/admin/emailInfo')}">{t}Mail Queue{/t}</a></li>
		<li {if $view->action eq 'emailLogs'}class="on"{/if}><a href="{$this->Html->url('/admin/emailLogs')}">{t}Mail Logs{/t}</a></li>
	</ul>

	<ul class="menuleft insidecol">
		<li {if $view->action eq 'customproperties'}class="on"{/if}><a href="{$this->Html->url('/admin/customproperties')}">{t}Custom properties{/t}</a></li>
		{bedev}<li {if $view->action eq 'customrelations'}class="on"{/if}><a href="{$this->Html->url('/admin/relations')}">{t}Custom relations{/t}</a></li>{/bedev}{* tpl relations still to develop *}
	</ul>

	<ul class="menuleft insidecol">
		<li {if $view->action eq 'coreModules'}class="on"{/if}><a href="{$this->Html->url('/admin/coreModules')}">{t}Core Modules{/t}</a></li>
		<li {if $view->action eq 'pluginModules'}class="on"{/if}><a href="{$this->Html->url('/admin/pluginModules')}">{t}Plugin Modules{/t}</a></li>
		<li {if $view->action eq 'sortModules'}class="on"{/if}><a href="{$this->Html->url('/admin/sortModules')}">{t}Sort Modules{/t}</a></li>
		<li {if $view->action eq 'addons'}class="on"{/if}><a href="{$this->Html->url('/admin/addons')}">{t}Addons{/t}</a></li>
	</ul>

	<ul class="menuleft insidecol">
		{bedev}<li {if $view->action eq 'importData'}class="on"{/if}><a href="{$this->Html->url('/admin/importData')}">{t}Import Data{/t}</a></li>{/bedev}
		<li {if $view->action eq 'utility'}class="on"{/if}><a href="{$this->Html->url('/admin/utility')}">{t}Utility{/t}</a></li>
	</ul>
	
	{$view->element('user_module_perms')}

</div>