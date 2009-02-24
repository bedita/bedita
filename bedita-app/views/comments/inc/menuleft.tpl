{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
<div class="primacolonna">
		
	
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->version}</label></div>
		
		
	<ul class="menuleft insidecol">
		<li><a href="{$html->url('/comments')}">{t}Comments{/t}</a></li>					
	</ul>


	{include file="../common_inc/user_module_perms.tpl"}
	

</div>

