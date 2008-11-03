{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">
	
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">BEdita 3.0</label></div>
		
	<ul class="menuleft insidecol">
		<li {if $method eq "index"}class="on"{/if}>{$tr->link('Users', '/admin')}</li>
		<li {if $method eq 'viewUser'}class="on"{/if}>{$tr->link('New user', '/admin/viewUser')}</li>
		<li {if $method eq 'groups'}class="on"{/if}>{$tr->link('User groups', '/admin/groups')}</li>
	</ul>
	
	<ul class="menuleft insidecol">
		<li {if $method eq 'customproperties'}class="on"{/if}>{$tr->link('Custom properties', '/admin/customproperties')}</li>
	</ul>
	
	
	<ul class="menuleft insidecol">
		<li {if $method eq 'systemInfo'}class="on"{/if}>{$tr->link('System Info', '/admin/systemInfo')}</li>						
	</ul>



<div id="handlerChangeAlert"></div>

</div>