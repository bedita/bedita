{assign var='method' value=$view->action|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($view->action) && $view->action != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if !empty($view->action) && $view->action != "index"}
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
	</div>
	
		{$view->element('prevnext')}
	
	{/if}

	{assign var='cat' value=$categorySearched|default:''}

	{if $view->action == "index"}
		<ul class="menuleft insidecol catselector">
			<li><a href="javascript:void(0)" onClick="$('#mediatypes').slideToggle();">{t}Select by type{/t}</a></li>
				<ul id="mediatypes" {if empty($categorySearched)}style="display:none"{/if}>
					
					{foreach from=$conf->mediaTypes item="media_type"}
					<li class="ico_{$media_type} {if $cat==$media_type}on{/if}" rel="{$html->url('/multimedia')}/index/category:{$media_type}">
						{$media_type}
					</li>
					{/foreach}
					<li class="ico_all" rel="{$html->url('/multimedia')}">
						All
					</li>
				
				</ul>
		</ul>
	{/if}	



</div>