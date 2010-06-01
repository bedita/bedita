{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{$view->set("method", $method)}
<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	{$view->element("messages")}
	
		{assign_concat var="modulePath" 0="/" 1=$currentModule.url}
		<ul class="menuleft insidecol">
		<li {if $method eq 'index'}class="on"{/if}>{$tr->link($currentModule.label, $modulePath)}</li>
		{if $module_modify eq '1'}
		<li><a href="{$html->url($modulePath)}/view">{t}Create new sample object{/t}</a></li>
		{/if}
	</ul>

{$view->element("export")}

{if (!empty($method)) && $method eq "index"}

		<div class="insidecol publishingtree">
			
			{$view->element("tree")}
		
		</div>

{/if}

{$view->element("previews")}

{$view->element("user_module_perms")}

</div>
