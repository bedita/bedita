{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/')}{$currentModule.path}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	
	
	{if !empty($method) && $method != "index"}
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
	</div>
	
	{/if}

	{assign var='cat' value=$categorySearched|default:''}

	{if $method == "index"}
		<ul class="menuleft insidecol">
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