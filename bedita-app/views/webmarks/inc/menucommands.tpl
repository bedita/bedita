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
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
	
		<div class="listobjnav">
			<a title="next" href="#">
				‹
			</a>
	
			<a title="next" href="#">
				›
			</a> 
		</div>
		
	</div>
	
	{elseif $method == "index"}
	

		{if !empty($categories)}
		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#groups').slideToggle();">{t}Select by category{/t}</a></li>
				
				<ul id="groups" {if (empty($categorySearched))}style="display:none"{/if}>
					{foreach key=val item=cat from=$categories}
					<li {if (($categorySearched|default:'')==$cat.id)}class="on"{/if}><a href="{$html->url('/')}{$currentModule.path}/index/category:{$cat.id}">{$cat.label}</a></li>
					{/foreach}
				</ul>
		</ul>
		{/if}
	{/if}

</div>
