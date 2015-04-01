{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">
		
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:''|escape}</label></div>
			
	<ul class="menuleft insidecol bordered">
		<li><a href="{$html->url('/comments')}">{t}Comments{/t}</a></li>					
	</ul>

	{if ($view->action == "view")}
	<ul class="menuleft insidecol  bordered">
		<li><a href="{$html->url('/comments/index')}/ip_created:{$object.ip_created|default:''}">{t}more from this IP{/t}</a></li>
		<li><a href="{$html->url('/comments/index')}/email:{$object.email|default:''}">{t}more from this email{/t}</a></li>
		<li><a href="{$html->url('/comments/index')}/comment_object_id:{$object.object_id|default:''}">{t}more on this content{/t}</a></li>
	</ul>
	{/if}
	
	{$view->element('user_module_perms')}
	
</div>