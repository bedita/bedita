{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{$view->set("method", $method)}
<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	{$view->element('messages')}
	
	{if $module_modify eq '1'}{/if}
	
	<ul class="menuleft insidecol">
		<li {if $method eq "index"}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.path}/index">{t}Questionnaires list{/t}</a>
		</li>
		<li {if $method eq "view" && (empty($object))}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.path}/viewQuestionnaire">{t}Create new{/t}</a>
		</li>
	</ul>
	
	<ul class="menuleft insidecol">
				
		<li {if $method eq "indexQuestions"}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.path}/indexQuestions">{t}Questions list{/t}</a>
		</li>
		<li {if $method eq "viewQuestion" && (empty($object))}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.path}/viewQuestion">{t}Create new question{/t}</a>
		</li>
	
	</ul>


{$view->element('export')}


	{if (!empty($method)) && $method eq "index"}
	<div class="insidecol publishingtree">
		
		{$view->element('tree')}
	
	</div>
	{/if}
	

{$view->element('user_module_perms')}

</div>