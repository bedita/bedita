{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	   <div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
		{include file="../common_inc/messages.tpl"}

		<ul class="menuleft insidecol">
			
			<li>{$tr->link('Add new item', '/multimedia/view')}</li>	
					
		</ul>

{include file="../common_inc/export.tpl"}

{include file="../common_inc/user_module_perms.tpl"}

</div>




