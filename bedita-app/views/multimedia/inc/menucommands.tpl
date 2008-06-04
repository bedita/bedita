{assign var='method' value=$method|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

	
			

<div class="secondacolonna {if $fixed}fixed{/if}">
	
	<div class="modules">
	   <label class="multimedia" rel="{$html->url('/multimedia')}">{t}Multimedia{/t}</label>
	</div> 

{include file="../messages.tpl"}

</div>





