{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''}</label></div>
		
		

		<ul class="menuleft insidecol">
			
			<li><a href="{$html->url('/multimedia/view')}">{t}Add new item{/t}</a></li>
			<li><a href="javascript:void(0);" class="modalbutton" rel="{$html->url('/multimedia/multipleUpload')}" title="{t}Upload files{/t}">{t}Add many items{/t}</a></li>
					
		</ul>

{$view->element('export')}

	{if (!empty($view->action)) && $view->action eq "index"}
	<div class="insidecol publishingtree">
		{assign_associative var="options" treeParams=['action' => 'index']}
		{$view->element('tree', $options)}
	</div>
	{/if}

{$view->element('user_module_perms')}

</div>




