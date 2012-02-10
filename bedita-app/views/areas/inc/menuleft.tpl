{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$view->action|default:'index'}

<div class="primacolonna">
		
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
	
	{if $module_modify eq '1'}
	<ul class="menuleft insidecol">
		<li id="newArea" {if $method eq 'viewArea'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/viewArea">
				{t}new publication{/t}
			</a>
		</li>
		<li id="newSection" {if $method eq 'viewSection'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/viewSection">
				{t}new section{/t}
			</a>
		</li>
	</ul>
	{/if}
	
	{if ($method != "viewArea" && $method != "viewSection")}
	<div class="insidecol publishingtree">	
			{if !empty($tree)}
			
			{$view->element('tree')}
			
			{/if}
	</div>
	{/if}
	
	<div style="margin-top:40px;">
	
	</div>
	

	{$view->element('user_module_perms')}
	
</div>





