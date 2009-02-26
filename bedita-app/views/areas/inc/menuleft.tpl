{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{assign var='method' value=$method|default:'index'}

<div class="primacolonna">
		
	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>
		
	
	{if $module_modify eq '1'}
	<ul class="menuleft insidecol">
		<li id="newArea" {if $method eq 'viewArea'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.path}/viewArea">
				{t}new publishing{/t}
			</a>
		</li>
		<li id="newSection" {if $method eq 'viewSection'}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.path}/viewSection">
				{t}new section{/t}
			</a>
		</li>
	</ul>
	{/if}
	
	<div class="insidecol publishingtree">	
			{if !empty($tree)}{$beTree->view($tree)}{/if}
	</div>

	<div style="margin-top:40px;">
	{include file="../common_inc/messages.tpl"}
	</div>
	

	{include file="../common_inc/user_module_perms.tpl"}
	
</div>





