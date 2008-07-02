{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
			

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="multimedia" rel="{$html->url('/multimedia')}">{t}Multimedia{/t}</label>
	</div> 

	{include file="../common_inc/messages.tpl"}
	
</div>





