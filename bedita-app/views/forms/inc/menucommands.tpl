{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	<div class="modules">
		<label class="{$moduleName}" rel="{$session->read("backFromView")}">{t}{$currentModule.label}{/t}</label>
	</div> 


	
	{if !empty($method) && $method != "index" && $method != "indexQuestions"}
	
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
	
		{include file="../common_inc/prevnext.tpl"}

		
	</div>
	
	{elseif $method == "indexQuestions"}
	

		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#groups').slideToggle();">{t}Select by category{/t}</a></li>
				
				<ul id="groups" style="margin-top:10px;">
					<li><a href="">matematica</a></li>
					<li><a href="">fisica</a></li>
					<li><a href="">geologia</a></li>
					<li><a href="">chimica</a></li>
					<li><a href="">francese</a></li>
					<li><a href="">italiano</a></li>
				</ul>
		</ul>

	
	{/if}

</div>
