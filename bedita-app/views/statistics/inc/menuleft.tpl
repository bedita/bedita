{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="primacolonna">

	<div class="modules"><label class="bedita" rel="{$html->url('/')}">{$conf->projectName|default:$conf->userVersion}</label></div>

	{include file="../common_inc/messages.tpl"}


	{if (!empty($method)) && $method eq "index"}
	<div class="insidecol publishingtree">

		{include file="../common_inc/tree.tpl"}
	
	</div>
	{/if}


{include file="../common_inc/user_module_perms.tpl"}

</div>