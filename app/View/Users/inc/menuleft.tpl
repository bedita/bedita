{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="{$this->Html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
	<ul class="menuleft insidecol">
		<li {if $view->action eq 'index'}class="on"{/if}>{$this->Tr->link('Users', '/users/')}</li>
		<li {if $view->action eq 'viewUser' && (empty($userdetail))}class="on"{/if}>{$this->Tr->link('New user', '/users/viewUser')}</li>
		<li {if $view->action eq 'groups' or !empty($group)}class="on"{/if}>{$this->Tr->link('Groups', '/users/groups')}</li>
		<li {if $view->action eq 'viewGroup' && empty($group)}class="on"{/if}>{$this->Tr->link('New group', '/users/viewGroup/')}</li>
		
	</ul>

	{$view->element('user_module_perms')}

</div>